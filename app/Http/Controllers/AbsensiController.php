<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\PesertaFace;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AbsensiController extends Controller
{
    public function __construct(
        protected FaceRecognitionService $faceRecognitionService
    ) {
    }

    /**
     * Halaman absensi.
     */
    public function index()
    {
        $jadwals = Jadwal::with([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])
        ->whereDate('tanggal', today())
        ->orderBy('jam_mulai')
        ->get();

        $recentAttendances = Absensi::with([
            'pendaftaran.peserta',
            'pendaftaran.kelas',
            'jadwal.programPelatihan.pelatihan',
            'jadwal.programPelatihan.tahap',
        ])
        ->whereDate('waktu_masuk', today())
        ->latest('waktu_masuk')
        ->limit(5)
        ->get();

        return Inertia::render('Absensi/Index', [
            'jadwals' => $jadwals,
            'recentAttendances' => $recentAttendances,
        ]);
    }

    /**
     * Proses absensi menggunakan face recognition.
     */
    public function recognize(Request $request)
    {
        $request->validate([
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
            'programPelatihan',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])->findOrFail($request->jadwal_id);

        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | 2. Cek tanggal jadwal
        |--------------------------------------------------------------------------
        */

        if (!Carbon::parse($jadwal->tanggal)->isSameDay($now)) {

            return response()->json([
                'success' => false,
                'message' => 'Jadwal ini bukan jadwal hari ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Cek jam jadwal
        |--------------------------------------------------------------------------
        */

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
                'message' =>
                    'Waktu absensi untuk jadwal ini sudah berakhir.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Kirim foto ke Python / DeepFace
        |--------------------------------------------------------------------------
        */

        try {

            $embedding = $this->faceRecognitionService
                ->generateEmbedding(
                    $request->file('image')
                );

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Wajah tidak dapat diproses. ' .
                    'Pastikan wajah terlihat jelas.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Validasi embedding
        |--------------------------------------------------------------------------
        */

        if (
            !is_array($embedding) ||
            count($embedding) !== 512
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Embedding wajah tidak valid.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Ambil seluruh data wajah
        |--------------------------------------------------------------------------
        */

        $pesertaFaces = PesertaFace::with([
            'peserta.pendaftarans.programPelatihan',
            'peserta.pendaftarans.kelas',
        ])->get();

        if ($pesertaFaces->isEmpty()) {

            return response()->json([
                'success' => false,
                'message' => 'Belum ada data wajah peserta.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Cari wajah yang paling mirip
        |--------------------------------------------------------------------------
        */

        $bestMatch = null;
        $bestScore = -1;

        foreach ($pesertaFaces as $pesertaFace) {

            $storedEmbedding = json_decode(
                $pesertaFace->face_embedding,
                true
            );

            if (!is_array($storedEmbedding)) {
                continue;
            }

            if (count($storedEmbedding) !== 512) {
                continue;
            }

            $score = $this->cosineSimilarity(
                $embedding,
                $storedEmbedding
            );

            if ($score > $bestScore) {

                $bestScore = $score;
                $bestMatch = $pesertaFace;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Threshold pengenalan wajah
        |--------------------------------------------------------------------------
        */

        $threshold = 0.70;

        if (
            !$bestMatch ||
            $bestScore < $threshold
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak dikenali.',
                'face_score' => round($bestScore, 4),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Ambil peserta
        |--------------------------------------------------------------------------
        */

        $peserta = $bestMatch->peserta;

        if (!$peserta) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Data peserta dari wajah tidak ditemukan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Cari pendaftaran aktif
        |--------------------------------------------------------------------------
        */

        $pendaftaran = Pendaftaran::with([
            'peserta',
            'kelas',
            'programPelatihan',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
        ])
            ->where('peserta_id', $peserta->id)
            ->where('status', 'aktif')
            ->where(
                'program_pelatihan_id',
                $jadwal->program_pelatihan_id
            )
            ->first();

        if (!$pendaftaran) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Peserta ' .
                    $peserta->nama .
                    ' tidak terdaftar pada program pelatihan jadwal ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Cek kelas
        |--------------------------------------------------------------------------
        */

        $kelasSesuai = $jadwal->kelas->contains(
            'id',
            $pendaftaran->kelas_id
        );

        if (!$kelasSesuai) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Peserta ' .
                    $peserta->nama .
                    ' tidak termasuk kelas pada jadwal ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 12. Cek absensi ganda
        |--------------------------------------------------------------------------
        */

        $sudahAbsen = Absensi::where(
            'jadwal_id',
            $jadwal->id
        )
            ->where(
                'pendaftaran_id',
                $pendaftaran->id
            )
            ->exists();

        if ($sudahAbsen) {

            return response()->json([
                'success' => false,
                'message' =>
                    $peserta->nama .
                    ' sudah melakukan absensi pada jadwal ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 13. Simpan absensi
        |--------------------------------------------------------------------------
        */

        $absensi = DB::transaction(function () use (
            $jadwal,
            $pendaftaran,
            $bestScore,
            $now
        ) {

            return Absensi::create([
                'jadwal_id' => $jadwal->id,
                'pendaftaran_id' => $pendaftaran->id,
                'waktu_masuk' => $now,
                'waktu_keluar' => null,
                'status' => 'hadir',
                'metode' => 'face',
                'face_score' => round($bestScore, 2),
                'keterangan' =>
                    'Absensi menggunakan pengenalan wajah.',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | 14. Berhasil
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'Absensi berhasil untuk ' .
                $peserta->nama .
                '. Kecocokan wajah: ' .
                number_format($bestScore * 100, 2) .
                '%.',

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

            'face_score' => round($bestScore, 2),
        ]);
    }

    /**
     * Rekap absensi.
     */
    public function rekap()
    {
        $absensis = Absensi::with([
            'jadwal.programPelatihan.pelatihan',
            'jadwal.programPelatihan.tahap',
            'jadwal.kelas',

            'pendaftaran.peserta',
            'pendaftaran.kelas',
            'pendaftaran.programPelatihan',
        ])
            ->latest('waktu_masuk')
            ->get();

        return Inertia::render('Absensi/Rekap', [
            'absensis' => $absensis,
        ]);
    }

    /**
     * Hapus data absensi.
     */
    public function destroy(Absensi $absensi)
    {
        $absensi->delete();

        return redirect()
            ->route('rekap.absensi')
            ->with(
                'success',
                'Data absensi berhasil dihapus.'
            );
    }

    /**
     * Cosine similarity.
     */
    private function cosineSimilarity(
        array $a,
        array $b
    ): float {
        if (
            count($a) !== count($b) ||
            empty($a)
        ) {
            return 0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $value) {
            $valueA = (float) $value;
            $valueB = (float) $b[$i];

            $dot += $valueA * $valueB;
            $normA += $valueA ** 2;
            $normB += $valueB ** 2;
        }

        if (
            $normA == 0 ||
            $normB == 0
        ) {
            return 0;
        }

        return $dot / (
            sqrt($normA) *
            sqrt($normB)
        );
    }
}