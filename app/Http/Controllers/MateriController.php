<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Pelatihan;
use App\Models\Tahap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    /**
     * Menampilkan daftar materi.
     */
    public function index(Request $request): Response
    {
        $query = Materi::with([
            'pelatihan',
            'kelas',
            'tahap',
            'creator',
            'files',
        ]);

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('pelatihan_id')) {
            $query->where('pelatihan_id', $request->pelatihan_id);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('tahap_id')) {
            $query->where('tahap_id', $request->tahap_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $materis = $query
            ->orderBy('urutan')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Materi/Index', [
            'materis' => $materis,

            'filters' => [
                'search' => $request->search,
                'pelatihan_id' => $request->pelatihan_id,
                'kelas_id' => $request->kelas_id,
                'tahap_id' => $request->tahap_id,
                'status' => $request->status,
            ],

            'pelatihans' => Pelatihan::orderBy('nama')->get(),

            'kelas' => Kelas::orderBy('nama')->get(),

            'tahaps' => Tahap::orderBy('nama')->get(),
        ]);
    }

    /**
     * Form tambah materi.
     */
    public function create(): Response
    {
        return Inertia::render('Materi/Create', [
            'pelatihans' => Pelatihan::query()
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                ]),

            'kelas' => Kelas::query()
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                ]),

            'tahaps' => Tahap::query()
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                ]),
        ]);
    }

    /**
     * Menyimpan materi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pelatihan_id' => [
                'required',
                'exists:pelatihan,id',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'tahap_id' => [
                'nullable',
                'exists:tahap,id',
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

            'isi_materi' => [
                'nullable',
                'string',
            ],

            'urutan' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],
        ]);

        $validated['created_by'] = auth()->id();

        Materi::create($validated);

        return redirect()
            ->route('materi.index')
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail materi.
     */
    public function show(Materi $materi): Response
    {
        $materi->load([
            'pelatihan:id,nama',
            'kelas:id,nama',
            'tahap:id,nama',
            'creator:id,name',
            'files',
            'tugas',
            'kuis.soal.opsiJawaban',
        ]);

        return Inertia::render('Materi/Show', [
            'materi' => $materi,
        ]);
    }

    /**
     * Form edit materi.
     */
    public function edit(Materi $materi): Response
    {
        $materi->load('files');

        return Inertia::render('Materi/Edit', [
            'materi' => $materi,

            'pelatihans' => Pelatihan::query()
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                ]),

            'kelas' => Kelas::query()
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                ]),

            'tahaps' => Tahap::query()
                ->orderBy('nama')
                ->get([
                    'id',
                    'nama',
                ]),
        ]);
    }

    /**
     * Update materi.
     */
    public function update(
        Request $request,
        Materi $materi
    ): RedirectResponse {
        $validated = $request->validate([
            'pelatihan_id' => [
                'required',
                'exists:pelatihan,id',
            ],

            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],

            'tahap_id' => [
                'nullable',
                'exists:tahap,id',
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

            'isi_materi' => [
                'nullable',
                'string',
            ],

            'urutan' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],
        ]);

        $materi->update($validated);

        return redirect()
            ->route('materi.index')
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Hapus materi.
     */

    public function destroy(Materi $materi): RedirectResponse
    {
        foreach ($materi->files as $file) {
            if (
                $file->file_path &&
                Storage::disk('public')->exists($file->file_path)
            ) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $materi->delete();

        return redirect()
            ->route('materi.index')
            ->with('success', 'Materi berhasil dihapus.');
    }
}