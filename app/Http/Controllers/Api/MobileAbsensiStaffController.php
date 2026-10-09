<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiStaff;
use App\Models\JadwalStaff;
use App\Models\LokasiAbsensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class MobileAbsensiStaffController extends Controller
{

    public function lokasi(): JsonResponse
    {
        $lokasi = LokasiAbsensi::query()
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'lokasi' => $lokasi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'radius' => (int) $item->radius,
                ];
            })->values(),
        ]);
    }

    /**
     * ============================================================
     * ABSEN MASUK
     * ============================================================
     */
    public function masuk(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'lokasi_absensi_id' => ['required', 'integer', 'exists:lokasi_absensi,id'],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | 1. CEK WAJAH STAFF
        |--------------------------------------------------------------------------
        */

        if (empty($user->face_embedding)) {
            return response()->json([
                'success' => false,
                'message' => 'Data wajah Anda belum terdaftar.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. CEK ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        $absensi = AbsensiStaff::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($absensi && $absensi->waktu_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini.',
                'absensi' => $this->formatAbsensi($absensi),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. CEK LOKASI
        |--------------------------------------------------------------------------
        */

        $lokasi = LokasiAbsensi::where('id', $validated['lokasi_absensi_id'])
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
        | 4. HITUNG JARAK
        |--------------------------------------------------------------------------
        */

        $jarak = $this->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        if ($jarak > $lokasi->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius lokasi absensi.',
                'jarak' => round($jarak, 2),
                'radius' => $lokasi->radius,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. CEK JADWAL STAFF
        |--------------------------------------------------------------------------
        */

        $hari = strtolower(
            Carbon::now()->locale('id')->translatedFormat('l')
        );

        $jadwal = JadwalStaff::where('hari', $hari)
            ->where('status', true)
            ->orderBy('jam_masuk')
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal staff aktif untuk hari ini.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. FACE RECOGNITION
        |--------------------------------------------------------------------------
        */

        $faceResult = $this->recognizeStaffFace(
            $request->file('image'),
            $user
        );

        if (!$faceResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $faceResult['message'],
            ], 422);
        }

        $faceScore = $faceResult['score'];

        /*
        |--------------------------------------------------------------------------
        | 7. TENTUKAN STATUS HADIR / TERLAMBAT
        |--------------------------------------------------------------------------
        */

        $now = Carbon::now();

        $jamMasuk = Carbon::today()->setTimeFromTimeString(
            $jadwal->jam_masuk
        );

        $batasToleransi = $jamMasuk->copy()->addMinutes(
            (int) $jadwal->toleransi_menit
        );

        $status = $now->greaterThan($batasToleransi)
            ? 'terlambat'
            : 'hadir';

        /*
        |--------------------------------------------------------------------------
        | 8. SIMPAN FOTO
        |--------------------------------------------------------------------------
        */

        $photoPath = $request->file('image')->store(
            'absensi-staff/' . $today->format('Y/m/d'),
            'public'
        );

        /*
        |--------------------------------------------------------------------------
        | 9. SIMPAN ABSENSI
        |--------------------------------------------------------------------------
        */

        if ($absensi) {
            $absensi->update([
                'lokasi_absensi_id' => $lokasi->id,
                'waktu_masuk' => $now,
                'latitude_masuk' => $validated['latitude'],
                'longitude_masuk' => $validated['longitude'],
                'jarak_masuk' => $jarak,
                'face_score_masuk' => $faceScore * 100,
                'metode' => 'face',
                'status' => $status,
            ]);
        } else {
            $absensi = AbsensiStaff::create([
                'user_id' => $user->id,
                'lokasi_absensi_id' => $lokasi->id,
                'tanggal' => $today,
                'waktu_masuk' => $now,
                'latitude_masuk' => $validated['latitude'],
                'longitude_masuk' => $validated['longitude'],
                'jarak_masuk' => $jarak,
                'face_score_masuk' => $faceScore * 100,
                'metode' => 'face',
                'status' => $status,
                'keterangan' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $status === 'terlambat'
                ? 'Absen masuk berhasil. Anda tercatat terlambat.'
                : 'Absen masuk berhasil.',

            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
            ],

            'absensi' => $this->formatAbsensi($absensi),

            'lokasi' => [
                'id' => $lokasi->id,
                'nama' => $lokasi->nama,
                'jarak' => round($jarak, 2),
                'radius' => $lokasi->radius,
            ],

            'jadwal' => [
                'id' => $jadwal->id,
                'nama' => $jadwal->nama,
                'hari' => $jadwal->hari,
                'jam_masuk' => $jadwal->jam_masuk,
                'jam_pulang' => $jadwal->jam_pulang,
                'toleransi_menit' => $jadwal->toleransi_menit,
            ],

            'face_score' => round($faceScore * 100, 2),

            'foto' => Storage::url($photoPath),
        ]);
    }

    /**
     * ============================================================
     * ABSEN PULANG
     * ============================================================
     */
    public function pulang(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'lokasi_absensi_id' => ['required', 'integer', 'exists:lokasi_absensi,id'],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | 1. CEK WAJAH
        |--------------------------------------------------------------------------
        */

        if (empty($user->face_embedding)) {
            return response()->json([
                'success' => false,
                'message' => 'Data wajah Anda belum terdaftar.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. CARI ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */

        $absensi = AbsensiStaff::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if (!$absensi || !$absensi->waktu_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 422);
        }

        if ($absensi->waktu_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
                'absensi' => $this->formatAbsensi($absensi),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. CEK LOKASI
        |--------------------------------------------------------------------------
        */

        $lokasi = LokasiAbsensi::where('id', $validated['lokasi_absensi_id'])
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
        | 4. HITUNG JARAK
        |--------------------------------------------------------------------------
        */

        $jarak = $this->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        if ($jarak > $lokasi->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius lokasi absensi.',
                'jarak' => round($jarak, 2),
                'radius' => $lokasi->radius,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. FACE RECOGNITION
        |--------------------------------------------------------------------------
        */

        $faceResult = $this->recognizeStaffFace(
            $request->file('image'),
            $user
        );

        if (!$faceResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $faceResult['message'],
            ], 422);
        }

        $faceScore = $faceResult['score'];

        /*
        |--------------------------------------------------------------------------
        | 6. SIMPAN FOTO
        |--------------------------------------------------------------------------
        */

        $photoPath = $request->file('image')->store(
            'absensi-staff/' . Carbon::today()->format('Y/m/d'),
            'public'
        );

        /*
        |--------------------------------------------------------------------------
        | 7. UPDATE ABSEN PULANG
        |--------------------------------------------------------------------------
        */

        $now = Carbon::now();

        $absensi->update([
            'waktu_keluar' => $now,
            'latitude_keluar' => $validated['latitude'],
            'longitude_keluar' => $validated['longitude'],
            'jarak_keluar' => $jarak,
            'face_score_keluar' => $faceScore * 100,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil.',

            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
            ],

            'absensi' => $this->formatAbsensi($absensi),

            'lokasi' => [
                'id' => $lokasi->id,
                'nama' => $lokasi->nama,
                'jarak' => round($jarak, 2),
                'radius' => $lokasi->radius,
            ],

            'face_score' => round($faceScore * 100, 2),

            'foto' => Storage::url($photoPath),
        ]);
    }

    /**
     * ============================================================
     * FACE RECOGNITION
     * ============================================================
     */
    private function recognizeStaffFace($image, User $user): array
    {
        try {
            $response = Http::timeout(30)
                ->attach(
                    'image',
                    fopen($image->getRealPath(), 'r'),
                    $image->getClientOriginalName()
                )
                ->post(
                    rtrim(config('services.face_recognition.url'), '/')
                    . '/embedding'
                );

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Server Face Recognition tidak dapat memproses wajah.',
                ];
            }

            $data = $response->json();

            $embedding = $data['embedding'] ?? null;

            if (!is_array($embedding)) {
                return [
                    'success' => false,
                    'message' => 'Embedding wajah tidak valid.',
                ];
            }

            if (count($embedding) !== 512) {
                return [
                    'success' => false,
                    'message' => 'Dimensi embedding wajah tidak sesuai.',
                ];
            }

            $registeredEmbedding = json_decode(
                $user->face_embedding,
                true
            );

            if (!is_array($registeredEmbedding)) {
                return [
                    'success' => false,
                    'message' => 'Data embedding wajah staff tidak valid.',
                ];
            }

            if (count($registeredEmbedding) !== 512) {
                return [
                    'success' => false,
                    'message' => 'Data embedding wajah staff rusak.',
                ];
            }

            $score = $this->cosineSimilarity(
                $embedding,
                $registeredEmbedding
            );

            /*
            |--------------------------------------------------------------------------
            | THRESHOLD
            |--------------------------------------------------------------------------
            */

            $threshold = 0.70;

            if ($score < $threshold) {
                return [
                    'success' => false,
                    'message' =>
                        'Wajah tidak cocok. Tingkat kecocokan: '
                        . number_format($score * 100, 2)
                        . '%.',
                    'score' => $score,
                ];
            }

            return [
                'success' => true,
                'score' => $score,
            ];

        } catch (\Throwable $e) {

            report($e);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi wajah.',
            ];
        }
    }

    /**
     * ============================================================
     * COSINE SIMILARITY
     * ============================================================
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || count($a) === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $value) {
            $x = (float) $value;
            $y = (float) $b[$i];

            $dot += $x * $y;
            $normA += $x * $x;
            $normB += $y * $y;
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return $dot / (
            sqrt($normA) * sqrt($normB)
        );
    }

    /**
     * ============================================================
     * HITUNG JARAK GPS
     * ============================================================
     */
    private function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a =
            sin($deltaLat / 2) ** 2
            +
            cos($lat1Rad)
            *
            cos($lat2Rad)
            *
            sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }

    /**
     * ============================================================
     * FORMAT ABSENSI
     * ============================================================
     */
    private function formatAbsensi(AbsensiStaff $absensi): array
    {
        return [
            'id' => $absensi->id,
            'tanggal' => $absensi->tanggal?->format('Y-m-d'),

            'waktu_masuk' => $absensi->waktu_masuk?->format('H:i:s'),
            'waktu_keluar' => $absensi->waktu_keluar?->format('H:i:s'),

            'status' => $absensi->status,

            'latitude_masuk' => $absensi->latitude_masuk,
            'longitude_masuk' => $absensi->longitude_masuk,
            'jarak_masuk' => $absensi->jarak_masuk,
            'face_score_masuk' => $absensi->face_score_masuk,

            'latitude_keluar' => $absensi->latitude_keluar,
            'longitude_keluar' => $absensi->longitude_keluar,
            'jarak_keluar' => $absensi->jarak_keluar,
            'face_score_keluar' => $absensi->face_score_keluar,
        ];
    }
}