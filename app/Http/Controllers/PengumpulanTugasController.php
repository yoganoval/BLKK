<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PengumpulanTugasController extends Controller
{
    /**
     * Menampilkan daftar pengumpulan tugas.
     */
    public function index(Request $request): Response
    {
        $query = PengumpulanTugas::with([
            'tugas',
            'peserta',
            'penilai',
        ]);

        if ($request->filled('tugas_id')) {
            $query->where('tugas_id', $request->input('tugas_id'));
        }

        if ($request->filled('peserta_id')) {
            $query->where('peserta_id', $request->input('peserta_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pengumpulanTugas = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('PengumpulanTugas/Index', [
            'pengumpulanTugas' => $pengumpulanTugas,

            'filters' => [
                'tugas_id' => $request->input('tugas_id', ''),
                'peserta_id' => $request->input('peserta_id', ''),
                'status' => $request->input('status', ''),
            ],

            'tugas' => Tugas::query()
                ->orderBy('judul')
                ->get(),

            'pesertas' => Peserta::query()
                ->orderBy('nama')
                ->get(),
        ]);
    }

    /**
     * Menampilkan form pengumpulan tugas.
     */
    public function create(Request $request): Response
    {
        $tugas = null;

        if ($request->filled('tugas_id')) {
            $tugas = Tugas::query()
                ->findOrFail($request->input('tugas_id'));
        }

        return Inertia::render('PengumpulanTugas/Create', [
            'tugas' => $tugas,

            'tugasList' => Tugas::query()
                ->orderBy('judul')
                ->get(),

            'pesertas' => Peserta::query()
                ->orderBy('nama')
                ->get(),
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
                'integer',
                'exists:tugas,id',
            ],

            'peserta_id' => [
                'required',
                'integer',
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

        $jawaban = trim($validated['jawaban'] ?? '');

        if ($jawaban === '' && !$request->hasFile('file')) {
            return back()
                ->withErrors([
                    'jawaban' => 'Isi jawaban atau lampirkan file terlebih dahulu.',
                ])
                ->withInput();
        }

        $sudahMengumpulkan = PengumpulanTugas::query()
            ->where('tugas_id', $validated['tugas_id'])
            ->where('peserta_id', $validated['peserta_id'])
            ->exists();

        if ($sudahMengumpulkan) {
            return back()
                ->withErrors([
                    'peserta_id' => 'Peserta sudah memiliki pengumpulan untuk tugas ini.',
                ])
                ->withInput();
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store(
                'pengumpulan-tugas',
                'public'
            );
        }

        try {
            PengumpulanTugas::create([
                'tugas_id' => $validated['tugas_id'],
                'peserta_id' => $validated['peserta_id'],
                'jawaban' => $jawaban !== '' ? $jawaban : null,
                'file_path' => $filePath,
                'dikumpulkan_at' => now(),
                'status' => 'dikumpulkan',
            ]);
        } catch (\Throwable $exception) {
            if ($filePath !== null) {
                Storage::disk('public')->delete($filePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Menampilkan detail pengumpulan tugas.
     */
    public function show(
        PengumpulanTugas $pengumpulanTugas
    ): Response {
        $pengumpulanTugas->load([
            'tugas',
            'peserta',
            'penilai',
        ]);

        return Inertia::render('PengumpulanTugas/Show', [
            'pengumpulanTugas' => $pengumpulanTugas,
        ]);
    }

    /**
     * Menampilkan form edit dan penilaian.
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
     * Memperbarui nilai, feedback, dan status pengumpulan.
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
            if (
                !isset($validated['nilai'])
                || $validated['nilai'] === ''
            ) {
                return back()
                    ->withErrors([
                        'nilai' => 'Nilai harus diisi sebelum tugas ditandai sudah dinilai.',
                    ])
                    ->withInput();
            }

            $validated['dinilai_oleh'] = Auth::id();
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
     * Menghapus pengumpulan tugas dan file terkait.
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
