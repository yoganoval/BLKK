<?php

namespace App\Http\Controllers;

use App\Models\LpjKeuangan;
use App\Models\LpjPengeluaran;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LpjKeuanganController extends Controller
{
    /**
     * Menampilkan daftar LPJ
     */
    public function index()
    {
        $lpj = LpjKeuangan::withCount('pengeluaran')
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->total_pemasukan = $this->totalPemasukan($item);
                $item->total_pengeluaran = $item->pengeluaran()->sum('jumlah');
                $item->saldo =
                    $item->total_pemasukan -
                    $item->total_pengeluaran;

                return $item;
            });

        return Inertia::render('LpjKeuangan/Index', [
            'lpj' => $lpj,
        ]);
    }

    /**
     * Form membuat LPJ
     */
    public function create()
    {
        return Inertia::render('LpjKeuangan/Create');
    }

    /**
     * Simpan LPJ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_lpj' => [
                'required',
                'string',
                'max:100',
                'unique:lpj_keuangan,kode_lpj',
            ],

            'periode_mulai' => [
                'required',
                'date',
            ],

            'periode_selesai' => [
                'required',
                'date',
                'after_or_equal:periode_mulai',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['status'] = 'draft';

        $lpj = LpjKeuangan::create($validated);

        return redirect()
            ->route('lpj-keuangan.show', $lpj->id)
            ->with('success', 'LPJ berhasil dibuat.');
    }

    /**
     * Detail LPJ
     */
    public function show(LpjKeuangan $lpjKeuangan)
    {
        $lpjKeuangan->load([
            'pengeluaran' => function ($query) {
                $query->latest('tanggal');
            },
            'dibuatOleh',
            'disetujuiOleh',
        ]);

        $totalPemasukan = $this->totalPemasukan($lpjKeuangan);

        $totalPengeluaran = $lpjKeuangan
            ->pengeluaran
            ->sum('jumlah');

        $saldo = $totalPemasukan - $totalPengeluaran;

        return Inertia::render('LpjKeuangan/Show', [
            'lpj' => [
                'id' => $lpjKeuangan->id,
                'kode_lpj' => $lpjKeuangan->kode_lpj,
                'periode_mulai' => $lpjKeuangan->periode_mulai?->format('Y-m-d'),
                'periode_selesai' => $lpjKeuangan->periode_selesai?->format('Y-m-d'),
                'status' => $lpjKeuangan->status,
                'keterangan' => $lpjKeuangan->keterangan,

                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo' => $saldo,

                'pengeluaran' => $lpjKeuangan->pengeluaran,

                'dibuat_oleh' => $lpjKeuangan->dibuatOleh?->name,
                'disetujui_oleh' => $lpjKeuangan->disetujuiOleh?->name,

                'tanggal_pengajuan' =>
                    $lpjKeuangan->tanggal_pengajuan?->format('Y-m-d H:i:s'),

                'tanggal_pengesahan' =>
                    $lpjKeuangan->tanggal_pengesahan?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Form edit LPJ
     */
    public function edit(LpjKeuangan $lpjKeuangan)
    {
        return Inertia::render('LpjKeuangan/Edit', [
            'lpj' => $lpjKeuangan,
        ]);
    }

    /**
     * Update LPJ
     */
    public function update(
        Request $request,
        LpjKeuangan $lpjKeuangan
    ) {
        $validated = $request->validate([
            'kode_lpj' => [
                'required',
                'string',
                'max:100',
                'unique:lpj_keuangan,kode_lpj,' . $lpjKeuangan->id,
            ],

            'periode_mulai' => [
                'required',
                'date',
            ],

            'periode_selesai' => [
                'required',
                'date',
                'after_or_equal:periode_mulai',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $lpjKeuangan->update($validated);

        return redirect()
            ->route('lpj-keuangan.show', $lpjKeuangan->id)
            ->with('success', 'LPJ berhasil diperbarui.');
    }

    /**
     * Hapus LPJ
     */
    public function destroy(LpjKeuangan $lpjKeuangan)
    {
        DB::transaction(function () use ($lpjKeuangan) {

            foreach ($lpjKeuangan->pengeluaran as $pengeluaran) {
                if ($pengeluaran->bukti_path) {
                    Storage::disk('public')->delete(
                        $pengeluaran->bukti_path
                    );
                }
            }

            $lpjKeuangan->delete();
        });

        return redirect()
            ->route('lpj-keuangan.index')
            ->with('success', 'LPJ berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | PENGELUARAN
    |--------------------------------------------------------------------------
    */

    /**
     * Tambah pengeluaran
     */
    public function storePengeluaran(Request $request, LpjKeuangan $lpjKeuangan)
    {
        $validated = $request->validate([
            'tanggal' => [
                'required',
                'date',
            ],

            'kategori' => [
                'required',
                'string',
                'max:100',
            ],

            'uraian' => [
                'required',
                'string',
                'max:255',
            ],

            'nomor_bukti' => [
                'nullable',
                'string',
                'max:100',
            ],

            'jumlah' => [
                'required',
                'numeric',
                'min:0',
            ],

            'metode_pembayaran' => [
                'required',
                'in:cash,transfer,qris',
            ],

            'penerima' => [
                'nullable',
                'string',
                'max:255',
            ],

            'bukti' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $validated,
            $lpjKeuangan
        ) {

            /*
             * Upload bukti
             */
            if ($request->hasFile('bukti')) {

                $file = $request->file('bukti');

                $path = $file->store(
                    'lpj/bukti/' . now()->format('Y/m'),
                    'public'
                );

                $validated['bukti_path'] = $path;
                $validated['bukti_nama'] = $file->getClientOriginalName();
                $validated['bukti_mime'] = $file->getMimeType();
                $validated['bukti_ukuran'] = $file->getSize();
            }

            unset($validated['bukti']);

            $validated['lpj_keuangan_id'] =
                $lpjKeuangan->id;

            LpjPengeluaran::create($validated);
        });

        return back()
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Update pengeluaran
     */
    public function updatePengeluaran(
        Request $request,
        LpjPengeluaran $pengeluaran
    ) {
        $validated = $request->validate([
            'tanggal' => [
                'required',
                'date',
            ],

            'kategori' => [
                'required',
                'string',
                'max:100',
            ],

            'uraian' => [
                'required',
                'string',
                'max:255',
            ],

            'nomor_bukti' => [
                'nullable',
                'string',
                'max:100',
            ],

            'jumlah' => [
                'required',
                'numeric',
                'min:0',
            ],

            'metode_pembayaran' => [
                'required',
                'in:cash,transfer,qris',
            ],

            'penerima' => [
                'nullable',
                'string',
                'max:255',
            ],

            'bukti' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $validated,
            $pengeluaran
        ) {

            if ($request->hasFile('bukti')) {

                /*
                 * Hapus bukti lama
                 */
                if ($pengeluaran->bukti_path) {
                    Storage::disk('public')->delete(
                        $pengeluaran->bukti_path
                    );
                }

                $file = $request->file('bukti');

                $path = $file->store(
                    'lpj/bukti/' . now()->format('Y/m'),
                    'public'
                );

                $validated['bukti_path'] = $path;
                $validated['bukti_nama'] = $file->getClientOriginalName();
                $validated['bukti_mime'] = $file->getMimeType();
                $validated['bukti_ukuran'] = $file->getSize();
            }

            unset($validated['bukti']);

            $pengeluaran->update($validated);
        });

        return back()
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Hapus pengeluaran
     */
    public function destroyPengeluaran(
        LpjPengeluaran $pengeluaran
    ) {
        DB::transaction(function () use ($pengeluaran) {

            if ($pengeluaran->bukti_path) {
                Storage::disk('public')->delete(
                    $pengeluaran->bukti_path
                );
            }

            $pengeluaran->delete();
        });

        return back()
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS LPJ
    |--------------------------------------------------------------------------
    */

    /**
     * Ajukan LPJ
     */
    public function ajukan(LpjKeuangan $lpjKeuangan)
    {
        $lpjKeuangan->update([
            'status' => 'diajukan',
            'tanggal_pengajuan' => now(),
        ]);

        return back()
            ->with('success', 'LPJ berhasil diajukan.');
    }

    /**
     * Setujui LPJ
     */
    public function setujui(LpjKeuangan $lpjKeuangan)
    {
        $lpjKeuangan->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_pengesahan' => now(),
        ]);

        return back()
            ->with('success', 'LPJ berhasil disetujui.');
    }

    /**
     * Tolak LPJ
     */
    public function tolak(LpjKeuangan $lpjKeuangan)
    {
        $lpjKeuangan->update([
            'status' => 'ditolak',
        ]);

        return back()
            ->with('success', 'LPJ ditolak.');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung total pemasukan berdasarkan pembayaran lunas.
     */
    private function totalPemasukan(LpjKeuangan $lpj): float
    {
        return (float) Pembayaran::query()
            ->where('status', 'lunas')
            ->whereBetween('tanggal_bayar', [
                $lpj->periode_mulai->startOfDay(),
                $lpj->periode_selesai->endOfDay(),
            ])
            ->sum('jumlah');
    }
}