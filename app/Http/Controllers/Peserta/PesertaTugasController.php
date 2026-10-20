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

class PesertaTugasController extends Controller
{
    private function peserta()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        return $peserta;
    }

    private function pendaftaranAktif()
    {
        return $this->peserta()
            ->pendaftarans()
            ->where('status', 'aktif')
            ->get();
    }

    private function tugasPesertaQuery()
    {
        $pendaftaran = $this->pendaftaranAktif();

        $programIds = $pendaftaran
            ->pluck('program_pelatihan_id')
            ->filter()
            ->unique();

        $kelasIds = $pendaftaran
            ->pluck('kelas_id')
            ->filter()
            ->unique();

        return Tugas::query()
            ->where('status', 'published')
            ->whereIn('kelas_id', $kelasIds)
            ->whereIn('program_pelatihan_id', $programIds);
    }

    public function index(): Response
    {
        $peserta = $this->peserta();

        $tugas = $this->tugasPesertaQuery()
            ->with([
                'programPelatihan.pelatihan',
                'programPelatihan.tahap',
                'kelas',
                'pengumpulanTugas' => function ($query) use ($peserta) {
                    $query->where('peserta_id', $peserta->id);
                },
            ])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Peserta/Tugas/Index', [
            'tugas' => $tugas,
        ]);
    }

    public function show(Tugas $tugas): Response
    {
        $peserta = $this->peserta();

        abort_unless(
            $this->tugasPesertaQuery()
                ->whereKey($tugas->id)
                ->exists(),
            404
        );

        $tugas->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
        ]);

        $pengumpulan = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        return Inertia::render('Peserta/Tugas/Show', [
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan,
        ]);
    }

    public function store(
        Request $request,
        Tugas $tugas
    ): RedirectResponse {
        $peserta = $this->peserta();

        abort_unless(
            $this->tugasPesertaQuery()
                ->whereKey($tugas->id)
                ->exists(),
            404
        );

        $data = $request->validate([
            'jawaban' => ['nullable', 'string', 'max:50000'],
            'file' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png',
            ],
        ], [
            'jawaban.max' => 'Jawaban teks maksimal 50.000 karakter.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.mimes' => 'Format file tidak didukung.',
        ]);

        $jawaban = trim($data['jawaban'] ?? '');

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

        $fileBaru = null;
        $fileLama = $pengumpulanLama?->file_path;

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
            $pengumpulanId = DB::transaction(
                function () use (
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

                    $dataSimpan = [
                        'jawaban' => $jawaban !== ''
                            ? $jawaban
                            : null,
                        'file_path' => $fileBaru ?? $fileLama,
                        'dikumpulkan_at' => $waktu,
                        'status' => $tugas->deadline
                            && $waktu->gt($tugas->deadline)
                                ? 'terlambat'
                                : 'dikumpulkan',
                    ];

                    if ($pengumpulan) {
                        $pengumpulan->fill($dataSimpan);
                        $pengumpulan->save();
                    } else {
                        $pengumpulan = PengumpulanTugas::create([
                            'tugas_id' => $tugas->id,
                            'peserta_id' => $peserta->id,
                            ...$dataSimpan,
                        ]);
                    }

                    return $pengumpulan->id;
                }
            );
        } catch (\Throwable $e) {
            if ($fileBaru) {
                Storage::disk('public')->delete($fileBaru);
            }

            throw $e;
        }

        if ($fileBaru && $fileLama && $fileBaru !== $fileLama) {
            Storage::disk('public')->delete($fileLama);
        }

        $pengumpulan = PengumpulanTugas::query()
            ->with('tugas')
            ->findOrFail($pengumpulanId);

        logger()->info('Pengumpulan tugas tersimpan', [
            'id' => $pengumpulan->id,
            'tugas_id' => $pengumpulan->tugas_id,
            'peserta_id' => $pengumpulan->peserta_id,
            'dikumpulkan_at' => $pengumpulan->dikumpulkan_at
                ?->toDateTimeString(),
            'status' => $pengumpulan->status,
        ]);

        return redirect()
            ->route('peserta.tugas.show', $tugas->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
