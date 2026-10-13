<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PengumpulanTugasController extends Controller
{
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
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('PengumpulanTugas/Index', [
            'pengumpulanTugas' => $pengumpulanTugas,
            'filters' => $request->only([
                'tugas_id',
                'peserta_id',
                'status',
            ]),
            'tugas' => Tugas::select('id', 'judul')->get(),
            'pesertas' => Peserta::select('id', 'nama')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PengumpulanTugas/Create', [
            'tugasList' => Tugas::select('id', 'judul')->get(),
            'pesertas' => Peserta::select('id', 'nama')->get(),
        ]);
    }

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
                Rule::unique('pengumpulan_tugas', 'peserta_id')
                    ->where('tugas_id', $request->input('tugas_id')),
            ],
            'jawaban' => [
                'nullable',
                'string',
                'required_without:file',
            ],
            'file' => [
                'nullable',
                'file',
                'max:10240',
                'required_without:jawaban',
            ],
        ]);

        $data = [
            'tugas_id' => $validated['tugas_id'],
            'peserta_id' => $validated['peserta_id'],
            'jawaban' => $validated['jawaban'] ?? null,
            'dikumpulkan_at' => now(),
            'status' => 'dikumpulkan',
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request
                ->file('file')
                ->store('pengumpulan-tugas', 'public');
        }

        PengumpulanTugas::create($data);

        return redirect()
            ->route('pengumpulan-tugas.index')
            ->with('success', 'Pengumpulan tugas berhasil disimpan.');
    }

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

    public function edit(
        PengumpulanTugas $pengumpulanTugas
    ): Response {
        $pengumpulanTugas->load([
            'tugas',
            'peserta',
        ]);

        return Inertia::render('PengumpulanTugas/Edit', [
            'pengumpulanTugas' => $pengumpulanTugas,
        ]);
    }

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
            ->with('success', 'Data pengumpulan tugas berhasil dihapus.');
    }
}