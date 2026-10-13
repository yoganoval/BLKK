<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
            'penilai',
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

            'jawaban' => [
                'nullable',
                'string',
            ],

            'file' => [
                'nullable',
                'file',
                'max:51200',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,webp,zip,rar,mp4,webm',
                'required_without:jawaban',
            ],
        ]);

        if (
            blank($validated['jawaban'] ?? null) &&
            !$request->hasFile('file')
        ) {
            return back()
                ->withErrors([
                    'jawaban' => 'Isi jawaban atau lampirkan file terlebih dahulu.',
                ])
                ->withInput();
        }

        $sudahMengumpulkan = PengumpulanTugas::where(
            'tugas_id',
            $validated['tugas_id']
        )
            ->where('peserta_id', $validated['peserta_id'])
            ->exists();

        if ($sudahMengumpulkan) {
            return back()
                ->withErrors([
                    'peserta_id' => 'Peserta sudah memiliki pengumpulan untuk tugas ini.',
                ])
                ->withInput();
        }

        $data = [
            'tugas_id' => $validated['tugas_id'],
            'peserta_id' => $validated['peserta_id'],
            'jawaban' => $validated['jawaban'] ?? null,
            'dikumpulkan_at' => now(),
            'status' => 'dikumpulkan',
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store(
                'pengumpulan-tugas',
                'public'
            );
        }

        PengumpulanTugas::create($data);

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Detail pengumpulan tugas.
     */
    public function show(
        PengumpulanTugas $pengumpulanTugas
    ): Response {
        $pengumpulanTugas->load([
            'tugas.materi',
            'peserta',
            'penilai',
        ]);

        return Inertia::render('PengumpulanTugas/Show', [
            'pengumpulanTugas' => $pengumpulanTugas,
        ]);
    }

    /**
     * Form edit dan penilaian tugas.
     */
    public function edit(
        PengumpulanTugas $pengumpulanTugas
    ): Response {
        $pengumpulanTugas->load([
            'tugas',
            'peserta',
            'penilai',
        ]);

        return Inertia::render('PengumpulanTugas/Edit', [
            'pengumpulanTugas' => $pengumpulanTugas,
        ]);
    }

    /**
     * Menyimpan nilai, umpan balik, dan status.
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
                'max:100',
            ],

            'feedback' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'required',
                Rule::in([
                    'belum_dikumpulkan',
                    'dikumpulkan',
                    'dinilai',
                    'terlambat',
                ]),
            ],
        ]);

        if ($validated['status'] === 'dinilai') {
            if ($validated['nilai'] === null) {
                return back()
                    ->withErrors([
                        'nilai' => 'Nilai harus diisi sebelum tugas ditandai sudah dinilai.',
                    ])
                    ->withInput();
            }

            $validated['dinilai_oleh'] = auth()->id();
            $validated['dinilai_at'] = now();
        } else {
            $validated['dinilai_oleh'] = null;
            $validated['dinilai_at'] = null;
        }

        $pengumpulanTugas->update($validated);

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Penilaian tugas berhasil disimpan.');
    }

    /**
     * Menghapus pengumpulan beserta file terkait.
     */
    public function destroy(
        PengumpulanTugas $pengumpulanTugas
    ): RedirectResponse {
        if ($pengumpulanTugas->file_path) {
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
