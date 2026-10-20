<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\ProgramPelatihan;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TugasController extends Controller
{
    /**
     * Menampilkan daftar tugas beserta pencarian dan filter.
     */
    public function index(Request $request): Response
    {
        $search = trim($request->input('search', ''));

        $tugas = Tugas::query()
            ->with([
                'programPelatihan.pelatihan',
                'programPelatihan.tahap',
                'kelas',
                'pembuat',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('judul', 'like', "%{$search}%");
            })
            ->when(
                $request->filled('program_pelatihan_id'),
                function ($query) use ($request) {
                    $query->where(
                        'program_pelatihan_id',
                        $request->input('program_pelatihan_id')
                    );
                }
            )
            ->when(
                $request->filled('kelas_id'),
                function ($query) use ($request) {
                    $query->where(
                        'kelas_id',
                        $request->input('kelas_id')
                    );
                }
            )
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'status',
                        $request->input('status')
                    );
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tugas/Index', [
            'tugas' => $tugas,

            'filters' => $request->only([
                'search',
                'program_pelatihan_id',
                'kelas_id',
                'status',
            ]),

            'programPelatihans' => ProgramPelatihan::with([
                'pelatihan:id,nama',
                'tahap:id,nama',
            ])
                ->orderBy('nama')
                ->get(),

            'kelass' => Kelas::orderBy('nama')->get([
                'id',
                'nama',
                'durasi_hari',
            ]),
        ]);
    }

    /**
     * Menampilkan form tambah tugas.
     */
    public function create(): Response
    {
        return Inertia::render('Tugas/Create', [
            'programPelatihans' => ProgramPelatihan::with([
                'pelatihan:id,nama',
                'tahap:id,nama',
            ])
                ->orderBy('nama')
                ->get(),

            'kelass' => Kelas::orderBy('nama')->get([
                'id',
                'nama',
                'durasi_hari',
            ]),
        ]);
    }

    /**
     * Menyimpan tugas baru.
     */
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

            'judul' => [
                'required',
                'string',
                'max:255',
            ],

            'instruksi' => [
                'nullable',
                'string',
            ],

            'deadline' => [
                'nullable',
                'date',
            ],

            'nilai_maksimal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:draft,published,closed',
            ],
        ]);

        $validated['created_by'] = auth()->id();

        Tugas::create($validated);

        return redirect()
            ->route('tugas.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail tugas.
     */
    public function show(Tugas $tugas): Response
    {
        $tugas->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'pembuat',
            'pengumpulanTugas',
        ]);

        return Inertia::render('Tugas/Show', [
            'tugas' => $tugas,
        ]);
    }

    /**
     * Menampilkan form edit tugas.
     */
public function edit(Tugas $tuga): Response
{
    $tuga->load([
        'programPelatihan.pelatihan',
        'programPelatihan.tahap',
        'kelas',
        'pembuat',
    ]);

    return Inertia::render('Tugas/Edit', [
        'tugas' => $tuga,
        'programPelatihans' => ProgramPelatihan::with([
            'pelatihan:id,nama',
            'tahap:id,nama',
        ])->orderBy('nama')->get(),
        'kelass' => Kelas::orderBy('nama')
            ->get(['id', 'nama', 'durasi_hari']),
    ]);
}

    /**
     * Memperbarui tugas.
     */
    public function update(
        Request $request,
        Tugas $tuga
    ): RedirectResponse {
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

            'judul' => [
                'required',
                'string',
                'max:255',
            ],

            'instruksi' => [
                'nullable',
                'string',
            ],

            'deadline' => [
                'nullable',
                'date',
            ],

            'nilai_maksimal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:draft,published,closed',
            ],
        ]);

        $tuga->update($validated);

        return redirect()
            ->route('tugas.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Menghapus tugas jika belum memiliki pengumpulan.
     */
    public function destroy(Tugas $tugas): RedirectResponse
    {
        if ($tugas->pengumpulanTugas()->exists()) {
            return redirect()
                ->route('tugas.index')
                ->with(
                    'error',
                    'Tugas tidak dapat dihapus karena sudah memiliki pengumpulan.'
                );
        }

        $tugas->delete();

        return redirect()
            ->route('tugas.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
