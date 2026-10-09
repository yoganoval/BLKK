<?php

namespace App\Http\Controllers;

use App\Models\AbsensiStaff;
use App\Models\JadwalStaff;
use App\Models\LokasiAbsensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Models\HariLibur;


class AbsensiStaffController extends Controller
{
    /**
     * ============================================================
     * HALAMAN ABSENSI STAFF
     * ============================================================
     */
    public function absen(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Hari sekarang
        |--------------------------------------------------------------------------
        */
        $hari = strtolower(
            Carbon::now()->locale('id')->dayName
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil jadwal staff aktif hari ini
        |--------------------------------------------------------------------------
        */
        $jadwal = JadwalStaff::query()
            ->where('hari', $hari)
            ->where('status', true)
            ->orderBy('jam_masuk')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Ambil absensi staff hari ini
        |--------------------------------------------------------------------------
        */
        $absensiHariIni = AbsensiStaff::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Ambil lokasi absensi aktif
        |--------------------------------------------------------------------------
        */
        $lokasi = LokasiAbsensi::query()
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get([
                'id',
                'nama',
                'latitude',
                'longitude',
                'radius',
                'status',
                'keterangan',
            ]);

        return Inertia::render('AbsensiStaff/Absen', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'face_registered' => !empty($user->face_embedding),
            ],

            'lokasi' => $lokasi,

            'absensiHariIni' => $absensiHariIni,

            'jadwal' => $jadwal,
        ]);
    }

    private function getJadwalHariIni(): array
    {
        $tanggal = today();

        /*
        |--------------------------------------------------------------------------
        | CEK HARI LIBUR KHUSUS
        |--------------------------------------------------------------------------
        */

        $hariLibur = HariLibur::query()
            ->whereDate('tanggal', $tanggal)
            ->where('status', true)
            ->first();

        if ($hariLibur) {
            return [
                'jadwal' => null,
                'libur' => true,
                'alasan' => $hariLibur->nama,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CEK HARI
        |--------------------------------------------------------------------------
        */

        $hari = strtolower(
            Carbon::now()
                ->locale('id')
                ->translatedFormat('l')
        );

        /*
        |--------------------------------------------------------------------------
        | AMBIL JADWAL AKTIF
        |--------------------------------------------------------------------------
        */

        $jadwal = JadwalStaff::query()
            ->where('hari', $hari)
            ->where('status', true)
            ->orderBy('jam_masuk')
            ->first();

        if (!$jadwal) {
            return [
                'jadwal' => null,
                'libur' => true,
                'alasan' => 'Tidak ada jadwal kerja aktif hari ini.',
            ];
        }

        return [
            'jadwal' => $jadwal,
            'libur' => false,
            'alasan' => null,
        ];
    }


    /**
     * ============================================================
     * ABSENSI MASUK
     * ============================================================
     */
    public function masuk(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'accuracy' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'lokasi_absensi_id' => [
                'required',
                'integer',
                'exists:lokasi_absensi,id',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Pastikan wajah sudah terdaftar
        |--------------------------------------------------------------------------
        */

        if (empty($user->face_embedding)) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah Anda belum terdaftar. Silakan daftarkan wajah terlebih dahulu.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK JADWAL HARI INI
        |--------------------------------------------------------------------------
        */

        $jadwalInfo = $this->getJadwalHariIni();

        if (!$jadwalInfo['jadwal']) {
            return response()->json([
                'success' => false,
                'message' => $jadwalInfo['libur']
                    ? 'Absensi ditutup. ' . $jadwalInfo['alasan']
                    : $jadwalInfo['alasan'],
            ], 422);
        }

        $jadwal = $jadwalInfo['jadwal'];

        /*
        |--------------------------------------------------------------------------
        | CEK JAM MASUK
        |--------------------------------------------------------------------------
        */

        $now = Carbon::now();

        $jamMasuk = Carbon::today()
            ->setTimeFromTimeString($jadwal->jam_masuk);

        $batasTerlambat = $jamMasuk->copy()
            ->addMinutes((int) $jadwal->toleransi_menit);

        /*
        |--------------------------------------------------------------------------
        | BELUM MASUK JAM KERJA
        |--------------------------------------------------------------------------
        */

        if ($now->lt($jamMasuk)) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi masuk belum dibuka. Absensi dimulai pukul '
                    . $jamMasuk->format('H:i') . '.',
                'jadwal' => [
                    'jam_masuk' => $jadwal->jam_masuk,
                    'jam_pulang' => $jadwal->jam_pulang,
                    'toleransi_menit' => $jadwal->toleransi_menit,
                ],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK SUDAH ABSEN MASUK
        |--------------------------------------------------------------------------
        */

        $existing = AbsensiStaff::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing?->waktu_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi masuk hari ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL LOKASI AKTIF
        |--------------------------------------------------------------------------
        */

        $lokasi = LokasiAbsensi::query()
            ->where('id', $validated['lokasi_absensi_id'])
            ->where('status', 'aktif')
            ->first();

        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi absensi tidak aktif atau tidak ditemukan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG JARAK GPS
        |--------------------------------------------------------------------------
        */

        $distance = $this->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        /*
        |--------------------------------------------------------------------------
        | CEK RADIUS
        |--------------------------------------------------------------------------
        */

        if ($distance > (float) $lokasi->radius) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Anda berada di luar radius absensi. Jarak Anda ' .
                    number_format($distance, 2) .
                    ' meter, sedangkan radius maksimal ' .
                    number_format($lokasi->radius, 0) .
                    ' meter.',
                'distance' => round($distance, 2),
                'radius' => (int) $lokasi->radius,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | FACE RECOGNITION
        |--------------------------------------------------------------------------
        */

        $faceResult = $this->recognizeStaffFace(
            $request,
            $user
        );

        if (!$faceResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $faceResult['message'],
                'face_score' => $faceResult['score'] ?? null,
            ], 422);
        }

        $faceScore = $faceResult['score'];

        /*
        |--------------------------------------------------------------------------
        | TENTUKAN STATUS
        |--------------------------------------------------------------------------
        */

        $status = $now->gt($batasTerlambat)
            ? 'terlambat'
            : 'hadir';

        /*
        |--------------------------------------------------------------------------
        | SIMPAN ABSENSI
        |--------------------------------------------------------------------------
        */

        $absensi = AbsensiStaff::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => today()->toDateString(),
            ],
            [
                'lokasi_absensi_id' => $lokasi->id,

                'waktu_masuk' => $now,

                'latitude_masuk' => $validated['latitude'],
                'longitude_masuk' => $validated['longitude'],

                'jarak_masuk' => round($distance, 2),

                'face_score_masuk' => $faceScore,

                'metode' => 'face',

                'status' => $status,

                'keterangan' => $status === 'terlambat'
                    ? 'Absensi masuk terlambat.'
                    : null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' => $status === 'terlambat'
                ? 'Absensi masuk berhasil, tetapi Anda terlambat.'
                : 'Absensi masuk berhasil.',

            'absensi' => [
                'id' => $absensi->id,
                'tanggal' => $absensi->tanggal,
                'waktu_masuk' => $absensi->waktu_masuk,
                'status' => $absensi->status,
                'face_score' => $faceScore,
                'jarak' => round($distance, 2),

                'lokasi' => [
                    'id' => $lokasi->id,
                    'nama' => $lokasi->nama,
                ],

                'jadwal' => [
                    'id' => $jadwal->id,
                    'nama' => $jadwal->nama,
                    'hari' => $jadwal->hari,
                    'jam_masuk' => $jadwal->jam_masuk,
                    'jam_pulang' => $jadwal->jam_pulang,
                    'toleransi_menit' => $jadwal->toleransi_menit,
                ],
            ],
        ]);
    }

    /**
     * ============================================================
     * ABSENSI PULANG
     * ============================================================
     */
    public function pulang(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'accuracy' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'lokasi_absensi_id' => [
                'required',
                'integer',
                'exists:lokasi_absensi,id',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Pastikan wajah sudah terdaftar
        |--------------------------------------------------------------------------
        */

        if (empty($user->face_embedding)) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah Anda belum terdaftar. Silakan daftarkan wajah terlebih dahulu.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK JADWAL HARI INI
        |--------------------------------------------------------------------------
        */

        $jadwalInfo = $this->getJadwalHariIni();

        if (!$jadwalInfo['jadwal']) {
            return response()->json([
                'success' => false,
                'message' => $jadwalInfo['libur']
                    ? 'Absensi ditutup. ' . $jadwalInfo['alasan']
                    : $jadwalInfo['alasan'],
            ], 422);
        }

        $jadwal = $jadwalInfo['jadwal'];

        /*
        |--------------------------------------------------------------------------
        | CEK JAM PULANG
        |--------------------------------------------------------------------------
        */

        $now = Carbon::now();

        $jamPulang = Carbon::today()
            ->setTimeFromTimeString($jadwal->jam_pulang);

        /*
        |--------------------------------------------------------------------------
        | HARUS SETELAH JAM PULANG
        |--------------------------------------------------------------------------
        */

        if ($now->lte($jamPulang)) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi pulang belum dibuka. Silakan absen setelah pukul '
                    . $jamPulang->format('H:i') . '.',

                'jadwal' => [
                    'jam_masuk' => $jadwal->jam_masuk,
                    'jam_pulang' => $jadwal->jam_pulang,
                ],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CARI ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */

        $absensi = AbsensiStaff::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absensi masuk hari ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK SUDAH PULANG
        |--------------------------------------------------------------------------
        */

        if ($absensi->waktu_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi pulang hari ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL LOKASI AKTIF
        |--------------------------------------------------------------------------
        */

        $lokasi = LokasiAbsensi::query()
            ->where('id', $validated['lokasi_absensi_id'])
            ->where('status', 'aktif')
            ->first();

        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi absensi tidak aktif atau tidak ditemukan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG JARAK GPS
        |--------------------------------------------------------------------------
        */

        $distance = $this->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        /*
        |--------------------------------------------------------------------------
        | CEK RADIUS
        |--------------------------------------------------------------------------
        */

        if ($distance > (float) $lokasi->radius) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Anda berada di luar radius absensi. Jarak Anda ' .
                    number_format($distance, 2) .
                    ' meter, sedangkan radius maksimal ' .
                    number_format($lokasi->radius, 0) .
                    ' meter.',

                'distance' => round($distance, 2),
                'radius' => (int) $lokasi->radius,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | FACE RECOGNITION
        |--------------------------------------------------------------------------
        */

        $faceResult = $this->recognizeStaffFace(
            $request,
            $user
        );

        if (!$faceResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $faceResult['message'],
                'face_score' => $faceResult['score'] ?? null,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN ABSENSI PULANG
        |--------------------------------------------------------------------------
        */

        $absensi->update([
            'waktu_keluar' => $now,

            'latitude_keluar' => $validated['latitude'],
            'longitude_keluar' => $validated['longitude'],

            'jarak_keluar' => round($distance, 2),

            'face_score_keluar' => $faceResult['score'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' => 'Absensi pulang berhasil.',

            'absensi' => [
                'id' => $absensi->id,

                'tanggal' => $absensi->tanggal,

                'waktu_masuk' => $absensi->waktu_masuk,

                'waktu_keluar' => $absensi->waktu_keluar,

                'status' => $absensi->status,

                'face_score' => $faceResult['score'],

                'jarak' => round($distance, 2),

                'lokasi' => [
                    'id' => $lokasi->id,
                    'nama' => $lokasi->nama,
                ],

                'jadwal' => [
                    'id' => $jadwal->id,
                    'nama' => $jadwal->nama,
                    'hari' => $jadwal->hari,
                    'jam_masuk' => $jadwal->jam_masuk,
                    'jam_pulang' => $jadwal->jam_pulang,
                    'toleransi_menit' => $jadwal->toleransi_menit,
                ],
            ],
        ]);
    }

    /**
     * ============================================================
     * FACE RECOGNITION STAFF
     * ============================================================
     */
    private function recognizeStaffFace(
        Request $request,
        $user
    ): array {
        try {
            /*
            |--------------------------------------------------------------------------
            | URL Python Face Recognition
            |--------------------------------------------------------------------------
            */
            $faceUrl = rtrim(
                config('services.face_recognition.url'),
                '/'
            );

            if (empty($faceUrl)) {
                return [
                    'success' => false,
                    'message' => 'Konfigurasi Face Recognition belum tersedia.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan file tersedia
            |--------------------------------------------------------------------------
            */
            if (!$request->hasFile('image')) {
                return [
                    'success' => false,
                    'message' => 'Foto wajah tidak ditemukan.',
                ];
            }

            $image = $request->file('image');

            if (!$image->isValid()) {
                return [
                    'success' => false,
                    'message' => 'File foto wajah tidak valid.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Kirim foto ke Python
            |--------------------------------------------------------------------------
            */
            $response = Http::timeout(60)
                ->attach(
                    'image',
                    file_get_contents(
                        $image->getRealPath()
                    ),
                    $image->getClientOriginalName()
                )
                ->post(
                    $faceUrl . '/embedding'
                );

            /*
            |--------------------------------------------------------------------------
            | Cek response Python
            |--------------------------------------------------------------------------
            */
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' =>
                        'Server Face Recognition tidak dapat memproses wajah.',
                ];
            }

            $data = $response->json();

            /*
            |--------------------------------------------------------------------------
            | Pastikan embedding tersedia
            |--------------------------------------------------------------------------
            */
            if (
                !isset($data['embedding']) ||
                !is_array($data['embedding'])
            ) {
                return [
                    'success' => false,
                    'message' => 'Embedding wajah tidak ditemukan.',
                ];
            }

            $probeEmbedding = $data['embedding'];

            /*
            |--------------------------------------------------------------------------
            | Facenet512 harus menghasilkan 512 nilai
            |--------------------------------------------------------------------------
            */
            if (count($probeEmbedding) !== 512) {
                return [
                    'success' => false,
                    'message' =>
                        'Embedding wajah tidak valid. Model harus menghasilkan 512 data.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil embedding wajah staff dari database
            |--------------------------------------------------------------------------
            */
            $storedEmbedding = json_decode(
                $user->face_embedding,
                true
            );

            if (
                !is_array($storedEmbedding) ||
                count($storedEmbedding) !== 512
            ) {
                return [
                    'success' => false,
                    'message' =>
                        'Data wajah staff tidak valid. Silakan daftarkan ulang wajah.',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung cosine similarity
            |--------------------------------------------------------------------------
            */
            $score = $this->cosineSimilarity(
                $storedEmbedding,
                $probeEmbedding
            );

            /*
            |--------------------------------------------------------------------------
            | Threshold kecocokan
            |--------------------------------------------------------------------------
            */
            $threshold = 0.70;

            /*
            |--------------------------------------------------------------------------
            | Wajah tidak cocok
            |--------------------------------------------------------------------------
            */
            if ($score < $threshold) {
                return [
                    'success' => false,

                    'message' =>
                        'Wajah tidak cocok. Tingkat kecocokan: ' .
                        number_format(
                            $score * 100,
                            2
                        ) .
                        '%.',

                    'score' => round(
                        $score * 100,
                        2
                    ),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Wajah cocok
            |--------------------------------------------------------------------------
            */
            return [
                'success' => true,

                'message' =>
                    'Wajah berhasil diverifikasi.',

                'score' => round(
                    $score * 100,
                    2
                ),
            ];

        } catch (\Throwable $e) {
            /*
            |--------------------------------------------------------------------------
            | Simpan error ke Laravel log
            |--------------------------------------------------------------------------
            */
            report($e);

            return [
                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat verifikasi wajah: ' .
                    $e->getMessage(),
            ];
        }
    }

    /**
     * ============================================================
     * COSINE SIMILARITY
     * ============================================================
     */
    private function cosineSimilarity(
        array $a,
        array $b
    ): float {
        /*
        |--------------------------------------------------------------------------
        | Pastikan jumlah dimensi sama
        |--------------------------------------------------------------------------
        */
        if (
            count($a) !== count($b) ||
            count($a) === 0
        ) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $valueA) {
            $valueA = (float) $valueA;
            $valueB = (float) ($b[$i] ?? 0);

            $dotProduct +=
                $valueA * $valueB;

            $normA +=
                $valueA * $valueA;

            $normB +=
                $valueB * $valueB;
        }

        /*
        |--------------------------------------------------------------------------
        | Hindari pembagian dengan nol
        |--------------------------------------------------------------------------
        */
        if (
            $normA <= 0 ||
            $normB <= 0
        ) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | Cosine similarity
        |--------------------------------------------------------------------------
        */
        return $dotProduct /
            (
                sqrt($normA) *
                sqrt($normB)
            );
    }

    /**
     * ============================================================
     * HITUNG JARAK GPS
     * ============================================================
     *
     * Haversine Formula
     *
     * Hasil dalam meter.
     */
    private function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000;

        $dLat = deg2rad(
            $lat2 - $lat1
        );

        $dLon = deg2rad(
            $lon2 - $lon1
        );

        $a =
            sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c =
            2 *
            atan2(
                sqrt($a),
                sqrt(1 - $a)
            );

        return $earthRadius * $c;
    }

    /**
     * ============================================================
     * MONITORING ABSENSI STAFF
     * ============================================================
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Query absensi
        |--------------------------------------------------------------------------
        */
        $query = AbsensiStaff::with([
            'user',
            'lokasiAbsensi',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tanggal')) {
            $query->whereDate(
                'tanggal',
                $request->tanggal
            );
        } else {
            $query->whereDate(
                'tanggal',
                today()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter staff
        |--------------------------------------------------------------------------
        */
        if ($request->filled('user_id')) {
            $query->where(
                'user_id',
                $request->user_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $absensis = $query
            ->latest('tanggal')
            ->latest('waktu_masuk')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Daftar staff
        |--------------------------------------------------------------------------
        */
        $users = User::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'username',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Daftar lokasi aktif
        |--------------------------------------------------------------------------
        */
        $lokasis = LokasiAbsensi::query()
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get([
                'id',
                'nama',
                'latitude',
                'longitude',
                'radius',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Tanggal statistik
        |--------------------------------------------------------------------------
        */
        $tanggal = $request->filled('tanggal')
            ? $request->tanggal
            : today()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */
        $statsQuery = AbsensiStaff::query()
            ->whereDate(
                'tanggal',
                $tanggal
            );

        $stats = [
            'total' =>
                (clone $statsQuery)->count(),

            'hadir' =>
                (clone $statsQuery)
                    ->where('status', 'hadir')
                    ->count(),

            'terlambat' =>
                (clone $statsQuery)
                    ->where('status', 'terlambat')
                    ->count(),

            'izin' =>
                (clone $statsQuery)
                    ->where('status', 'izin')
                    ->count(),

            'sakit' =>
                (clone $statsQuery)
                    ->where('status', 'sakit')
                    ->count(),

            'dinas' =>
                (clone $statsQuery)
                    ->where('status', 'dinas')
                    ->count(),

            'alpha' =>
                (clone $statsQuery)
                    ->where('status', 'alpha')
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Response Inertia
        |--------------------------------------------------------------------------
        */
        return Inertia::render(
            'AbsensiStaff/Index',
            [
                'absensis' => $absensis,

                'users' => $users,

                'lokasis' => $lokasis,

                'stats' => $stats,

                'filters' => [
                    'tanggal' => $tanggal,
                    'user_id' => $request->user_id,
                    'status' => $request->status,
                ],
            ]
        );
    }

    /**
     * ============================================================
     * DETAIL ABSENSI STAFF
     * ============================================================
     */
    public function show(
        AbsensiStaff $absensiStaff
    ) {
        $absensiStaff->load([
            'user',
            'lokasiAbsensi',
        ]);

        return Inertia::render(
            'AbsensiStaff/Show',
            [
                'absensi' => $absensiStaff,
            ]
        );
    }

    /**
     * ============================================================
     * HAPUS DATA ABSENSI
     * ============================================================
     */
    public function destroy(
        AbsensiStaff $absensiStaff
    ) {
        $absensiStaff->delete();

        return redirect()
            ->route('absensi-staff.index')
            ->with(
                'success',
                'Data absensi staff berhasil dihapus.'
            );
    }



}