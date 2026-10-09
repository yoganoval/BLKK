<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HariLiburController extends Controller
{
    public function index()
    {
        $hariLibur = HariLibur::query()
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('HariLibur/Index', [
            'hariLibur' => $hariLibur,
        ]);
    }

    public function create()
    {
        return Inertia::render('HariLibur/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => [
                'required',
                'date',
                'unique:hari_libur,tanggal',
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        HariLibur::create($validated);

        return redirect()
            ->route('hari-libur.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function show(HariLibur $hariLibur)
    {
        return Inertia::render('HariLibur/Show', [
            'hariLibur' => $hariLibur,
        ]);
    }

    public function edit(HariLibur $hariLibur)
    {
        return Inertia::render('HariLibur/Edit', [
            'hariLibur' => $hariLibur,
        ]);
    }

    public function update(Request $request, HariLibur $hariLibur)
    {
        $validated = $request->validate([
            'tanggal' => [
                'required',
                'date',
                'unique:hari_libur,tanggal,' . $hariLibur->id,
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $hariLibur->update($validated);

        return redirect()
            ->route('hari-libur.index')
            ->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(HariLibur $hariLibur)
    {
        $hariLibur->delete();

        return redirect()
            ->route('hari-libur.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}