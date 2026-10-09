<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\InventarisFile;
use App\Models\InventarisRiwayat;
use App\Models\KategoriInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $inventaris = Inventaris::with([
                'kategori',
                'files' => function ($query) {
                    $query->where('jenis', 'foto')
                        ->latest();
                },
            ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Inventaris/Index', [
            'inventaris' => $inventaris,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    public function create()
    {
        $kategori = KategoriInventaris::orderBy('nama')->get();

        return Inertia::render('Inventaris/Create', [
            'kategori' => $kategori,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // =========================
            // INFORMASI INVENTARIS
            // =========================
            'kode' => [
                'required',
                'string',
                'max:100',
                'unique:inventaris,kode',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kategori_id' => [
                'required',
                'exists:kategori_inventaris,id',
            ],

            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],

            'satuan' => [
                'required',
                'string',
                'max:50',
            ],

            'kondisi' => [
                'required',
                'in:baik,rusak_ringan,rusak_berat',
            ],

            'lokasi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tahun_perolehan' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . date('Y'),
            ],

            'harga_perolehan' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'sumber_dana' => [
                'nullable',
                'string',
                'max:255',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            // =========================
            // FILE
            // =========================
            'files' => [
                'nullable',
                'array',
            ],

            'files.*' => [
                'required',
                'array',
            ],

            'files.*.file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf',
            ],

            'files.*.jenis' => [
                'required',
                'in:foto,nota,dokumen',
            ],

            'files.*.keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($request, $validated) {

            // ==========================================
            // 1. SIMPAN DATA INVENTARIS
            // ==========================================
            $inventaris = Inventaris::create([
                'kode' => $validated['kode'],
                'nama' => $validated['nama'],
                'kategori_id' => $validated['kategori_id'],
                'jumlah' => $validated['jumlah'],
                'satuan' => $validated['satuan'],
                'kondisi' => $validated['kondisi'],
                'lokasi' => $validated['lokasi'] ?? null,
                'tahun_perolehan' => $validated['tahun_perolehan'] ?? null,
                'harga_perolehan' => $validated['harga_perolehan'] ?? null,
                'sumber_dana' => $validated['sumber_dana'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $inventaris->update([
                'barcode' => 'BLKK-' . str_pad($inventaris->id, 5, '0', STR_PAD_LEFT),
            ]);


            // ==========================================
            // 3. SIMPAN MULTIPLE FILE
            // ==========================================
            $files = $request->file('files', []);

            if (is_array($files)) {

                foreach ($files as $index => $fileData) {

                    // Pastikan struktur:
                    // files[0][file]
                    // files[1][file]
                    // dst.
                    if (
                        !is_array($fileData) ||
                        !isset($fileData['file']) ||
                        !$fileData['file'] instanceof \Illuminate\Http\UploadedFile
                    ) {
                        continue;
                    }

                    $file = $fileData['file'];

                    // Ambil metadata dari request biasa
                    $jenis = $request->input(
                        "files.$index.jenis",
                        'dokumen'
                    );

                    $keterangan = $request->input(
                        "files.$index.keterangan"
                    );

                    // Simpan file
                    $path = $file->store(
                        'inventaris',
                        'public'
                    );

                    // Simpan metadata ke database
                    InventarisFile::create([
                        'inventaris_id' => $inventaris->id,
                        'jenis' => $jenis,
                        'nama_file' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'keterangan' => $keterangan,
                    ]);
                }
            }

            // ==========================================
            // 4. SIMPAN RIWAYAT KONDISI AWAL
            // ==========================================
            InventarisRiwayat::create([
                'inventaris_id' => $inventaris->id,
                'tanggal' => now()->toDateString(),
                'kondisi' => $inventaris->kondisi,
                'keterangan' => 'Kondisi awal saat inventaris ditambahkan.',
                'biaya_perbaikan' => null,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('inventaris.index')
            ->with('success', 'Inventaris berhasil ditambahkan.');
    }

    public function show(Inventaris $inventaris)
    {
        $inventaris->load([
            'kategori',
            'files',
            'riwayat.user',
        ]);

        $inventaris->setRelation(
            'riwayat',
            $inventaris->riwayat->sortByDesc('tanggal')->values()
        );

        return Inertia::render('Inventaris/Show', [
            'inventaris' => $inventaris,
        ]);
    }

    public function edit(Inventaris $inventaris)
    {
        $inventaris->load([
            'kategori',
            'files' => function ($query) {
                $query->latest();
            },
        ]);

        return Inertia::render('Inventaris/Edit', [
            'inventaris' => $inventaris,
            'kategori' => KategoriInventaris::orderBy('nama')->get(),
        ]);
    }


    public function update(Request $request, Inventaris $inventaris)
    {
        $validated = $request->validate([
            // =========================
            // INFORMASI INVENTARIS
            // =========================
            'kode' => [
                'required',
                'string',
                'max:255',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kategori_id' => [
                'required',
                'exists:kategori_inventaris,id',
            ],

            'jumlah' => [
                'required',
                'numeric',
                'min:0',
            ],

            'satuan' => [
                'required',
                'string',
                'max:50',
            ],

            'kondisi' => [
                'required',
                'string',
                'max:50',
            ],

            'lokasi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tahun_perolehan' => [
                'nullable',
                'integer',
            ],

            'harga_perolehan' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'sumber_dana' => [
                'nullable',
                'string',
                'max:255',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            // =========================
            // FOTO
            // =========================
            'foto' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            // =========================
            // NOTA
            // =========================
            'nota' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],

            // =========================
            // DOKUMEN
            // =========================
            'dokumen' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $inventaris,
            $validated
        ) {

            // ==========================================
            // 1. UPDATE DATA INVENTARIS
            // ==========================================

            $inventaris->update([
                'kode' => $validated['kode'],
                'nama' => $validated['nama'],
                'kategori_id' => $validated['kategori_id'],
                'jumlah' => $validated['jumlah'],
                'satuan' => $validated['satuan'],
                'kondisi' => $validated['kondisi'],
                'lokasi' => $validated['lokasi'] ?? null,
                'tahun_perolehan' => $validated['tahun_perolehan'] ?? null,
                'harga_perolehan' => $validated['harga_perolehan'] ?? null,
                'sumber_dana' => $validated['sumber_dana'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // ==========================================
            // 2. FUNGSI REPLACE FILE
            // ==========================================

            $replaceFile = function (
                string $jenis,
                $uploadedFile
            ) use ($inventaris) {

                // Tidak ada file baru
                // berarti file lama tetap dipertahankan
                if (!$uploadedFile) {
                    return;
                }

                // ==========================================
                // CARI SEMUA FILE LAMA DENGAN JENIS TERSEBUT
                // ==========================================

                $fileLama = $inventaris->files()
                    ->where('jenis', $jenis)
                    ->get();

                // ==========================================
                // HAPUS FILE LAMA
                // ==========================================

                foreach ($fileLama as $oldFile) {

                    // Hapus file fisik
                    if (
                        $oldFile->file_path &&
                        Storage::disk('public')->exists(
                            $oldFile->file_path
                        )
                    ) {
                        Storage::disk('public')->delete(
                            $oldFile->file_path
                        );
                    }

                    // Hapus record database
                    $oldFile->delete();
                }

                // ==========================================
                // SIMPAN FILE BARU
                // ==========================================

                $path = $uploadedFile->store(
                    'inventaris/' . $inventaris->id,
                    'public'
                );

                // ==========================================
                // SIMPAN RECORD BARU
                // ==========================================

                $inventaris->files()->create([
                    'nama_file' => $uploadedFile->getClientOriginalName(),
                    'file_path' => $path,
                    'jenis' => $jenis,
                    'mime_type' => $uploadedFile->getClientMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'keterangan' => null,
                ]);
            };

            // ==========================================
            // 3. REPLACE FOTO
            // ==========================================

            $replaceFile(
                'foto',
                $request->file('foto')
            );

            // ==========================================
            // 4. REPLACE NOTA
            // ==========================================

            $replaceFile(
                'nota',
                $request->file('nota')
            );

            // ==========================================
            // 5. REPLACE DOKUMEN
            // ==========================================

            $replaceFile(
                'dokumen',
                $request->file('dokumen')
            );
        });

        return redirect()
            ->route('inventaris.index')
            ->with(
                'success',
                'Data inventaris berhasil diperbarui.'
            );
    }


    public function showPublic(string $barcode)
    {
        $inventaris = Inventaris::with([
            'kategori',
            'files' => function ($query) {
                $query->latest();
            },
        ])
        ->where('barcode', $barcode)
        ->firstOrFail();

        return Inertia::render('Inventaris/ShowPublic', [
            'inventaris' => $inventaris,
        ]);
    }


    public function destroy(Inventaris $inventaris)
    {
        /*
         * Hapus file fisik
         */
        foreach ($inventaris->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $inventaris->delete();

        return redirect()
            ->route('inventaris.index')
            ->with('success', 'Inventaris berhasil dihapus.');
    }
}