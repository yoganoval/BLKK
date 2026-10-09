<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Models\Peserta;
use App\Models\ProgramPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PendaftaranController extends Controller
{
    /**
     * Menampilkan daftar pendaftaran.
     */
    public function index()
    {
        $pendaftarans = Pendaftaran::with([
            'peserta',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'pembayarans',
        ])
            ->orderByDesc('tanggal_daftar')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Pendaftaran/Index', [
            'pendaftarans' => $pendaftarans,
        ]);
    }

    /**
     * Form pendaftaran.
     */
    public function create()
    {
        $programPelatihans = ProgramPelatihan::with([
            'pelatihan',
            'tahap',
        ])
            ->where('status', 'dibuka')
            ->orderBy('nama')
            ->get();

        $kelass = Kelas::orderBy('nama')->get();

        return Inertia::render('Pendaftaran/Create', [
            'programPelatihans' => $programPelatihans,
            'kelass' => $kelass,
        ]);
    }

    /**
     * Simpan peserta + pendaftaran + pembayaran.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | DATA PESERTA
            |--------------------------------------------------------------------------
            */

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'kamar' => [
                'nullable',
                'string',
                'max:255',
            ],

            'lembaga' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | DATA PENDAFTARAN
            |--------------------------------------------------------------------------
            */

            'program_pelatihan_id' => [
                'required',
                'exists:program_pelatihan,id',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'tanggal_daftar' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:aktif,selesai,batal',
            ],

            /*
            |--------------------------------------------------------------------------
            | DATA PEMBAYARAN
            |--------------------------------------------------------------------------
            */

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
                'max:255',
            ],

            'status_pembayaran' => [
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
        | Ambil program
        |--------------------------------------------------------------------------
        */

        $program = ProgramPelatihan::findOrFail(
            $validated['program_pelatihan_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Program harus dibuka
        |--------------------------------------------------------------------------
        */

        if ($program->status !== 'dibuka') {
            return back()
                ->withErrors([
                    'program_pelatihan_id' =>
                        'Program pelatihan ini sedang ditutup.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Cek kuota program
        |--------------------------------------------------------------------------
        */

        $jumlahAktif = Pendaftaran::where(
            'program_pelatihan_id',
            $program->id
        )
            ->where('status', 'aktif')
            ->count();

        if (
            $validated['status'] === 'aktif'
            && $jumlahAktif >= $program->kuota
        ) {
            return back()
                ->withErrors([
                    'program_pelatihan_id' =>
                        'Kuota program pelatihan sudah penuh.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan semuanya dalam satu transaksi
        |--------------------------------------------------------------------------
        */

        try {

            DB::transaction(function () use ($validated, $request) {

                /*
                |--------------------------------------------------------------------------
                | 1. Simpan PESERTA
                |--------------------------------------------------------------------------
                */

                $peserta = Peserta::create([
                    'nama' => $validated['nama'],
                    'alamat' => $validated['alamat'] ?? null,
                    'kamar' => $validated['kamar'] ?? null,
                    'lembaga' => $validated['lembaga'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 2. Simpan PENDAFTARAN
                |--------------------------------------------------------------------------
                */

                $pendaftaran = Pendaftaran::create([
                    'peserta_id' => $peserta->id,
                    'program_pelatihan_id' =>
                        $validated['program_pelatihan_id'],
                    'kelas_id' => $validated['kelas_id'],
                    'tanggal_daftar' =>
                        $validated['tanggal_daftar'],
                    'status' => $validated['status'],
                ]);

                /*
                |--------------------------------------------------------------------------
                | 3. Upload bukti pembayaran
                |--------------------------------------------------------------------------
                */

                $buktiPath = null;

                if ($request->hasFile('bukti')) {
                    $buktiPath = $request->file('bukti')
                        ->store('bukti-pembayaran', 'public');
                }

                /*
                |--------------------------------------------------------------------------
                | 4. Simpan PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                Pembayaran::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jumlah' => $validated['jumlah'],
                    'tanggal_bayar' =>
                        $validated['tanggal_bayar'] ?? null,
                    'metode' =>
                        $validated['metode'] ?? null,
                    'status' =>
                        $validated['status_pembayaran'],
                    'bukti' => $buktiPath,
                    'keterangan' =>
                        $validated['keterangan'] ?? null,
                ]);
            });

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'pembayaran' =>
                        'Data gagal disimpan. Silakan coba kembali.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('pendaftarans.index')
            ->with(
                'success',
                'Peserta, pendaftaran, dan pembayaran berhasil ditambahkan.'
            );
    }

    /**
     * Detail pendaftaran.
     */
    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'peserta.pesertaFace',
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'pembayarans',
            'absensis.jadwal',
        ]);

        return Inertia::render('Pendaftaran/Show', [
            'pendaftaran' => $pendaftaran,
        ]);
    }

    /**
     * Form edit.
     */
    public function edit(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'peserta',
            'programPelatihan',
            'kelas',
            'pembayarans',
        ]);

        $programPelatihans = ProgramPelatihan::with([
            'pelatihan',
            'tahap',
        ])
            ->orderBy('nama')
            ->get();

        $kelass = Kelas::orderBy('nama')->get();

        return Inertia::render('Pendaftaran/Edit', [
            'pendaftaran' => $pendaftaran,
            'programPelatihans' => $programPelatihans,
            'kelass' => $kelass,
        ]);
    }

    /**
     * Update pendaftaran.
     */
    public function update(
        Request $request,
        Pendaftaran $pendaftaran
    ) {
        $validated = $request->validate([
            'peserta_id' => [
                'required',
                'exists:peserta,id',
            ],

            'program_pelatihan_id' => [
                'required',
                'exists:program_pelatihan,id',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'tanggal_daftar' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:aktif,selesai,batal',
            ],
        ]);

        $program = ProgramPelatihan::findOrFail(
            $validated['program_pelatihan_id']
        );

        if ($validated['status'] === 'aktif') {

            $jumlahAktif = Pendaftaran::where(
                'program_pelatihan_id',
                $program->id
            )
                ->where('status', 'aktif')
                ->where('id', '!=', $pendaftaran->id)
                ->count();

            if ($jumlahAktif >= $program->kuota) {
                return back()
                    ->withErrors([
                        'program_pelatihan_id' =>
                            'Kuota program pelatihan sudah penuh.',
                    ])
                    ->withInput();
            }
        }

        $sudahTerdaftar = Pendaftaran::where(
            'peserta_id',
            $validated['peserta_id']
        )
            ->where(
                'program_pelatihan_id',
                $validated['program_pelatihan_id']
            )
            ->where('status', 'aktif')
            ->where('id', '!=', $pendaftaran->id)
            ->exists();

        if ($sudahTerdaftar) {
            return back()
                ->withErrors([
                    'peserta_id' =>
                        'Peserta sudah memiliki pendaftaran aktif pada program ini.',
                ])
                ->withInput();
        }

        $pendaftaran->update($validated);

        return redirect()
            ->route('pendaftarans.index')
            ->with(
                'success',
                'Pendaftaran berhasil diperbarui.'
            );
    }

    /**
     * Hapus pendaftaran.
     */
    public function destroy(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->pembayarans()->exists()) {
            return back()->withErrors([
                'pendaftaran' =>
                    'Pendaftaran tidak dapat dihapus karena sudah memiliki pembayaran.',
            ]);
        }

        if ($pendaftaran->absensis()->exists()) {
            return back()->withErrors([
                'pendaftaran' =>
                    'Pendaftaran tidak dapat dihapus karena sudah memiliki data absensi.',
            ]);
        }

        $pendaftaran->delete();

        return redirect()
            ->route('pendaftarans.index')
            ->with(
                'success',
                'Pendaftaran berhasil dihapus.'
            );
    }
}