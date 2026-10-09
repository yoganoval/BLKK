<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    /**
     * Daftar kelas.
     */
    public function index()
    {
        $kelass = Kelas::withCount([
            'pendaftarans as peserta_aktif_count' => function ($query) {
                $query->where('status', 'aktif');
            },
        ])
            ->orderBy('nama')
            ->get();

        return Inertia::render('Kelas/Index', [
            'kelass' => $kelass,
        ]);
    }

    /**
     * Form tambah kelas.
     */
    public function create()
    {
        return Inertia::render('Kelas/Create');
    }

    /**
     * Simpan kelas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:kelas,nama',
            ],
            'durasi_hari' => [
                'required',
                'integer',
                'min:1',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'nama.required' => 'Nama kelas wajib diisi.',
            'nama.unique' => 'Nama kelas sudah digunakan.',
            'durasi_hari.required' => 'Durasi kelas wajib diisi.',
            'durasi_hari.integer' => 'Durasi harus berupa angka.',
            'durasi_hari.min' => 'Durasi minimal 1 hari.',
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Detail kelas.
     */
    public function show(Kelas $kela)
    {
        $kela->load([
            'pendaftarans.peserta',
            'pendaftarans.programPelatihan.pelatihan',
            'pendaftarans.programPelatihan.tahap',
        ]);

        return Inertia::render('Kelas/Show', [
            'kelas' => $kela,
        ]);
    }

    /**
     * Form edit.
     */
    public function edit(Kelas $kelas)
    {
        return Inertia::render('Kelas/Edit', [
            'kelas' => $kelas,
        ]);
    }

    /**
     * Update kelas.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:kelas,nama,' . $kelas->id,
            ],
            'durasi_hari' => [
                'required',
                'integer',
                'min:1',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'nama.required' => 'Nama kelas wajib diisi.',
            'nama.unique' => 'Nama kelas sudah digunakan.',
            'durasi_hari.required' => 'Durasi kelas wajib diisi.',
            'durasi_hari.integer' => 'Durasi harus berupa angka.',
            'durasi_hari.min' => 'Durasi minimal 1 hari.',
        ]);

        $kelas->update($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Hapus kelas.
     */
    public function destroy(Kelas $kela)
    {
        if ($kela->pendaftarans()->exists()) {
            return back()->with(
                'error',
                'Kelas tidak dapat dihapus karena sudah digunakan pada pendaftaran.'
            );
        }

        if ($kela->jadwals()->exists()) {
            return back()->with(
                'error',
                'Kelas tidak dapat dihapus karena sudah digunakan pada jadwal.'
            );
        }

        $kela->delete();

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}