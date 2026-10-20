<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\ProgramPelatihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KuisController extends Controller
{
    public function index(Request $request): Response
    {
        $kuis = Kuis::query()
            ->with([
                'programPelatihan',
                'kelas',
                'materi',
                'creator',
            ])
            ->withCount('soal')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Kuis/Index', [
            'kuis' => $kuis,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Kuis/Create', [
            'programPelatihans' => ProgramPelatihan::query()
                ->orderBy('id')
                ->get(),

            'kelas' => Kelas::query()
                ->orderBy('nama')
                ->get(),

            'materi' => Materi::query()
                ->orderBy('id', 'desc')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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
            'materi_id' => [
                'nullable',
                'integer',
                'exists:materi,id',
            ],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'waktu_menit' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'nilai_lulus' => ['required', 'numeric', 'min:0', 'max:100'],
            'percobaan_maksimal' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published', 'closed']),
            ],
        ]);

        $validated['created_by'] = $request->user()?->id;

        DB::transaction(function () use ($validated) {
            Kuis::create($validated);
        });

        return to_route('kuis.index')
            ->with('success', 'Kuis berhasil ditambahkan.');
    }

    public function show(Kuis $kui): Response
    {
        $kui->load([
            'programPelatihan',
            'kelas',
            'materi',
            'creator',
            'soal.opsiJawaban',
        ]);

        return Inertia::render('Kuis/Show', [
            'kuis' => $kui->only([
                'id',
                'program_pelatihan_id',
                'kelas_id',
                'materi_id',
                'judul',
                'deskripsi',
                'waktu_menit',
                'nilai_lulus',
                'percobaan_maksimal',
                'status',
            ]),
        ]);
    }

    public function edit(Kuis $kui)
    {
        return Inertia::render('Kuis/Edit', [
            'kuis' => $kui->only([
                'id',
                'program_pelatihan_id',
                'kelas_id',
                'materi_id',
                'judul',
                'deskripsi',
                'waktu_menit',
                'nilai_lulus',
                'percobaan_maksimal',
                'status',
            ]),

            'programPelatihan' => \App\Models\ProgramPelatihan::query()
                ->orderBy('id')
                ->get(),

            'kelas' => \App\Models\Kelas::query()
                ->orderBy('nama')
                ->get(),

            'materi' => \App\Models\Materi::query()
                ->orderBy('id', 'desc')
                ->get(),
        ]);
    }

    public function update(Request $request, Kuis $kui)
    {
        $validated = $request->validate([
            'program_pelatihan_id' => 'required|exists:program_pelatihan,id',
            'kelas_id' => 'required|exists:kelas,id',
            'materi_id' => 'nullable|exists:materi,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'waktu_menit' => 'required|integer|min:1',
            'nilai_lulus' => 'required|numeric|min:0|max:100',
            'percobaan_maksimal' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,closed',
        ]);

        $kui->update($validated);

        return redirect()
            ->route('kuis.index')
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroy(Kuis $kui): RedirectResponse
    {
        if ($kui->percobaanKuis()->exists()) {
            return back()->with(
                'error',
                'Kuis tidak dapat dihapus karena sudah memiliki riwayat pengerjaan.'
            );
        }

        DB::transaction(function () use ($kui) {
            $kui->delete();
        });

        return to_route('kuis.index')
            ->with('success', 'Kuis berhasil dihapus.');
    }
}
