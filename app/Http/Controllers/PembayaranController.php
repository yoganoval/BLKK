<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PembayaranController extends Controller
{
    /**
     * Daftar pembayaran.
     */
    public function index()
    {
        $pembayarans = Pembayaran::with([
            'pendaftaran.peserta',
            'pendaftaran.programPelatihan.pelatihan',
            'pendaftaran.programPelatihan.tahap',
            'pendaftaran.kelas',
        ])
            ->orderByDesc('created_at')
            ->get();


        return Inertia::render('Pembayaran/Index', [
            'pembayarans' => $pembayarans,
        ]);
    }


    /**
     * Form pembayaran.
     */
    public function create()
    {
        $pendaftarans = Pendaftaran::with([
            'peserta',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])
            ->whereIn('status', [
                'aktif',
                'selesai',
            ])
            ->orderByDesc('id')
            ->get();


        return Inertia::render('Pembayaran/Create', [
            'pendaftarans' => $pendaftarans,
        ]);
    }


    /**
     * Simpan pembayaran.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pendaftaran_id' => [
                'required',
                'exists:pendaftaran,id',
            ],

            'jumlah' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tanggal_bayar' => [
                'nullable',
                'date',
            ],

            'metode' => [
                'nullable',
                'string',
                'max:50',
            ],

            'status' => [
                'required',
                'in:pending,lunas,ditolak',
            ],

            'bukti' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload bukti pembayaran
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request
                ->file('bukti')
                ->store('bukti-pembayaran', 'public');
        }


        Pembayaran::create($validated);


        return redirect()
            ->route('pembayarans.index')
            ->with('success', 'Pembayaran berhasil ditambahkan.');
    }


    /**
     * Detail pembayaran.
     */
    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load([
            'pendaftaran.peserta',
            'pendaftaran.programPelatihan.pelatihan',
            'pendaftaran.programPelatihan.tahap',
            'pendaftaran.kelas',
        ]);


        return Inertia::render('Pembayaran/Show', [
            'pembayaran' => $pembayaran,
        ]);
    }


    /**
     * Form edit pembayaran.
     */
    public function edit(Pembayaran $pembayaran)
    {
        $pembayaran->load([
            'pendaftaran.peserta',
            'pendaftaran.programPelatihan.pelatihan',
            'pendaftaran.programPelatihan.tahap',
            'pendaftaran.kelas',
        ]);


        $pendaftarans = Pendaftaran::with([
            'peserta',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])
            ->whereIn('status', [
                'aktif',
                'selesai',
            ])
            ->orderByDesc('id')
            ->get();


        return Inertia::render('Pembayaran/Edit', [
            'pembayaran' => $pembayaran,
            'pendaftarans' => $pendaftarans,
        ]);
    }


    /**
     * Update pembayaran.
     */
    public function update(
        Request $request,
        Pembayaran $pembayaran
    ) {
        $validated = $request->validate([
            'pendaftaran_id' => [
                'required',
                'exists:pendaftaran,id',
            ],

            'jumlah' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tanggal_bayar' => [
                'nullable',
                'date',
            ],

            'metode' => [
                'nullable',
                'string',
                'max:50',
            ],

            'status' => [
                'required',
                'in:pending,lunas,ditolak',
            ],

            'bukti' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload bukti baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('bukti')) {

            if (
                $pembayaran->bukti &&
                Storage::disk('public')->exists($pembayaran->bukti)
            ) {
                Storage::disk('public')->delete(
                    $pembayaran->bukti
                );
            }


            $validated['bukti'] = $request
                ->file('bukti')
                ->store('bukti-pembayaran', 'public');
        }


        $pembayaran->update($validated);


        return redirect()
            ->route('pembayarans.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }


    /**
     * Hapus pembayaran.
     */
    public function destroy(Pembayaran $pembayaran)
    {
        if (
            $pembayaran->bukti &&
            Storage::disk('public')->exists($pembayaran->bukti)
        ) {
            Storage::disk('public')->delete(
                $pembayaran->bukti
            );
        }


        $pembayaran->delete();


        return redirect()
            ->route('pembayarans.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
}