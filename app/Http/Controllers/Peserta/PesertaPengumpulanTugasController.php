<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PesertaPengumpulanTugasController extends Controller
{
    /**
     * Menyimpan atau memperbarui pengumpulan tugas peserta.
     */
    public function store(
        Request $request,
        Tugas $tugas
    ): RedirectResponse {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        // Pastikan tugas tersedia untuk program dan kelas peserta.
        $pendaftaran = $peserta->pendaftarans()
            ->where('status', 'aktif')
            ->get();

        $programIds = $pendaftaran
            ->pluck('program_pelatihan_id')
            ->filter()
            ->unique();

        $kelasIds = $pendaftaran
            ->pluck('kelas_id')
            ->filter()
            ->unique();

        abort_unless(
            $tugas->status === 'published'
            && $programIds->contains($tugas->program_pelatihan_id)
            && $kelasIds->contains($tugas->kelas_id),
            404
        );

        $validated = $request->validate([
            'jawaban' => ['nullable', 'string', 'max:50000'],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png',
                'max:10240',
            ],
        ], [
            'jawaban.max' => 'Jawaban maksimal 50.000 karakter.',
            'file.mimes' => 'Format berkas tidak didukung.',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $jawaban = trim($validated['jawaban'] ?? '');

        $pengumpulanLama = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if (
            $jawaban === ''
            && !$request->hasFile('file')
            && !$pengumpulanLama?->file_path
        ) {
            throw ValidationException::withMessages([
                'jawaban' => 'Isi jawaban atau unggah berkas tugas.',
            ]);
        }

        $fileLama = $pengumpulanLama?->file_path;
        $fileBaru = null;

        if ($request->hasFile('file')) {
            $fileBaru = $request->file('file')->store(
                'pengumpulan-tugas/' . $peserta->id,
                'public'
            );

            if (!$fileBaru) {
                throw ValidationException::withMessages([
                    'file' => 'Berkas gagal disimpan. Silakan coba kembali.',
                ]);
            }
        }

        try {
            DB::transaction(function () use (
                $tugas,
                $peserta,
                $jawaban,
                $fileBaru,
                $fileLama
            ) {
                $waktu = now();

                $pengumpulan = PengumpulanTugas::query()
                    ->where('tugas_id', $tugas->id)
                    ->where('peserta_id', $peserta->id)
                    ->lockForUpdate()
                    ->first();

                $data = [
                    'jawaban' => $jawaban !== ''
                        ? $jawaban
                        : null,

                    'file_path' => $fileBaru ?? $fileLama,

                    // Waktu pengumpulan diambil dari server.
                    'dikumpulkan_at' => $waktu,

                    'status' => $tugas->deadline
                        && $waktu->gt($tugas->deadline)
                            ? 'terlambat'
                            : 'dikumpulkan',
                ];

                if ($pengumpulan) {
                    // Tidak mengubah nilai, feedback, atau data penilai.
                    $pengumpulan->fill($data);
                    $pengumpulan->save();
                } else {
                    PengumpulanTugas::create([
                        'tugas_id' => $tugas->id,
                        'peserta_id' => $peserta->id,
                        ...$data,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            if ($fileBaru) {
                Storage::disk('public')->delete($fileBaru);
            }

            throw $e;
        }

        // Hapus berkas lama hanya setelah transaksi berhasil.
        if (
            $fileBaru
            && $fileLama
            && $fileBaru !== $fileLama
        ) {
            Storage::disk('public')->delete($fileLama);
        }

        return redirect()
            ->route('peserta.tugas.show', $tugas->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Riwayat pengumpulan tugas peserta.
     */
    public function index(): Response
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $pengumpulan = PengumpulanTugas::query()
            ->where('peserta_id', $peserta->id)
            ->with([
                'tugas.programPelatihan.pelatihan',
                'tugas.programPelatihan.tahap',
                'tugas.kelas',
            ])
            ->orderByDesc('dikumpulkan_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render(
            'Peserta/Tugas/Pengumpulan',
            [
                'pengumpulan' => $pengumpulan,
            ]
        );
    }
}
