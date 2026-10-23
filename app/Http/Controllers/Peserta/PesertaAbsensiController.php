<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\PesertaFace;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PesertaAbsensiController extends Controller
{
    public function __construct(
        protected FaceRecognitionService $faceRecognitionService
    ) {
    }

    /**
     * Mengambil peserta yang sedang login.
     */
    private function pesertaLogin()
    {
        return Auth::guard('peserta')->user();
    }

    /**
     * Halaman absensi khusus peserta.
     */
    public function index()
    {
        $peserta = $this->pesertaLogin();

        abort_unless($peserta, 403);

        // Ambil pendaftaran aktif milik peserta yang login.
        $pendaftarans = Pendaftaran::with([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])
            ->where('peserta_id', $peserta->id)
            ->where('status', 'aktif')
            ->get();

        $programIds = $pendaftarans
            ->pluck('program_pelatihan_id')
            ->unique()
            ->values();

        $kelasIds = $pendaftarans
            ->pluck('kelas_id')
            ->unique()
            ->values();

        // Jadwal hanya untuk program dan kelas peserta sendiri.
        $jadwals = Jadwal::with([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])
            ->whereDate('tanggal', today())
            ->whereIn('program_pelatihan_id', $programIds)
            ->whereHas('kelas', function ($query) use ($kelasIds) {
                $query->whereIn('kelas.id', $kelasIds);
            })
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($jadwal) use ($pendaftarans) {
                $pendaftaran = $pendaftarans->first(
                    fn ($item) =>
                        (int) $item->program_pelatihan_id ===
                            (int) $jadwal->program_pelatihan_id
                        && $jadwal->kelas->contains(
                            'id',
                            $item->kelas_id
                        )
                );

                $jadwal->sudah_absen = $pendaftaran
                    ? Absensi::where('jadwal_id', $jadwal->id)
                        ->where(
                            'pendaftaran_id',
                            $pendaftaran->id
                        )
                        ->exists()
                    : false;

                return $jadwal;
            });

        // Riwayat absensi hanya milik peserta yang login.
        $pendaftaranIds = $pendaftarans->pluck('id');

        $recentAttendances = Absensi::with([
            'pendaftaran.peserta',
            'pendaftaran.kelas',
            'jadwal.programPelatihan.pelatihan',
            'jadwal.programPelatihan.tahap',
        ])
            ->whereIn('pendaftaran_id', $pendaftaranIds)
            ->latest('waktu_masuk')
            ->limit(10)
            ->get();

        return Inertia::render('Peserta/Absensi/Index', [
            'jadwals' => $jadwals,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Memproses absensi wajah peserta yang sedang login.
     */
    public function recognize(Request $request)
    {
        $peserta = $this->pesertaLogin();

        abort_unless($peserta, 403);

        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'jadwal_id' => [
                'required',
                'integer',
                'exists:jadwal,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1. Ambil jadwal
        |--------------------------------------------------------------------------
        */

        $jadwal = Jadwal::with([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])->findOrFail($validated['jadwal_id']);

        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | 2. Validasi tanggal dan jam
        |--------------------------------------------------------------------------
        */

        if (!Carbon::parse($jadwal->tanggal)->isSameDay($now)) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ini bukan jadwal hari ini.',
            ], 422);
        }

        $tanggal = Carbon::parse($jadwal->tanggal)->format('Y-m-d');

        $mulai = Carbon::parse(
            $tanggal . ' ' . $jadwal->jam_mulai
        );

        $selesai = Carbon::parse(
            $tanggal . ' ' . $jadwal->jam_selesai
        );

        if ($now->lt($mulai)) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Absensi belum dibuka. Jadwal dimulai pukul ' .
                    Carbon::parse($jadwal->jam_mulai)->format('H:i') .
                    '.',
            ], 422);
        }

        if ($now->gt($selesai)) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu absensi untuk jadwal ini sudah berakhir.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Pastikan peserta terdaftar pada program dan kelas jadwal
        |--------------------------------------------------------------------------
        */

        $pendaftaran = Pendaftaran::with([
            'peserta',
            'kelas',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
        ])
            ->where('peserta_id', $peserta->id)
            ->where('status', 'aktif')
            ->where(
                'program_pelatihan_id',
                $jadwal->program_pelatihan_id
            )
            ->whereIn(
                'kelas_id',
                $jadwal->kelas->pluck('id')
            )
            ->first();

        if (!$pendaftaran) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Anda tidak terdaftar pada program atau kelas jadwal ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Pastikan peserta memiliki data wajah
        |--------------------------------------------------------------------------
        */

        $pesertaFace = PesertaFace::where(
            'peserta_id',
            $peserta->id
        )->first();

        if (!$pesertaFace || !$pesertaFace->face_embedding) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Data wajah Anda belum terdaftar. Hubungi administrator.',
            ], 422);
        }

        $storedEmbedding = $this->decodeEmbedding(
            $pesertaFace->face_embedding
        );

        if (
            !is_array($storedEmbedding) ||
            count($storedEmbedding) !== 512
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data embedding wajah Anda tidak valid.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Buat embedding dari foto kamera
        |--------------------------------------------------------------------------
        */

        try {
            $embedding = $this->faceRecognitionService
                ->generateEmbedding($request->file('image'));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Wajah tidak dapat diproses. Pastikan wajah terlihat jelas.',
            ], 422);
        }

        if (
            !is_array($embedding) ||
            count($embedding) !== 512
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Embedding wajah hasil kamera tidak valid.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Cocokkan hanya dengan embedding peserta yang login
        |--------------------------------------------------------------------------
        */

        $score = $this->cosineSimilarity(
            $embedding,
            $storedEmbedding
        );

        // Samakan threshold dengan kebijakan pengenalan di sistem admin.
        $threshold = 0.70;

        if ($score < $threshold) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Wajah tidak cocok dengan akun Anda. Silakan coba kembali.',
                'face_score' => round($score, 4),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Cegah absensi ganda
        |--------------------------------------------------------------------------
        */

        $sudahAbsen = Absensi::where(
            'jadwal_id',
            $jadwal->id
        )
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi pada jadwal ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Simpan absensi
        |--------------------------------------------------------------------------
        */

        try {
            $absensi = DB::transaction(function () use (
                $jadwal,
                $pendaftaran,
                $score,
                $now
            ) {
                // Periksa kembali di dalam transaksi.
                $sudahAbsen = Absensi::where(
                    'jadwal_id',
                    $jadwal->id
                )
                    ->where(
                        'pendaftaran_id',
                        $pendaftaran->id
                    )
                    ->lockForUpdate()
                    ->exists();

                if ($sudahAbsen) {
                    throw ValidationException::withMessages([
                        'jadwal_id' =>
                            'Anda sudah melakukan absensi pada jadwal ini.',
                    ]);
                }

                return Absensi::create([
                    'jadwal_id' => $jadwal->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'waktu_masuk' => $now,
                    'waktu_keluar' => null,
                    'status' => 'hadir',
                    'metode' => 'face',
                    'face_score' => round($score, 2),
                    'keterangan' =>
                        'Absensi peserta menggunakan verifikasi wajah.',
                ]);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan absensi peserta.', [
                'peserta_id' => $peserta->id,
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan absensi.',
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Respons berhasil
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' =>
                'Absensi berhasil. Wajah Anda terverifikasi dengan kecocokan ' .
                number_format($score * 100, 2) . '%.',

            'peserta' => [
                'id' => $peserta->id,
                'nama' => $peserta->nama,
            ],

            'program_pelatihan' => [
                'id' => $jadwal->programPelatihan->id,
                'nama' => $jadwal->programPelatihan->nama,
                'pelatihan' =>
                    $jadwal->programPelatihan->pelatihan->nama ?? '-',
                'tahap' =>
                    $jadwal->programPelatihan->tahap->nama ?? '-',
            ],

            'kelas' => [
                'id' => $pendaftaran->kelas->id,
                'nama' => $pendaftaran->kelas->nama,
            ],

            'jadwal' => [
                'id' => $jadwal->id,
                'pertemuan' => $jadwal->pertemuan,
            ],

            'absensi' => [
                'id' => $absensi->id,
                'waktu_masuk' => $absensi->waktu_masuk,
                'status' => $absensi->status,
                'metode' => $absensi->metode,
            ],

            'face_score' => round($score, 2),
        ]);
    }

    /**
     * Mendekode embedding yang disimpan dalam database.
     */
    private function decodeEmbedding($embedding): ?array
    {
        if (is_array($embedding)) {
            return $embedding;
        }

        if (!is_string($embedding)) {
            return null;
        }

        $decoded = json_decode($embedding, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Menghitung cosine similarity dua embedding wajah.
     */
    private function cosineSimilarity(
        array $a,
        array $b
    ): float {
        if (count($a) !== count($b) || empty($a)) {
            return -1.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $value) {
            if (!is_numeric($value) || !is_numeric($b[$i])) {
                return -1.0;
            }

            $x = (float) $value;
            $y = (float) $b[$i];

            $dotProduct += $x * $y;
            $normA += $x * $x;
            $normB += $y * $y;
        }

        if ($normA <= 0 || $normB <= 0) {
            return -1.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
