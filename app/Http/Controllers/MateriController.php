<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\MateriFile;
use App\Models\ProgramPelatihan;
use App\Models\Tahap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MateriController extends Controller
{
    /**
     * Menampilkan daftar materi, pencarian, dan filter.
     */
    public function index(Request $request): Response
    {
        $search = trim($request->input('search', ''));

        $materis = Materi::query()
            ->with([
                'programPelatihan.pelatihan',
                'programPelatihan.tahap',
                'kelas',
                'creator',
                'files',
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
                    $kelasId = $request->input('kelas_id');

                    $query->whereHas('kelas', function ($kelasQuery) use ($kelasId) {
                        $kelasQuery->where('kelas.id', $kelasId);
                    });
                }
            )
            ->when(
                $request->filled('tahap_id'),
                function ($query) use ($request) {
                    $tahapId = $request->input('tahap_id');

                    $query->whereHas(
                        'programPelatihan',
                        function ($programQuery) use ($tahapId) {
                            $programQuery->where('tahap_id', $tahapId);
                        }
                    );
                }
            )
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where('status', $request->input('status'));
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Materi/Index', [
            'materis' => $materis,
            'filters' => $request->only([
                'search',
                'program_pelatihan_id',
                'kelas_id',
                'tahap_id',
                'status',
            ]),
            'programPelatihans' => ProgramPelatihan::with([
                'pelatihan:id,nama',
                'tahap:id,nama',
            ])->orderBy('nama')->get([
                'id',
                'nama',
                'pelatihan_id',
                'tahap_id',
            ]),
            'kelass' => Kelas::orderBy('nama')->get([
                'id',
                'nama',
                'durasi_hari',
            ]),
            'tahaps' => Tahap::orderBy('nama')->get([
                'id',
                'nama',
            ]),
        ]);
    }

    /**
     * Form tambah materi.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Materi/Create', [
            'programPelatihans' => ProgramPelatihan::with([
                'pelatihan:id,nama',
                'tahap:id,nama',
            ])->orderBy('nama')->get([
                'id',
                'nama',
                'pelatihan_id',
                'tahap_id',
            ]),

            'kelass' => Kelas::orderBy('nama')->get([
                'id',
                'nama',
                'durasi_hari',
            ]),
        ]);
    }

    /**
     * Menyimpan materi beserta kelas-kelas yang dipilih.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_pelatihan_id' => [
                'required',
                'integer',
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
                'distinct',
                'exists:kelas,id',
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

        DB::transaction(function () use ($validated) {
            $kelasIds = $validated['kelas_ids'];

            $materi = Materi::create([
                'program_pelatihan_id' => $validated['program_pelatihan_id'],

                // Menjaga kolom kelas_id lama tetap terisi
                // jika kolom tersebut masih wajib di database.
                'kelas_id' => $kelasIds[0],

                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'isi_materi' => $validated['isi_materi'] ?? null,
                'urutan' => $validated['urutan'],
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ]);

            // Simpan seluruh kelas yang dicentang ke tabel kelas_materi.
            $materi->kelas()->sync($kelasIds);
        });

        return redirect()
            ->route('materi.index')
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Detail materi.
     */
    public function show(Materi $materi): Response
    {
        $materi->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'creator',
            'files',
        ]);

        return Inertia::render('Admin/Materi/Show', [
            'materi' => $materi,
        ]);
    }

    /**
     * Form edit materi.
     */
    public function edit(Materi $materi): Response
    {
        $materi->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'files',
        ]);

        return Inertia::render('Admin/Materi/Edit', [
            'materi' => $materi,

            'programPelatihans' => ProgramPelatihan::with([
                'pelatihan:id,nama',
                'tahap:id,nama',
            ])->orderBy('nama')->get([
                'id',
                'nama',
                'pelatihan_id',
                'tahap_id',
            ]),

            'kelass' => Kelas::orderBy('nama')->get([
                'id',
                'nama',
                'durasi_hari',
            ]),
        ]);
    }

    /**
     * Memperbarui materi dan kelas-kelas yang dipilih.
     */
    public function update(Request $request, Materi $materi): RedirectResponse
    {
        $validated = $request->validate([
            'program_pelatihan_id' => [
                'required',
                'integer',
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
                'distinct',
                'exists:kelas,id',
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

        DB::transaction(function () use ($validated, $materi) {
            $kelasIds = $validated['kelas_ids'];

            $materi->update([
                'program_pelatihan_id' => $validated['program_pelatihan_id'],
                'kelas_id' => $kelasIds[0],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'isi_materi' => $validated['isi_materi'] ?? null,
                'urutan' => $validated['urutan'],
                'status' => $validated['status'],
            ]);

            // Menyamakan kelas pivot dengan pilihan terbaru.
            // Kelas yang tidak dicentang akan dilepas.
            $materi->kelas()->sync($kelasIds);
        });

        return redirect()
            ->route('materi.index')
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Menghapus materi beserta file dan relasi kelasnya.
     */
    public function destroy(Materi $materi): RedirectResponse
    {
        DB::transaction(function () use ($materi) {
            // Hapus file fisik dari storage, jika ada.
            foreach ($materi->files as $file) {
                if (!empty($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
            }

            // Hapus data file dari database.
            $materi->files()->delete();

            // Lepaskan seluruh relasi kelas.
            $materi->kelas()->detach();

            // Hapus materi.
            $materi->delete();
        });

        return redirect()
            ->route('materi.index')
            ->with('success', 'Materi berhasil dihapus.');
    }
}