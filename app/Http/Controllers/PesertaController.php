<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PesertaController extends Controller
{
    /**
     * Daftar peserta.
     */
    public function index()
    {
        $pesertas = Peserta::with([
            'pesertaFace',
        ])
            ->withCount([
                'pendaftarans',
                'pendaftarans as pendaftaran_aktif_count' => function ($query) {
                    $query->where('status', 'aktif');
                },
            ])
            ->orderBy('nama')
            ->get();

        return Inertia::render('Peserta/Index', [
            'pesertas' => $pesertas,
        ]);
    }

    /**
     * Form tambah peserta.
     */
    public function create()
    {
        return Inertia::render('Peserta/Create');
    }

    /**
     * Simpan peserta.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
            'kamar' => [
                'nullable',
                'string',
                'max:100',
            ],
            'lembaga' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'nama.required' => 'Nama peserta wajib diisi.',
        ]);

        Peserta::create($validated);

        return redirect()
            ->route('pesertas.index')
            ->with('success', 'Peserta berhasil ditambahkan.');
    }

    /**
     * Detail peserta.
     */
    public function show(Peserta $peserta)
    {
        $peserta->load([
            'pesertaFace',
            'pendaftarans.programPelatihan.pelatihan',
            'pendaftarans.programPelatihan.tahap',
            'pendaftarans.kelas',
            'pendaftarans.pembayarans',
            'pendaftarans.absensis.jadwal',
        ]);

        return Inertia::render('Peserta/Show', [
            'peserta' => $peserta,
        ]);
    }

    /**
     * Form edit.
     */
    public function edit(Peserta $peserta)
    {
        return Inertia::render('Peserta/Edit', [
            'peserta' => $peserta,
        ]);
    }

    /**
     * Update peserta.
     */
    public function update(Request $request, Peserta $peserta)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
            'kamar' => [
                'nullable',
                'string',
                'max:100',
            ],
            'lembaga' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'nama.required' => 'Nama peserta wajib diisi.',
        ]);

        $peserta->update($validated);

        return redirect()
            ->route('pesertas.index')
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * Hapus peserta.
     */
    public function destroy(Peserta $peserta)
    {
        if ($peserta->pendaftarans()->exists()) {
            return back()->with(
                'error',
                'Peserta tidak dapat dihapus karena sudah memiliki data pendaftaran.'
            );
        }

        if ($peserta->pesertaFace()->exists()) {
            return back()->with(
                'error',
                'Peserta tidak dapat dihapus karena masih memiliki data wajah.'
            );
        }

        $peserta->delete();

        return redirect()
            ->route('pesertas.index')
            ->with('success', 'Peserta berhasil dihapus.');
    }
}