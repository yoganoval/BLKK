<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Peserta;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PesertaTugasController extends Controller
{
    /**
     * Daftar tugas peserta.
     */
    public function index(): Response
    {
        $peserta = $this->getPeserta();

        $tugas = Tugas::with([
            'materi',
            'pelatihan',
            'kelas',
            'tahap',
        ])
        ->where('status', 'published')
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($tugas) use ($peserta) {

            $pengumpulan = PengumpulanTugas::where(
                'tugas_id',
                $tugas->id
            )
            ->where(
                'peserta_id',
                $peserta->id
            )
            ->latest()
            ->first();

            return [
                'id' => $tugas->id,
                'judul' => $tugas->judul,
                'deskripsi' => $tugas->deskripsi,
                'deadline' => $tugas->deadline,
                'nilai_maksimal' => $tugas->nilai_maksimal,
                'materi' => $tugas->materi,
                'pelatihan' => $tugas->pelatihan,
                'kelas' => $tugas->kelas,
                'tahap' => $tugas->tahap,

                'pengumpulan' => $pengumpulan ? [
                    'id' => $pengumpulan->id,
                    'file' => $pengumpulan->file,
                    'catatan' => $pengumpulan->catatan,
                    'nilai' => $pengumpulan->nilai,
                    'feedback' => $pengumpulan->feedback,
                    'dikumpulkan_at' =>
                        $pengumpulan->dikumpulkan_at,
                ] : null,
            ];
        });

        return Inertia::render(
            'Peserta/Tugas/Index',
            [
                'tugas' => $tugas,
            ]
        );
    }

    /**
     * Detail tugas.
     */
    public function show(Tugas $tugas): Response
    {
        $peserta = $this->getPeserta();

        abort_if(
            $tugas->status !== 'published',
            404
        );

        $tugas->load([
            'materi',
            'pelatihan',
            'kelas',
            'tahap',
        ]);

        $pengumpulan = PengumpulanTugas::where(
            'tugas_id',
            $tugas->id
        )
        ->where(
            'peserta_id',
            $peserta->id
        )
        ->latest()
        ->first();

        return Inertia::render(
            'Peserta/Tugas/Show',
            [
                'tugas' => $tugas,
                'pengumpulan' => $pengumpulan,
            ]
        );
    }

    /**
     * Peserta mengumpulkan tugas.
     */
    public function store(
        Request $request,
        Tugas $tugas
    ): RedirectResponse {
        $peserta = $this->getPeserta();

        abort_if(
            $tugas->status !== 'published',
            404
        );

        if (
            $tugas->deadline &&
            now()->greaterThan($tugas->deadline)
        ) {
            return back()->with(
                'error',
                'Batas waktu pengumpulan tugas sudah berakhir.'
            );
        }

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar',
            ],

            'catatan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $file = $request->file('file');

        $path = $file->store(
            'pengumpulan-tugas',
            'public'
        );

        $pengumpulanLama =
            PengumpulanTugas::where(
                'tugas_id',
                $tugas->id
            )
            ->where(
                'peserta_id',
                $peserta->id
            )
            ->first();

        if ($pengumpulanLama) {

            if (
                $pengumpulanLama->file &&
                Storage::disk('public')->exists(
                    $pengumpulanLama->file
                )
            ) {
                Storage::disk('public')->delete(
                    $pengumpulanLama->file
                );
            }

            $pengumpulanLama->update([
                'file' => $path,
                'catatan' =>
                    $validated['catatan'] ?? null,
                'dikumpulkan_at' => now(),
            ]);

        } else {

            PengumpulanTugas::create([
                'tugas_id' => $tugas->id,
                'peserta_id' => $peserta->id,
                'file' => $path,
                'catatan' =>
                    $validated['catatan'] ?? null,
                'dikumpulkan_at' => now(),
            ]);
        }

        return redirect()
            ->route(
                'peserta.tugas.show',
                $tugas
            )
            ->with(
                'success',
                'Tugas berhasil dikumpulkan.'
            );
    }

    private function getPeserta(): Peserta
    {
        $user = Auth::user();

        /*
         * Sesuaikan bagian ini dengan relasi
         * User -> Peserta pada project Anda.
         */
        $peserta = $user->peserta;

        abort_unless(
            $peserta,
            403,
            'Akun ini belum terhubung dengan data peserta.'
        );

        return $peserta;
    }
}