<?php

namespace App\Http\Controllers;

use App\Models\LokasiAbsensi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LokasiAbsensiController extends Controller
{
    public function index()
    {
        $lokasi = LokasiAbsensi::query()
            ->orderBy('nama')
            ->get();

        return Inertia::render('LokasiAbsensi/Index', [
            'lokasi' => $lokasi,
        ]);
    }

    public function create()
    {
        return Inertia::render('LokasiAbsensi/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'radius' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],

            'status' => [
                'required',
                'in:aktif,nonaktif',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        LokasiAbsensi::create($validated);

        return redirect()
            ->route('lokasi-absensi.index')
            ->with(
                'success',
                'Lokasi absensi berhasil ditambahkan.'
            );
    }

    public function show(LokasiAbsensi $lokasiAbsensi)
    {
        return Inertia::render('LokasiAbsensi/Show', [
            'lokasi' => $lokasiAbsensi,
        ]);
    }

    public function edit(LokasiAbsensi $lokasiAbsensi)
    {
        return Inertia::render('LokasiAbsensi/Edit', [
            'lokasi' => $lokasiAbsensi,
        ]);
    }

    public function update(
        Request $request,
        LokasiAbsensi $lokasiAbsensi
    ) {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'radius' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],

            'status' => [
                'required',
                'in:aktif,nonaktif',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $lokasiAbsensi->update($validated);

        return redirect()
            ->route('lokasi-absensi.index')
            ->with(
                'success',
                'Lokasi absensi berhasil diperbarui.'
            );
    }

    public function destroy(LokasiAbsensi $lokasiAbsensi)
    {
        $lokasiAbsensi->delete();

        return redirect()
            ->route('lokasi-absensi.index')
            ->with(
                'success',
                'Lokasi absensi berhasil dihapus.'
            );
    }
}