<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PelatihanController extends Controller
{
    /**
     * Menampilkan daftar pelatihan.
     */
    public function index()
    {
        $pelatihans = Pelatihan::withCount('programPelatihans')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Pelatihan/Index', [
            'pelatihans' => $pelatihans,
        ]);
    }

    /**
     * Form tambah pelatihan.
     */
    public function create()
    {
        return Inertia::render('Pelatihan/Create');
    }

    /**
     * Simpan pelatihan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:pelatihan,nama',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'nama.required' => 'Nama pelatihan wajib diisi.',
            'nama.unique' => 'Nama pelatihan sudah digunakan.',
        ]);

        Pelatihan::create($validated);

        return redirect()
            ->route('pelatihans.index')
            ->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail pelatihan.
     */
    public function show(Pelatihan $pelatihan)
    {
        $pelatihan->load([
            'programPelatihans.tahap',
            'programPelatihans' => function ($query) {
                $query->withCount([
                    'pendaftarans as peserta_aktif_count' => function ($q) {
                        $q->where('status', 'aktif');
                    },
                ]);
            },
        ]);

        return Inertia::render('Pelatihan/Show', [
            'pelatihan' => $pelatihan,
        ]);
    }

    /**
     * Form edit pelatihan.
     */
    public function edit(Pelatihan $pelatihan)
    {
        return Inertia::render('Pelatihan/Edit', [
            'pelatihan' => $pelatihan,
        ]);
    }

    /**
     * Update pelatihan.
     */
    public function update(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:pelatihan,nama,' . $pelatihan->id,
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'nama.required' => 'Nama pelatihan wajib diisi.',
            'nama.unique' => 'Nama pelatihan sudah digunakan.',
        ]);

        $pelatihan->update($validated);

        return redirect()
            ->route('pelatihans.index')
            ->with('success', 'Pelatihan berhasil diperbarui.');
    }

    /**
     * Hapus pelatihan.
     */
    public function destroy(Pelatihan $pelatihan)
    {
        if ($pelatihan->programPelatihans()->exists()) {
            return back()->with(
                'error',
                'Pelatihan tidak dapat dihapus karena sudah memiliki program pelatihan.'
            );
        }

        $pelatihan->delete();

        return redirect()
            ->route('pelatihan.index')
            ->with('success', 'Pelatihan berhasil dihapus.');
    }
}