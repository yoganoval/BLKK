<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
use App\Models\Soal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SoalController extends Controller
{

    /**
     * Menampilkan semua soal kuis.
     */
    public function globalIndex(): \Inertia\Response
    {
        $soals = \App\Models\Soal::query()
            ->with('kuis')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return \Inertia\Inertia::render('Soal/Index', [
            'soals' => $soals,
        ]);
    }

    public function index(Kuis $kui): Response
    {
        $kui->load('programPelatihan', 'kelas');

        $soal = $kui->soal()
            ->with('opsiJawaban')
            ->withCount('jawabanKuis')
            ->get();

        return Inertia::render('Kuis/Soal/Index', [
            'kuis' => $kui,
            'soal' => $soal,
        ]);
    }

    public function create(Kuis $kui): Response
    {
        return Inertia::render('Kuis/Soal/Create', [
            'kuis' => $kui,
        ]);
    }

    public function store(Request $request, Kuis $kui): RedirectResponse
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'tipe' => [
                'required',
                Rule::in(['pilihan_ganda', 'benar_salah', 'esai']),
            ],
            'bobot' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($kui, $validated) {
            $kui->soal()->create($validated);
        });

        return to_route('kuis.soal.index', $kui)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function show(Kuis $kui, Soal $soal): Response
    {
        $this->ensureSoalMilikKuis($kui, $soal);

        $soal->load('opsiJawaban');

        return Inertia::render('Kuis/Soal/Show', [
            'kuis' => $kui,
            'soal' => $soal,
        ]);
    }

    public function edit(Kuis $kui, Soal $soal): Response
    {
        $this->ensureSoalMilikKuis($kui, $soal);

        $soal->load('opsiJawaban');

        return Inertia::render('Kuis/Soal/Edit', [
            'kuis' => $kui,
            'soal' => $soal,
        ]);
    }

    public function update(
        Request $request,
        Kuis $kui,
        Soal $soal
    ): RedirectResponse {
        $this->ensureSoalMilikKuis($kui, $soal);

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'tipe' => [
                'required',
                Rule::in(['pilihan_ganda', 'benar_salah', 'esai']),
            ],
            'bobot' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($soal, $validated) {
            $soal->update($validated);
        });

        return to_route('kuis.soal.index', $kui)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Kuis $kui, Soal $soal): RedirectResponse
    {
        $this->ensureSoalMilikKuis($kui, $soal);

        if ($soal->jawabanKuis()->exists()) {
            return back()->with(
                'error',
                'Soal tidak dapat dihapus karena sudah pernah dijawab peserta.'
            );
        }

        DB::transaction(function () use ($soal) {
            $soal->delete();
        });

        return to_route('kuis.soal.index', $kui)
            ->with('success', 'Soal berhasil dihapus.');
    }

    private function ensureSoalMilikKuis(Kuis $kui, Soal $soal): void
    {
        abort_unless(
            (int) $soal->kuis_id === (int) $kui->id,
            404
        );
    }
}
