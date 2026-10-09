<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiStaff;
use App\Models\JadwalStaff;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // User didapat dari token Sanctum
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | ABSENSI HARI INI
        |--------------------------------------------------------------------------
        */

        $absensiHariIni = AbsensiStaff::with('lokasiAbsensi')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        /*
        |--------------------------------------------------------------------------
        | JADWAL STAFF HARI INI
        |--------------------------------------------------------------------------
        */

        $hari = strtolower(
            Carbon::now()
                ->locale('id')
                ->translatedFormat('l')
        );

        $jadwalHariIni = JadwalStaff::where('hari', $hari)
            ->where('status', true)
            ->orderBy('jam_masuk')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | REKAP ABSENSI
        |--------------------------------------------------------------------------
        */

        $bulanIni = AbsensiStaff::where('user_id', $user->id)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month);

        $hadir = (clone $bulanIni)
            ->where('status', 'hadir')
            ->count();

        $terlambat = (clone $bulanIni)
            ->where('status', 'terlambat')
            ->count();

        $izin = (clone $bulanIni)
            ->where('status', 'izin')
            ->count();

        $sakit = (clone $bulanIni)
            ->where('status', 'sakit')
            ->count();

        $dinas = (clone $bulanIni)
            ->where('status', 'dinas')
            ->count();

        $alpha = (clone $bulanIni)
            ->where('status', 'alpha')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'staff' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
            ],

            'wajah' => [
                'terdaftar' => !empty($user->face_embedding),
                'foto' => $user->face_photo_path,
                'terdaftar_pada' => $user->face_registered_at,
            ],

            'absensi_hari_ini' => $absensiHariIni ? [
                'id' => $absensiHariIni->id,
                'tanggal' => $absensiHariIni->tanggal?->format('Y-m-d'),
                'waktu_masuk' => $absensiHariIni->waktu_masuk?->format('H:i:s'),
                'waktu_keluar' => $absensiHariIni->waktu_keluar?->format('H:i:s'),
                'status' => $absensiHariIni->status,

                'lokasi' => $absensiHariIni->lokasiAbsensi ? [
                    'id' => $absensiHariIni->lokasiAbsensi->id,
                    'nama' => $absensiHariIni->lokasiAbsensi->nama,
                ] : null,

                'jarak_masuk' => $absensiHariIni->jarak_masuk,
                'jarak_keluar' => $absensiHariIni->jarak_keluar,

                'face_score_masuk' =>
                    $absensiHariIni->face_score_masuk,

                'face_score_keluar' =>
                    $absensiHariIni->face_score_keluar,
            ] : null,

            'jadwal_hari_ini' => $jadwalHariIni ? [
                'id' => $jadwalHariIni->id,
                'nama' => $jadwalHariIni->nama,
                'hari' => $jadwalHariIni->hari,
                'jam_masuk' => $jadwalHariIni->jam_masuk,
                'jam_pulang' => $jadwalHariIni->jam_pulang,
                'toleransi_menit' => $jadwalHariIni->toleransi_menit,
                'status' => $jadwalHariIni->status,
            ] : null,

            'presensi_bulan_ini' => [
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'dinas' => $dinas,
                'alpha' => $alpha,
            ],
        ]);
    }
}