<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\PengaturanRekapNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PengaturanRekapNilaiController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::query()
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();

        $kelasId = $request->integer('kelas_id')
            ?: $kelasList->first()?->id;

        $pengaturan = $kelasId
            ? PengaturanRekapNilai::firstWhere('kelas_id', $kelasId)
            : null;

        return Inertia::render('PengaturanRekapNilai/Index', [
            'kelasList' => $kelasList,
            'kelasTerpilih' => $kelasId,
            'pengaturan' => $pengaturan,
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'metode_rekap' => [
                'required',
                Rule::in(['rata_rata']),
            ],
            'bobot_tugas' => [
                'required', 'numeric', 'min:0', 'max:100',
            ],
            'bobot_kuis' => [
                'required', 'numeric', 'min:0', 'max:100',
            ],
            'otomatis' => ['required', 'boolean'],
            'hitung_belum_dinilai' => ['required', 'boolean'],
            'esai_harus_dinilai' => ['required', 'boolean'],
        ]);

        $totalBobot = (float) $validated['bobot_tugas']
            + (float) $validated['bobot_kuis'];

        if (abs($totalBobot - 100) > 0.001) {
            return back()
                ->withErrors([
                    'bobot_kuis' => 'Total bobot tugas dan kuis harus tepat 100%.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $kelas) {
            PengaturanRekapNilai::updateOrCreate(
                ['kelas_id' => $kelas->id],
                [
                    ...$validated,
                    'updated_by' => auth()->id(),
                ]
            );
        });

        return redirect()
            ->route('pengaturan-rekap-nilai.index', [
                'kelas_id' => $kelas->id,
            ])
            ->with('success', 'Pengaturan rekap nilai berhasil disimpan.');
    }
}
