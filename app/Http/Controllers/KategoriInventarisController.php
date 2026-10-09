<?php

namespace App\Http\Controllers;

use App\Models\KategoriInventaris;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KategoriInventarisController extends Controller
{
    public function index()
    {
        $kategori = KategoriInventaris::withCount('inventaris')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('KategoriInventaris/Index', [
            'kategori' => $kategori,
        ]);
    }

    public function create()
    {
        return Inertia::render('KategoriInventaris/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:kategori_inventaris,nama',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        KategoriInventaris::create($validated);

        return redirect()
            ->route('kategori-inventaris.index')
            ->with(
                'success',
                'Kategori inventaris berhasil ditambahkan.'
            );
    }

    public function show(KategoriInventaris $kategoriInventaris)
    {
        $kategoriInventaris->load('inventaris');

        return Inertia::render(
            'KategoriInventaris/Show',
            [
                'kategori' => $kategoriInventaris,
            ]
        );
    }

    public function edit(KategoriInventaris $kategoriInventaris)
    {
        return Inertia::render(
            'KategoriInventaris/Edit',
            [
                'kategori' => $kategoriInventaris,
            ]
        );
    }

    public function update(
        Request $request,
        KategoriInventaris $kategoriInventaris
    ) {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:kategori_inventaris,nama,' .
                    $kategoriInventaris->id,
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $kategoriInventaris->update($validated);

        return redirect()
            ->route('kategori-inventaris.index')
            ->with(
                'success',
                'Kategori inventaris berhasil diperbarui.'
            );
    }

    public function destroy(
        KategoriInventaris $kategoriInventaris
    ) {
        if ($kategoriInventaris->inventaris()->exists()) {
            return back()->with(
                'error',
                'Kategori tidak dapat dihapus karena masih digunakan oleh inventaris.'
            );
        }

        $kategoriInventaris->delete();

        return redirect()
            ->route('kategori-inventaris.index')
            ->with(
                'success',
                'Kategori inventaris berhasil dihapus.'
            );
    }
}