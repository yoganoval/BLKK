<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\NilaiPeserta;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PesertaDashboardController extends Controller
{
    public function index()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        // Ambil seluruh pendaftaran milik peserta yang login.
        $pendaftarans = $peserta->pendaftarans()
            ->with([
                'programPelatihan.pelatihan',
                'programPelatihan.tahap',
                'kelas',
            ])
            ->get();

        // Susun daftar pelatihan yang diikuti.
        $pelatihanDiikuti = $pendaftarans
            ->map(function ($pendaftaran) {
                $program = $pendaftaran->programPelatihan;

                return [
                    'pendaftaran_id' => $pendaftaran->id,
                    'nama_pelatihan' =>
                        $program?->pelatihan?->nama ?? '-',
                    'tahap' =>
                        $program?->tahap?->nama ?? '-',
                    'kelas' =>
                        $pendaftaran->kelas?->nama ?? '-',
                    'status' =>
                        $pendaftaran->status ?? '-',
                ];
            })
            ->values();


            // Ambil nilai milik peserta yang sedang login.
            $nilai = NilaiPeserta::query()
                ->with('komponenNilai')
                ->where('peserta_id', $peserta->id)
                ->get()
                ->map(function ($item) use ($pendaftarans) {
                    $komponen = $item->komponenNilai;

                    // Cari pendaftaran peserta yang kelasnya cocok
                    // dengan kelas komponen penilaian.
                    $pendaftaranCocok = $pendaftarans->first(
                        function ($pendaftaran) use ($komponen) {
                            return $komponen
                                && (int) $pendaftaran->kelas_id
                                    === (int) $komponen->kelas_id;
                        }
                    );

                    $program = $pendaftaranCocok?->programPelatihan;

                    return [
                        'id' => $item->id,
                        'nama_pelatihan' =>
                            $program?->pelatihan?->nama ?? '-',
                        'tahap' =>
                            $program?->tahap?->nama ?? '-',
                        'komponen' => $komponen?->nama ?? '-',
                        'nilai' => $item->nilai,
                        'catatan' => $item->catatan,
                    ];
                })
                ->values();

        return Inertia::render('Peserta/Dashboard', [
            'peserta' => $peserta,
            'pelatihanDiikuti' => $pelatihanDiikuti,
            'nilai' => $nilai,
        ]);
    }
}
