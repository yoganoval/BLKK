<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use App\Models\ProgramPelatihan;
use App\Models\Tahap;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgramPelatihanController extends Controller
{
    /**
     * Menampilkan daftar program pelatihan.
     */
    public function index()
    {
        $programPelatihans = ProgramPelatihan::with([
            'pelatihan',
            'tahap',
        ])
            ->withCount([
                'pendaftarans as peserta_aktif_count' => function ($query) {
                    $query->where('status', 'aktif');
                },
            ])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('ProgramPelatihan/Index', [
            'programPelatihans' => $programPelatihans,
        ]);
    }


    /**
     * Form tambah program pelatihan.
     */
    public function create()
    {
        $pelatihans = Pelatihan::orderBy('nama')->get();

        $tahaps = Tahap::orderBy('periode')->get();

        return Inertia::render('ProgramPelatihan/Create', [
            'pelatihans' => $pelatihans,
            'tahaps' => $tahaps,
        ]);
    }


    /**
     * Simpan program pelatihan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelatihan_id' => [
                'required',
                'exists:pelatihan,id',
            ],

            'tahap_id' => [
                'required',
                'exists:tahap,id',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kuota' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                'in:dibuka,ditutup',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Cegah program pelatihan yang sama
        |--------------------------------------------------------------------------
        */

        $sudahAda = ProgramPelatihan::where(
            'pelatihan_id',
            $validated['pelatihan_id']
        )
            ->where(
                'tahap_id',
                $validated['tahap_id']
            )
            ->where(
                'nama',
                $validated['nama']
            )
            ->exists();


        if ($sudahAda) {
            return back()
                ->withErrors([
                    'nama' => 'Program pelatihan dengan nama tersebut sudah ada.',
                ])
                ->withInput();
        }


        ProgramPelatihan::create($validated);


        return redirect()
            ->route('program-pelatihans.index')
            ->with('success', 'Program pelatihan berhasil ditambahkan.');
    }


    /**
     * Menampilkan detail program.
     */
    public function show(ProgramPelatihan $programPelatihan)
    {
        $programPelatihan->load([
            'pelatihan',
            'tahap',
            'pendaftarans.peserta',
            'pendaftarans.kelas',
        ]);


        return Inertia::render('ProgramPelatihan/Show', [
            'programPelatihan' => $programPelatihan,
        ]);
    }


    /**
     * Form edit program.
     */
    public function edit(ProgramPelatihan $programPelatihan)
    {
        $pelatihans = Pelatihan::orderBy('nama')->get();

        $tahaps = Tahap::orderBy('periode')->get();

        return Inertia::render('ProgramPelatihan/Edit', [
            'programPelatihan' => $programPelatihan,
            'pelatihans' => $pelatihans,
            'tahaps' => $tahaps,
        ]);
    }


    /**
     * Update program pelatihan.
     */
    public function update(
        Request $request,
        ProgramPelatihan $programPelatihan
    ) {
        $validated = $request->validate([
            'pelatihan_id' => [
                'required',
                'exists:pelatihan,id',
            ],

            'tahap_id' => [
                'required',
                'exists:tahap,id',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'kuota' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                'in:dibuka,ditutup',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Cek agar kuota tidak lebih kecil dari peserta aktif
        |--------------------------------------------------------------------------
        */

        $pesertaAktif = $programPelatihan
            ->pendaftarans()
            ->where('status', 'aktif')
            ->count();


        if ($validated['kuota'] < $pesertaAktif) {
            return back()
                ->withErrors([
                    'kuota' =>
                        "Kuota tidak boleh lebih kecil dari jumlah peserta aktif ({$pesertaAktif} peserta).",
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Cegah duplikat
        |--------------------------------------------------------------------------
        */

        $sudahAda = ProgramPelatihan::where(
            'pelatihan_id',
            $validated['pelatihan_id']
        )
            ->where(
                'tahap_id',
                $validated['tahap_id']
            )
            ->where(
                'nama',
                $validated['nama']
            )
            ->where(
                'id',
                '!=',
                $programPelatihan->id
            )
            ->exists();


        if ($sudahAda) {
            return back()
                ->withErrors([
                    'nama' => 'Program pelatihan dengan nama tersebut sudah ada.',
                ])
                ->withInput();
        }


        $programPelatihan->update($validated);


        return redirect()
            ->route('program-pelatihans.index')
            ->with('success', 'Program pelatihan berhasil diperbarui.');
    }


    /**
     * Hapus program pelatihan.
     */
    public function destroy(ProgramPelatihan $programPelatihan)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan hapus jika sudah memiliki pendaftaran
        |--------------------------------------------------------------------------
        */

        if ($programPelatihan->pendaftarans()->exists()) {
            return back()->withErrors([
                'programPelatihan' =>
                    'Program pelatihan tidak dapat dihapus karena sudah memiliki data pendaftaran.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Jangan hapus jika sudah memiliki jadwal
        |--------------------------------------------------------------------------
        */

        if ($programPelatihan->jadwals()->exists()) {
            return back()->withErrors([
                'programPelatihan' =>
                    'Program pelatihan tidak dapat dihapus karena sudah memiliki jadwal.',
            ]);
        }


        $programPelatihan->delete();


        return redirect()
            ->route('program-pelatihans.index')
            ->with('success', 'Program pelatihan berhasil dihapus.');
    }
}