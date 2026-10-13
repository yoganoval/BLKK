<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PesertaPengumpulanTugasController extends Controller
{
    public function store(Request $request, Tugas $tugas)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $validated = $request->validate([
            'jawaban' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip', 'max:10240'],
        ]);

        abort_unless(
            ! empty($validated['jawaban']) || $request->hasFile('file'),
            422,
            'Isi jawaban atau unggah berkas tugas.'
        );

        $data = [
            'peserta_id' => $peserta->id,
            'tugas_id' => $tugas->id,
            'jawaban' => $validated['jawaban'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')
                ->store('pengumpulan-tugas', 'public');
        }

        PengumpulanTugas::create($data);

        return back()->with(
            'success',
            'Tugas berhasil dikumpulkan.'
        );
    }

    public function index()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $pengumpulan = PengumpulanTugas::query()
            ->where('peserta_id', $peserta->id)
            ->with('tugas')
            ->latest()
            ->paginate(10);

        return Inertia::render('Peserta/Tugas/Pengumpulan', [
            'pengumpulan' => $pengumpulan,
        ]);
    }
}