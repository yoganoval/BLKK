<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TugasController extends Controller
{
    /**
     * Menampilkan daftar tugas.
     */
    public function index(Request $request): Response
    {
        $query = Tugas::with([
            'materi',
            'pelatihan',
            'kelas',
            'tahap',
            'creator',
        ]);

        // Filter berdasarkan materi
        if ($request->filled('materi_id')) {
            $query->where('materi_id', $request->materi_id);
        }

        // Pencarian berdasarkan judul
        if ($request->filled('search')) {
            $query->where(
                'judul',
                'like',
                '%' . $request->search . '%'
            );
        }

        $tugas = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tugas/Index', [
            'tugas' => $tugas,

            'filters' => [
                'search' => $request->search,
                'materi_id' => $request->materi_id,
            ],

            'materis' => Materi::orderBy('judul')->get(),
        ]);
    }

    /**
     * Form tambah tugas.
     */
    public function create(): Response
    {
        return Inertia::render('Tugas/Create', [
            'materis' => Materi::with([
                'pelatihan',
                'kelas',
                'tahap',
            ])
                ->orderBy('judul')
                ->get(),
        ]);
    }

    /**
     * Menyimpan tugas baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'materi_id' => [
                'required',
                'exists:materi,id',
            ],

            'judul' => [
                'required',
                'string',
                'max:255',
            ],

            'deskripsi' => [
                'nullable',
                'string',
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

        /*
        |--------------------------------------------------------------------------
        | Ambil data materi
        |--------------------------------------------------------------------------
        |
        | Dari materi kita mengambil pelatihan, kelas dan tahap.
        |
        */

        $materi = Materi::findOrFail(
            $validated['materi_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan tugas
        |--------------------------------------------------------------------------
        */

        Tugas::create([
            'pelatihan_id' => $materi->pelatihan_id,
            'kelas_id' => $materi->kelas_id,
            'tahap_id' => $materi->tahap_id,
            'materi_id' => $materi->id,

            'judul' => $validated['judul'],

            'deskripsi' => $validated['deskripsi'] ?? null,

            'instruksi' => $validated['instruksi'] ?? null,

            'deadline' => $validated['deadline'] ?? null,

            'nilai_maksimal' => $validated['nilai_maksimal'],

            'status' => $validated['status'],

            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('tugas.index')
            ->with(
                'success',
                'Tugas berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail tugas.
     */
    public function show(Tugas $tugas): Response
    {
        $tugas->load([
            'materi',
            'pelatihan',
            'kelas',
            'tahap',
            'creator',
            'pengumpulanTugas.peserta',
        ]);

        return Inertia::render('Tugas/Show', [
            'tugas' => $tugas,
        ]);
    }

    /**
     * Form edit tugas.
     */
public function edit(Tugas $tuga)
{
    $tuga->load([
        'materi.pelatihan',
        'materi.kelas',
        'materi.tahap',
    ]);

    $materis = Materi::with([
        'pelatihan',
        'kelas',
        'tahap',
    ])
    ->orderBy('judul')
    ->get();

    return Inertia::render('Tugas/Edit', [
        'tugas' => $tuga,
        'materis' => $materis,
    ]);
}

    /**
     * Update tugas.
     */
public function update(
    Request $request,
    Tugas $tuga
): RedirectResponse {
    $validated = $request->validate([
        'materi_id' => [
            'required',
            'exists:materi,id',
        ],

        'judul' => [
            'required',
            'string',
            'max:255',
        ],

        'deskripsi' => [
            'nullable',
            'string',
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

    $materi = Materi::findOrFail(
        $validated['materi_id']
    );

    $tuga->update([
        'pelatihan_id' => $materi->pelatihan_id,
        'kelas_id' => $materi->kelas_id,
        'tahap_id' => $materi->tahap_id,
        'materi_id' => $materi->id,

        'judul' => $validated['judul'],
        'deskripsi' => $validated['deskripsi'] ?? null,
        'instruksi' => $validated['instruksi'] ?? null,
        'deadline' => $validated['deadline'] ?? null,
        'nilai_maksimal' => $validated['nilai_maksimal'],
        'status' => $validated['status'],
    ]);

    return redirect()
        ->route('tugas.index')
        ->with(
            'success',
            'Tugas berhasil diperbarui.'
        );
}

    /**
     * Hapus tugas.
     */
    public function destroy(Tugas $tugas): RedirectResponse
    {
        $tugas->delete();

        return redirect()
            ->route('tugas.index')
            ->with(
                'success',
                'Tugas berhasil dihapus.'
            );
    }
}