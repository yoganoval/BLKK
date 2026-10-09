<?php

namespace App\Http\Controllers;

use App\Models\JadwalStaff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalStaffController extends Controller
{
    private array $hari = [
        'senin',
        'selasa',
        'rabu',
        'kamis',
        'jumat',
        'sabtu',
        'minggu',
    ];

    public function index()
    {
        $jadwals = JadwalStaff::query()
            ->orderByRaw("
                CASE hari
                    WHEN 'senin' THEN 1
                    WHEN 'selasa' THEN 2
                    WHEN 'rabu' THEN 3
                    WHEN 'kamis' THEN 4
                    WHEN 'jumat' THEN 5
                    WHEN 'sabtu' THEN 6
                    WHEN 'minggu' THEN 7
                END
            ")
            ->orderBy('jam_masuk')
            ->get();

        return Inertia::render('JadwalStaff/Index', [
            'jadwals' => $jadwals,
        ]);
    }

    public function create()
    {
        return Inertia::render('JadwalStaff/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],

            'hari' => [
                'required',
                'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            ],

            'jam_masuk' => [
                'required',
                'date_format:H:i',
            ],

            'jam_pulang' => [
                'required',
                'date_format:H:i',
                'after:jam_masuk',
            ],

            'toleransi_menit' => [
                'required',
                'integer',
                'min:0',
                'max:1440',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        JadwalStaff::create($validated);

        return redirect()
            ->route('jadwal-staff.index')
            ->with('success', 'Jadwal staff berhasil ditambahkan.');
    }

    public function show(JadwalStaff $jadwalStaff)
    {
        return Inertia::render('JadwalStaff/Show', [
            'jadwal' => $jadwalStaff,
        ]);
    }

    public function edit(JadwalStaff $jadwalStaff)
    {
        return Inertia::render('JadwalStaff/Edit', [
            'jadwal' => $jadwalStaff,
        ]);
    }

    public function update(Request $request, JadwalStaff $jadwalStaff)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],

            'hari' => [
                'required',
                'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            ],

            'jam_masuk' => [
                'required',
                'date_format:H:i',
            ],

            'jam_pulang' => [
                'required',
                'date_format:H:i',
                'after:jam_masuk',
            ],

            'toleransi_menit' => [
                'required',
                'integer',
                'min:0',
                'max:1440',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $jadwalStaff->update($validated);

        return redirect()
            ->route('jadwal-staff.index')
            ->with('success', 'Jadwal staff berhasil diperbarui.');
    }

    public function destroy(JadwalStaff $jadwalStaff)
    {
        $jadwalStaff->delete();

        return redirect()
            ->route('jadwal-staff.index')
            ->with('success', 'Jadwal staff berhasil dihapus.');
    }
}