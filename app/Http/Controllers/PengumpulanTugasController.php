<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PengumpulanTugasController extends Controller
{
    /**
     * Daftar semua pengumpulan tugas.
     */
    public function index(Request $request): Response
    {
        $query = PengumpulanTugas::with([
            'tugas',
            'peserta',
        ]);

        if ($request->filled('tugas_id')) {
            $query->where('tugas_id', $request->tugas_id);
        }

        if ($request->filled('peserta_id')) {
            $query->where('peserta_id', $request->peserta_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengumpulanTugas = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('PengumpulanTugas/Index', [
            'pengumpulanTugas' => $pengumpulanTugas,

            'filters' => [
                'tugas_id' => $request->tugas_id,
                'peserta_id' => $request->peserta_id,
                'status' => $request->status,
            ],

            'tugas' => Tugas::orderBy('judul')->get(),

            'pesertas' => Peserta::orderBy('nama')->get(),
        ]);
    }

    /**
     * Form pengumpulan tugas.
     */
    public function create(Request $request): Response
    {
        $tugas = null;

        if ($request->filled('tugas_id')) {
            $tugas = Tugas::with('materi')
                ->findOrFail($request->tugas_id);
        }

        return Inertia::render('PengumpulanTugas/Create', [
            'tugas' => $tugas,

            'tugasList' => Tugas::with('materi')
                ->orderBy('judul')
                ->get(),

            'pesertas' => Peserta::orderBy('nama')->get(),
        ]);
    }

    /**
     * Menyimpan pengumpulan tugas.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tugas_id' => [
                'required',
                'exists:tugas,id',
            ],

            'peserta_id' => [
                'required',
                'exists:peserta,id',
            ],

            'file' => [
                'nullable',
                'file',
                'max:51200',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,webp,zip,rar,mp4,webm',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        $tugas = Tugas::findOrFail($validated['tugas_id']);

        $data = [
            'tugas_id' => $validated['tugas_id'],
            'peserta_id' => $validated['peserta_id'],
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'dikumpulkan',
            'tanggal_kumpul' => now(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $path = $file->store(
                'pengumpulan-tugas/' . $tugas->id,
                'public'
            );

            $data['file_path'] = $path;
            $data['nama_file'] = $file->getClientOriginalName();
            $data['tipe_file'] = $file->getClientMimeType();
            $data['ukuran'] = round(
                $file->getSize() / 1024
            );
        }

        PengumpulanTugas::create($data);

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Detail pengumpulan.
     */
    public function show(
        PengumpulanTugas $pengumpulanTugas
    ): Response {
        $pengumpulanTugas->load([
            'tugas.materi',
            'peserta',
        ]);

        return Inertia::render(
            'PengumpulanTugas/Show',
            [
                'pengumpulanTugas' => $pengumpulanTugas,
            ]
        );
    }

    /**
     * Form edit pengumpulan / penilaian.
     */
    public function edit(
        PengumpulanTugas $pengumpulanTugas
    ): Response {
        $pengumpulanTugas->load([
            'tugas',
            'peserta',
        ]);

        return Inertia::render(
            'PengumpulanTugas/Edit',
            [
                'pengumpulanTugas' => $pengumpulanTugas,
            ]
        );
    }

    /**
     * Update pengumpulan / penilaian.
     */
    public function update(
        Request $request,
        PengumpulanTugas $pengumpulanTugas
    ): RedirectResponse {
        $validated = $request->validate([
            'nilai' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'umpan_balik' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:dikumpulkan,dinilai,revisi',
            ],
        ]);

        $pengumpulanTugas->update($validated);

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Pengumpulan tugas berhasil diperbarui.');
    }

    /**
     * Hapus pengumpulan.
     */
    public function destroy(
        PengumpulanTugas $pengumpulanTugas
    ): RedirectResponse {
        if (
            $pengumpulanTugas->file_path &&
            Storage::disk('public')->exists(
                $pengumpulanTugas->file_path
            )
        ) {
            Storage::disk('public')->delete(
                $pengumpulanTugas->file_path
            );
        }

        $pengumpulanTugas->delete();

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Pengumpulan tugas berhasil dihapus.');
    }
}