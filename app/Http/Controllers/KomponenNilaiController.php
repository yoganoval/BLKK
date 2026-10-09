<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KomponenNilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KomponenNilaiController extends Controller
{
    /**
     * Menampilkan daftar komponen nilai.
     */
    // public function index(Request $request): Response
    // {
    //     $query = KomponenNilai::with('kelas');

    //     if ($request->filled('search')) {
    //         $query->where(
    //             'nama',
    //             'like',
    //             '%' . $request->search . '%'
    //         );
    //     }

    //     if ($request->filled('kelas_id')) {
    //         $query->where(
    //             'kelas_id',
    //             $request->kelas_id
    //         );
    //     }

    //     $komponen = $query
    //         ->orderBy('kelas_id')
    //         ->orderBy('urutan')
    //         ->orderBy('nama')
    //         ->paginate(10)
    //         ->withQueryString();

    //     return Inertia::render(
    //         'KomponenNilai/Index',
    //         [
    //             'komponen' => $komponen,

    //             'kelas' => Kelas::orderBy('nama')
    //                 ->get(),

    //             'filters' => [
    //                 'search' =>
    //                     $request->search,

    //                 'kelas_id' =>
    //                     $request->kelas_id,
    //             ],
    //         ]
    //     );
    // }
public function index(Request $request)
{
    $query = KomponenNilai::with('kelas')
        ->orderBy('kelas_id')
        ->orderBy('urutan');

    if ($request->filled('search')) {
        $query->where('nama', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('kelas_id')) {
        $query->where('kelas_id', $request->kelas_id);
    }

    return Inertia::render('KomponenNilai/Index', [
        'komponenNilai' => $query->paginate(10)->withQueryString(),

        'kelas' => Kelas::orderBy('nama')->get(),

        'filters' => [
            'search' => $request->search,
            'kelas_id' => $request->kelas_id,
        ],
    ]);
}


    /**
     * Form tambah komponen nilai.
     */
    public function create(): Response
    {
        return Inertia::render(
            'KomponenNilai/Create',
            [
                'kelas' => Kelas::orderBy('nama')
                    ->get(),
            ]
        );
    }

    /**
     * Simpan komponen nilai.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'bobot' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'nilai_maksimal' => [
                'required',
                'numeric',
                'min:1',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'urutan' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        /*
         * Cek agar total bobot dalam satu kelas
         * tidak melebihi 100%.
         */
        $totalBobot = KomponenNilai::where(
            'kelas_id',
            $validated['kelas_id']
        )->sum('bobot');

        $totalSetelahTambah =
            $totalBobot + $validated['bobot'];

        if ($totalSetelahTambah > 100) {
            return back()
                ->withErrors([
                    'bobot' =>
                        'Total bobot komponen dalam kelas ' .
                        'tidak boleh lebih dari 100%. ' .
                        'Bobot saat ini: ' .
                        $totalBobot . '%.',
                ])
                ->withInput();
        }

        KomponenNilai::create($validated);

        return redirect()
            ->route('komponen-nilai.index')
            ->with(
                'success',
                'Komponen nilai berhasil ditambahkan.'
            );
    }

    /**
     * Detail komponen nilai.
     */
    public function show(
        KomponenNilai $komponenNilai
    ): Response {
        $komponenNilai->load([
            'kelas',
            'nilaiPeserta.peserta',
        ]);

        return Inertia::render(
            'KomponenNilai/Show',
            [
                'komponen' => $komponenNilai,
            ]
        );
    }

    /**
     * Form edit komponen nilai.
     */

public function edit(KomponenNilai $komponenNilai)
{
    $komponenNilai->load('kelas');

    return Inertia::render('KomponenNilai/Edit', [
        'komponenNilai' => $komponenNilai,
        'kelas' => Kelas::orderBy('nama')->get(),
    ]);
}

    /**
     * Update komponen nilai.
     */
    // public function update(
    //     Request $request,
    //     KomponenNilai $komponenNilai
    // ): RedirectResponse {
    //     $validated = $request->validate([
    //         'kelas_id' => [
    //             'required',
    //             'exists:kelas,id',
    //         ],

    //         'nama' => [
    //             'required',
    //             'string',
    //             'max:100',
    //         ],

    //         'bobot' => [
    //             'required',
    //             'numeric',
    //             'min:0',
    //             'max:100',
    //         ],

    //         'nilai_maksimal' => [
    //             'required',
    //             'numeric',
    //             'min:1',
    //         ],

    //         'keterangan' => [
    //             'nullable',
    //             'string',
    //             'max:1000',
    //         ],

    //         'urutan' => [
    //             'required',
    //             'integer',
    //             'min:1',
    //         ],
    //     ]);

    //     /*
    //      * Saat edit, komponen yang sedang diedit
    //      * tidak dihitung dalam total bobot lama.
    //      */
    //     $totalBobot = KomponenNilai::where(
    //         'kelas_id',
    //         $validated['kelas_id']
    //     )
    //     ->where(
    //         'id',
    //         '!=',
    //         $komponenNilai->id
    //     )
    //     ->sum('bobot');

    //     $totalSetelahUpdate =
    //         $totalBobot + $validated['bobot'];

    //     if ($totalSetelahUpdate > 100) {
    //         return back()
    //             ->withErrors([
    //                 'bobot' =>
    //                     'Total bobot komponen dalam kelas ' .
    //                     'tidak boleh lebih dari 100%. ' .
    //                     'Total setelah perubahan: ' .
    //                     $totalSetelahUpdate . '%.',
    //             ])
    //             ->withInput();
    //     }

    //     $komponenNilai->update($validated);

    //     return redirect()
    //         ->route('komponen-nilai.index')
    //         ->with(
    //             'success',
    //             'Komponen nilai berhasil diperbarui.'
    //         );
    // }

public function update(Request $request, KomponenNilai $komponenNilai)
{
    $validated = $request->validate([
        'kelas_id' => ['required', 'exists:kelas,id'],
        'nama' => ['required', 'string', 'max:100'],
        'bobot' => ['required', 'numeric', 'min:0', 'max:100'],
        'nilai_maksimal' => ['required', 'numeric', 'min:1'],
        'keterangan' => ['nullable', 'string', 'max:1000'],
        'urutan' => ['required', 'integer', 'min:1'],
    ]);

    $totalBobot = KomponenNilai::where(
        'kelas_id',
        $validated['kelas_id']
    )
        ->where('id', '!=', $komponenNilai->id)
        ->sum('bobot');

    if (($totalBobot + $validated['bobot']) > 100) {
        return back()
            ->withErrors([
                'bobot' => 'Total bobot komponen untuk kelas ini tidak boleh melebihi 100%.',
            ])
            ->withInput();
    }

    $komponenNilai->update($validated);

    return redirect()
        ->route('komponen-nilai.index')
        ->with('success', 'Komponen nilai berhasil diperbarui.');
}

    /**
     * Hapus komponen nilai.
     */
    public function destroy(
        KomponenNilai $komponenNilai
    ): RedirectResponse {
        /*
         * Jangan hapus komponen yang sudah mempunyai
         * nilai peserta.
         */
        if (
            $komponenNilai
                ->nilaiPeserta()
                ->exists()
        ) {
            return back()->with(
                'error',
                'Komponen tidak dapat dihapus karena sudah memiliki nilai peserta.'
            );
        }

        $komponenNilai->delete();

        return redirect()
            ->route('komponen-nilai.index')
            ->with(
                'success',
                'Komponen nilai berhasil dihapus.'
            );
    }
}