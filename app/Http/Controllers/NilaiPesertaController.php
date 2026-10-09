<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KomponenNilai;
use App\Models\NilaiPeserta;
use App\Models\Peserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NilaiPesertaController extends Controller
{
    /**
     * Menampilkan daftar nilai peserta.
     */
    public function index(Request $request): Response
    {
        $query = NilaiPeserta::with([
            'peserta',
            'komponenNilai.kelas',
            'penilai',
        ]);

        if ($request->filled('peserta_id')) {
            $query->where(
                'peserta_id',
                $request->peserta_id
            );
        }

        if ($request->filled('komponen_nilai_id')) {
            $query->where(
                'komponen_nilai_id',
                $request->komponen_nilai_id
            );
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas(
                'komponenNilai',
                function ($q) use ($request) {
                    $q->where(
                        'kelas_id',
                        $request->kelas_id
                    );
                }
            );
        }

        $nilai = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render(
            'NilaiPeserta/Index',
            [
                'nilai' => $nilai,

                'peserta' => Peserta::orderBy(
                    'nama'
                )->get(),

                'komponen' => KomponenNilai::with(
                    'kelas'
                )
                ->orderBy('urutan')
                ->get(),

                'kelas' => Kelas::orderBy(
                    'nama'
                )->get(),

                'filters' => [
                    'peserta_id' =>
                        $request->peserta_id,

                    'komponen_nilai_id' =>
                        $request->komponen_nilai_id,

                    'kelas_id' =>
                        $request->kelas_id,
                ],
            ]
        );
    }

    /**
     * Form input nilai.
     */
    public function create(): Response
    {
        return Inertia::render(
            'NilaiPeserta/Create',
            [
                'peserta' => Peserta::orderBy(
                    'nama'
                )->get(),

                'komponen' => KomponenNilai::with(
                    'kelas'
                )
                ->orderBy('kelas_id')
                ->orderBy('urutan')
                ->get(),
            ]
        );
    }

    /**
     * Simpan nilai peserta.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'peserta_id' => [
                'required',
                'exists:peserta,id',
            ],

            'komponen_nilai_id' => [
                'required',
                'exists:komponen_nilai,id',
            ],

            'nilai' => [
                'required',
                'numeric',
                'min:0',
            ],

            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $komponen = KomponenNilai::findOrFail(
            $validated['komponen_nilai_id']
        );

        /*
         * Nilai tidak boleh melebihi nilai maksimal.
         */
        if (
            $validated['nilai']
            > $komponen->nilai_maksimal
        ) {
            return back()
                ->withErrors([
                    'nilai' =>
                        'Nilai tidak boleh lebih dari ' .
                        $komponen->nilai_maksimal . '.',
                ])
                ->withInput();
        }

        /*
         * Satu peserta hanya boleh mempunyai
         * satu nilai untuk satu komponen.
         */
        $nilaiPeserta = NilaiPeserta::updateOrCreate(
            [
                'peserta_id' =>
                    $validated['peserta_id'],

                'komponen_nilai_id' =>
                    $validated['komponen_nilai_id'],
            ],
            [
                'nilai' =>
                    $validated['nilai'],

                'catatan' =>
                    $validated['catatan'] ?? null,

                'dinilai_oleh' =>
                    Auth::id(),
            ]
        );

        return redirect()
            ->route('nilai-peserta.index')
            ->with(
                'success',
                'Nilai peserta berhasil disimpan.'
            );
    }

    /**
     * Detail nilai.
     */
    public function show(
        NilaiPeserta $nilaiPeserta
    ): Response {
        $nilaiPeserta->load([
            'peserta',
            'komponenNilai.kelas',
            'penilai',
        ]);

        return Inertia::render(
            'NilaiPeserta/Show',
            [
                'nilai' => $nilaiPeserta,
            ]
        );
    }

    /**
     * Form edit nilai.
     */
    public function edit(
        NilaiPeserta $nilaiPeserta
    ): Response {
        $nilaiPeserta->load([
            'peserta',
            'komponenNilai.kelas',
        ]);

        return Inertia::render(
            'NilaiPeserta/Edit',
            [
                'nilai' => $nilaiPeserta,

                'peserta' => Peserta::orderBy(
                    'nama'
                )->get(),

                'komponen' => KomponenNilai::with(
                    'kelas'
                )
                ->orderBy('kelas_id')
                ->orderBy('urutan')
                ->get(),
            ]
        );
    }

    /**
     * Update nilai peserta.
     */
    public function update(
        Request $request,
        NilaiPeserta $nilaiPeserta
    ): RedirectResponse {
        $validated = $request->validate([
            'nilai' => [
                'required',
                'numeric',
                'min:0',
            ],

            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $komponen = $nilaiPeserta
            ->komponenNilai;

        if (
            $validated['nilai']
            > $komponen->nilai_maksimal
        ) {
            return back()
                ->withErrors([
                    'nilai' =>
                        'Nilai tidak boleh lebih dari ' .
                        $komponen->nilai_maksimal . '.',
                ])
                ->withInput();
        }

        $nilaiPeserta->update([
            'nilai' =>
                $validated['nilai'],

            'catatan' =>
                $validated['catatan'] ?? null,

            'dinilai_oleh' =>
                Auth::id(),
        ]);

        return redirect()
            ->route('nilai-peserta.index')
            ->with(
                'success',
                'Nilai peserta berhasil diperbarui.'
            );
    }

    /**
     * Hapus nilai.
     */
    public function destroy(
        NilaiPeserta $nilaiPeserta
    ): RedirectResponse {
        $nilaiPeserta->delete();

        return redirect()
            ->route('nilai-peserta.index')
            ->with(
                'success',
                'Nilai peserta berhasil dihapus.'
            );
    }
}