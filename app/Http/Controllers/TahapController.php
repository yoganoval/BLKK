<?php

namespace App\Http\Controllers;

use App\Models\Tahap;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TahapController extends Controller
{
    /**
     * Daftar tahap.
     */
    public function index()
    {
        $tahaps = Tahap::withCount('programPelatihans')
            ->orderBy('periode')
            ->get();

        return Inertia::render('Tahap/Index', [
            'tahaps' => $tahaps,
        ]);
    }

    /**
     * Form tambah tahap.
     */
    public function create()
    {
        return Inertia::render('Tahap/Create');
    }

    /**
     * Simpan tahap.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'periode' => [
                'required',
                'integer',
                'min:2',
                // 'unique:tahap,urutan',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'nama.required' => 'Nama tahap wajib diisi.',
            'periode.required' => 'Periode tahap wajib diisi.',
            'periode.integer' => 'Periode harus berupa angka.',
            'periode.min' => 'Periode minimal 2.',
            // 'periode.unique' => 'Periode tahap sudah digunakan.',
        ]);

        Tahap::create($validated);

        return redirect()
            ->route('tahaps.index')
            ->with('success', 'Tahap berhasil ditambahkan.');
    }

    /**
     * Detail tahap.
     */
    public function show(Tahap $tahap)
    {
        $tahap->load([
            'programPelatihans.pelatihan',
        ]);

        return Inertia::render('Tahap/Show', [
            'tahap' => $tahap,
        ]);
    }

    /**
     * Form edit.
     */
    public function edit(Tahap $tahap)
    {
        return Inertia::render('Tahap/Edit', [
            'tahap' => $tahap,
        ]);
    }

    /**
     * Update tahap.
     */
    public function update(Request $request, Tahap $tahap)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'periode' => [
                'required',
                'integer',
                'min:1',
                // 'unique:tahap,periode,' . $tahap->id,
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'nama.required' => 'Nama tahap wajib diisi.',
            'periode.required' => 'Periode tahap wajib diisi.',
            'periode.integer' => 'Periode harus berupa angka.',
            'periode.min' => 'Periode minimal 2.',
            // 'periode.unique' => 'Periode tahap sudah digunakan.',
        ]);

        $tahap->update($validated);

        return redirect()
            ->route('tahaps.index')
            ->with('success', 'Tahap berhasil diperbarui.');
    }

    /**
     * Hapus tahap.
     */
    public function destroy(Tahap $tahap)
    {
        if ($tahap->programPelatihans()->exists()) {
            return back()->with(
                'error',
                'Tahap tidak dapat dihapus karena sudah digunakan pada program pelatihan.'
            );
        }

        $tahap->delete();

        return redirect()
            ->route('tahaps.index')
            ->with('success', 'Tahap berhasil dihapus.');
    }
}