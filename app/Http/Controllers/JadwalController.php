<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\ProgramPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class JadwalController extends Controller
{
    /**
     * Menampilkan daftar jadwal.
     */
    public function index()
    {
        $jadwals = Jadwal::with([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ])
            ->orderByDesc('tanggal')
            ->orderBy('jam_mulai')
            ->orderBy('pertemuan')
            ->get();

        return Inertia::render('Jadwal/Index', [
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Form tambah jadwal.
     */
    public function create()
    {
        $programPelatihans = ProgramPelatihan::with([
            'pelatihan',
            'tahap',
        ])
            ->orderBy('nama')
            ->get();

        $kelass = Kelas::orderBy('nama')->get();

        return Inertia::render('Jadwal/Create', [
            'programPelatihans' => $programPelatihans,
            'kelass' => $kelass,
        ]);
    }

    /**
     * Simpan jadwal untuk beberapa kelas sekaligus.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_pelatihan_id' => [
                'required',
                'exists:program_pelatihan,id',
            ],

            'kelas_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'kelas_ids.*' => [
                'required',
                'integer',
                'exists:kelas,id',
                'distinct',
            ],

            'pertemuan' => [
                'required',
                'integer',
                'min:1',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],

            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
            ],

            'kegiatan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:belum_dimulai,berlangsung,selesai,dibatalkan',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil kelas yang dipilih
        |--------------------------------------------------------------------------
        */

        $kelass = Kelas::whereIn(
            'id',
            $validated['kelas_ids']
        )
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Validasi durasi setiap kelas
        |--------------------------------------------------------------------------
        */

        foreach ($kelass as $kelas) {
            if ($validated['pertemuan'] > $kelas->durasi_hari) {
                return back()
                    ->withErrors([
                        'pertemuan' =>
                            "Jumlah pertemuan untuk kelas {$kelas->nama} maksimal {$kelas->durasi_hari} pertemuan.",
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cek apakah kelas sudah mempunyai jadwal
        |--------------------------------------------------------------------------
        |
        | Karena satu Jadwal sekarang dapat memiliki banyak kelas,
        | kita mengecek melalui tabel pivot jadwal_kelas.
        |
        */

        $kelasSudahAda = Jadwal::where(
            'program_pelatihan_id',
            $validated['program_pelatihan_id']
        )
            ->where(
                'pertemuan',
                $validated['pertemuan']
            )
            ->whereHas('kelas', function ($query) use ($validated) {
                $query->whereIn(
                    'kelas.id',
                    $validated['kelas_ids']
                );
            })
            ->with('kelas')
            ->get();

        if ($kelasSudahAda->isNotEmpty()) {
            $namaKelas = $kelasSudahAda
                ->flatMap(fn ($jadwal) => $jadwal->kelas)
                ->whereIn('id', $validated['kelas_ids'])
                ->pluck('nama')
                ->unique()
                ->implode(', ');

            return back()
                ->withErrors([
                    'kelas_ids' =>
                        "Pertemuan {$validated['pertemuan']} sudah memiliki jadwal untuk kelas: {$namaKelas}.",
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan jadwal + kelas dalam satu transaksi
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($validated) {

            $jadwal = Jadwal::create([
                'program_pelatihan_id' => $validated['program_pelatihan_id'],
                'pertemuan' => $validated['pertemuan'],
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'kegiatan' => $validated['kegiatan'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => $validated['status'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Hubungkan semua kelas melalui pivot
            |--------------------------------------------------------------------------
            */

            $jadwal->kelas()->sync($validated['kelas_ids']);
        });

        return redirect()
            ->route('jadwals.index')
            ->with(
                'success',
                count($validated['kelas_ids']) .
                ' kelas berhasil ditambahkan ke jadwal.'
            );
    }

    /**
     * Detail jadwal.
     */
    public function show(Jadwal $jadwal)
    {
        $jadwal->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'absensis.pendaftaran.peserta',
        ]);

        return Inertia::render('Jadwal/Show', [
            'jadwal' => $jadwal,
        ]);
    }

    /**
     * Form edit jadwal.
     */
    public function edit(Jadwal $jadwal)
    {
        $jadwal->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ]);

        $programPelatihans = ProgramPelatihan::with([
            'pelatihan',
            'tahap',
        ])
            ->orderBy('nama')
            ->get();

        $kelass = Kelas::orderBy('nama')->get();

        return Inertia::render('Jadwal/Edit', [
            'jadwal' => $jadwal,
            'programPelatihans' => $programPelatihans,
            'kelass' => $kelass,
        ]);
    }

    /**
     * Update jadwal dan kelas sekaligus.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'program_pelatihan_id' => [
                'required',
                'exists:program_pelatihan,id',
            ],

            'kelas_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'kelas_ids.*' => [
                'required',
                'integer',
                'exists:kelas,id',
                'distinct',
            ],

            'pertemuan' => [
                'required',
                'integer',
                'min:1',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],

            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
            ],

            'kegiatan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:belum_dimulai,berlangsung,selesai,dibatalkan',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil kelas yang dipilih
        |--------------------------------------------------------------------------
        */

        $kelass = Kelas::whereIn(
            'id',
            $validated['kelas_ids']
        )
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Validasi durasi setiap kelas
        |--------------------------------------------------------------------------
        */

        foreach ($kelass as $kelas) {
            if ($validated['pertemuan'] > $kelas->durasi_hari) {
                return back()
                    ->withErrors([
                        'pertemuan' =>
                            "Jumlah pertemuan untuk kelas {$kelas->nama} maksimal {$kelas->durasi_hari} pertemuan.",
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cek bentrok dengan jadwal lain
        |--------------------------------------------------------------------------
        */

        $jadwalBentrok = Jadwal::where(
            'program_pelatihan_id',
            $validated['program_pelatihan_id']
        )
            ->where(
                'pertemuan',
                $validated['pertemuan']
            )
            ->where(
                'id',
                '!=',
                $jadwal->id
            )
            ->whereHas('kelas', function ($query) use ($validated) {
                $query->whereIn(
                    'kelas.id',
                    $validated['kelas_ids']
                );
            })
            ->with('kelas')
            ->get();

        if ($jadwalBentrok->isNotEmpty()) {
            $namaKelas = $jadwalBentrok
                ->flatMap(fn ($item) => $item->kelas)
                ->whereIn('id', $validated['kelas_ids'])
                ->pluck('nama')
                ->unique()
                ->implode(', ');

            return back()
                ->withErrors([
                    'kelas_ids' =>
                        "Pertemuan {$validated['pertemuan']} sudah digunakan oleh kelas: {$namaKelas}.",
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Update jadwal + pivot
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $jadwal
        ) {

            $jadwal->update([
                'program_pelatihan_id' => $validated['program_pelatihan_id'],
                'pertemuan' => $validated['pertemuan'],
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'kegiatan' => $validated['kegiatan'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => $validated['status'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Sinkronisasi kelas
            |--------------------------------------------------------------------------
            |
            | Kelas yang dicentang baru akan ditambahkan.
            | Kelas yang tidak dicentang akan dilepas dari jadwal.
            |
            */

            $jadwal->kelas()->sync(
                $validated['kelas_ids']
            );
        });

        return redirect()
            ->route('jadwals.index')
            ->with(
                'success',
                'Jadwal berhasil diperbarui.'
            );
    }

    /**
     * Hapus jadwal.
     */
    public function destroy(Jadwal $jadwal)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan hapus jika sudah ada absensi
        |--------------------------------------------------------------------------
        */

        if ($jadwal->absensis()->exists()) {
            return back()->withErrors([
                'jadwal' =>
                    'Jadwal tidak dapat dihapus karena sudah memiliki data absensi.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus jadwal
        |--------------------------------------------------------------------------
        |
        | Data pivot jadwal_kelas otomatis ikut terhapus
        | karena foreign key menggunakan cascadeOnDelete().
        |
        */

        $jadwal->delete();

        return redirect()
            ->route('jadwals.index')
            ->with(
                'success',
                'Jadwal berhasil dihapus.'
            );
    }
}
