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
     * Peserta harus sudah dibuat melalui menu Peserta.
     */
    public function create()
    {
        $pesertas = Peserta::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get([
                'id',
                'nama',
                'username',
                'kamar',
                'lembaga',
            ]);

        $programPelatihans = ProgramPelatihan::with([
            'pelatihan',
            'tahap',
        ])
            ->where('status', 'dibuka')
            ->orderBy('nama')
            ->get();

        $kelass = Kelas::orderBy('nama')->get();

        return Inertia::render('Pendaftaran/Create', [
            'pesertas' => $pesertas,
            'programPelatihans' => $programPelatihans,
            'kelass' => $kelass,
        ]);
    }

    /**
     * Simpan pendaftaran dan pembayaran.
     * Tidak membuat peserta baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'peserta_id' => [
                'required',
                'integer',
                'exists:peserta,id',
            ],

            'program_pelatihan_id' => [
                'required',
                'integer',
                'exists:program_pelatihan,id',
            ],

            'kelas_id' => [
                'required',
                'integer',
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
         * Pastikan peserta masih aktif.
         */
        $peserta = Peserta::whereKey($validated['peserta_id'])
            ->where('is_active', true)
            ->first();

        if (! $peserta) {
            return back()
                ->withErrors([
                    'peserta_id' =>
                        'Peserta tidak ditemukan atau akunnya tidak aktif.',
                ])
                ->withInput();
        }

        /*
         * Pastikan program tersedia dan sedang dibuka.
         */
        $program = ProgramPelatihan::findOrFail(
            $validated['program_pelatihan_id']
        );

        if ($program->status !== 'dibuka') {
            return back()
                ->withErrors([
                    'program_pelatihan_id' =>
                        'Program pelatihan ini sedang ditutup.',
                ])
                ->withInput();
        }

        /*
         * Cegah peserta mendaftar dua kali pada program
         * yang sama selama pendaftaran sebelumnya masih aktif.
         */
        if ($validated['status'] === 'aktif') {
            $sudahTerdaftar = Pendaftaran::where(
                'peserta_id',
                $validated['peserta_id']
            )
                ->where(
                    'program_pelatihan_id',
                    $validated['program_pelatihan_id']
                )
                ->where('status', 'aktif')
                ->exists();

            if ($sudahTerdaftar) {
                return back()
                    ->withErrors([
                        'peserta_id' =>
                            'Peserta sudah memiliki pendaftaran aktif pada program ini.',
                    ])
                    ->withInput();
            }
        }

        /*
         * Periksa kuota program.
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

        try {
            DB::transaction(function () use ($validated, $request) {
                /*
                 * 1. Simpan pendaftaran menggunakan peserta
                 * yang sudah ada di database.
                 */
                $pendaftaran = Pendaftaran::create([
                    'peserta_id' => $validated['peserta_id'],
                    'program_pelatihan_id' =>
                        $validated['program_pelatihan_id'],
                    'kelas_id' => $validated['kelas_id'],
                    'tanggal_daftar' => $validated['tanggal_daftar'],
                    'status' => $validated['status'],
                ]);

                /*
                 * 2. Upload bukti pembayaran jika tersedia.
                 */
                $buktiPath = null;

                if ($request->hasFile('bukti')) {
                    $buktiPath = $request->file('bukti')
                        ->store('bukti-pembayaran', 'public');
                }

                /*
                 * 3. Simpan pembayaran terkait pendaftaran.
                 */
                Pembayaran::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jumlah' => $validated['jumlah'],
                    'tanggal_bayar' =>
                        $validated['tanggal_bayar'] ?? null,
                    'metode' => $validated['metode'] ?? null,
                    'status' => $validated['status_pembayaran'],
                    'bukti' => $buktiPath,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors([
                    'pembayaran' =>
                        'Data pendaftaran atau pembayaran gagal disimpan. Periksa kembali data dan konfigurasi database.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('pendaftarans.index')
            ->with(
                'success',
                'Pendaftaran dan pembayaran berhasil ditambahkan.'
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
     * Form edit pendaftaran.
     */
    public function edit(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'peserta',
            'programPelatihan',
            'kelas',
            'pembayarans',
        ]);

        $pesertas = Peserta::query()
            ->where('is_active', true)
            ->orWhere('id', $pendaftaran->peserta_id)
            ->orderBy('nama')
            ->get([
                'id',
                'nama',
                'username',
                'kamar',
                'lembaga',
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
            'pesertas' => $pesertas,
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
                'integer',
                'exists:peserta,id',
            ],

            'program_pelatihan_id' => [
                'required',
                'integer',
                'exists:program_pelatihan,id',
            ],

            'kelas_id' => [
                'required',
                'integer',
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

        /*
         * Pastikan peserta aktif.
         * Peserta yang sedang diedit boleh tetap dipilih
         * walaupun akunnya sudah dinonaktifkan.
         */
        $pesertaValid = Peserta::whereKey($validated['peserta_id'])
            ->where(function ($query) use ($pendaftaran) {
                $query->where('is_active', true)
                    ->orWhere('id', $pendaftaran->peserta_id);
            })
            ->exists();

        if (! $pesertaValid) {
            return back()
                ->withErrors([
                    'peserta_id' =>
                        'Peserta tidak ditemukan atau akunnya tidak aktif.',
                ])
                ->withInput();
        }

        $program = ProgramPelatihan::findOrFail(
            $validated['program_pelatihan_id']
        );

        /*
         * Cek kuota, tidak menghitung pendaftaran yang sedang diedit.
         */
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

            /*
             * Cegah pendaftaran aktif ganda untuk peserta
             * dan program yang sama.
             */
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