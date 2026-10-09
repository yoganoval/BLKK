<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\NilaiPeserta;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PenilaianController extends Controller
{
    public function index(Request $request): Response
    {
        $kelasId = $request->integer('kelas_id');
        $komponenId = $request->integer(
            'komponen_nilai_id'
        );

        $peserta = collect();
        $nilai = collect();

        if ($kelasId) {
            /*
             * Sesuaikan relasi peserta dengan struktur
             * tabel BLKK Anda.
             *
             * Jika peserta memiliki kelas_id:
             */
            $peserta = Peserta::where(
                'kelas_id',
                $kelasId
            )
            ->orderBy('nama')
            ->get();
        }

        if ($kelasId && $komponenId) {
            $nilai = NilaiPeserta::where(
                'komponen_nilai_id',
                $komponenId
            )
            ->whereIn(
                'peserta_id',
                $peserta->pluck('id')
            )
            ->get();
        }

        return Inertia::render(
            'Penilaian/Index',
            [
                'kelas' => Kelas::orderBy(
                    'nama'
                )->get(),

                'komponen' => KomponenNilai::orderBy(
                    'urutan'
                )->get(),

                'peserta' => $peserta,

                'nilai' => $nilai,

                'filters' => [
                    'kelas_id' =>
                        $kelasId ?: null,

                    'komponen_nilai_id' =>
                        $komponenId ?: null,
                ],
            ]
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'komponen_nilai_id' => [
                'required',
                'exists:komponen_nilai,id',
            ],

            'nilai' => [
                'required',
                'array',
            ],

            'catatan' => [
                'nullable',
                'array',
            ],
        ]);

        $komponen = KomponenNilai::findOrFail(
            $validated['komponen_nilai_id']
        );

        /*
         * Pastikan komponen memang milik
         * kelas yang dipilih.
         */
        if (
            $komponen->kelas_id
            != $validated['kelas_id']
        ) {
            return back()->with(
                'error',
                'Komponen nilai tidak sesuai dengan kelas.'
            );
        }

        /*
         * Ambil peserta yang memang terdaftar
         * pada kelas tersebut.
         */
        $pesertaIds = Peserta::where(
            'kelas_id',
            $validated['kelas_id']
        )
        ->pluck('id')
        ->toArray();

        DB::transaction(function () use (
            $validated,
            $komponen,
            $pesertaIds
        ) {
            foreach (
                $validated['nilai']
                as $pesertaId => $nilai
            ) {

                /*
                 * Jangan simpan peserta yang bukan
                 * anggota kelas.
                 */
                if (
                    !in_array(
                        (int) $pesertaId,
                        $pesertaIds,
                        true
                    )
                ) {
                    continue;
                }

                /*
                 * Nilai kosong tidak diproses.
                 */
                if (
                    $nilai === null ||
                    $nilai === ''
                ) {
                    continue;
                }

                $nilai = (float) $nilai;

                /*
                 * Jangan melebihi nilai maksimal.
                 */
                if (
                    $nilai < 0 ||
                    $nilai >
                        (float)
                        $komponen->nilai_maksimal
                ) {
                    continue;
                }

                NilaiPeserta::updateOrCreate(
                    [
                        'peserta_id' =>
                            $pesertaId,

                        'komponen_nilai_id' =>
                            $komponen->id,
                    ],
                    [
                        'nilai' =>
                            $nilai,

                        'catatan' =>
                            $validated['catatan'][
                                $pesertaId
                            ] ?? null,

                        'dinilai_oleh' =>
                            Auth::id(),
                    ]
                );
            }
        });

        return redirect()
            ->route(
                'penilaian.index',
                [
                    'kelas_id' =>
                        $validated['kelas_id'],

                    'komponen_nilai_id' =>
                        $validated[
                            'komponen_nilai_id'
                        ],
                ]
            )
            ->with(
                'success',
                'Nilai seluruh peserta berhasil disimpan.'
            );
    }
}