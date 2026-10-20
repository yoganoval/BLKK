<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PesertaMateriController extends Controller
{
    /**
     * Menampilkan daftar materi yang sudah diterbitkan.
     */
    public function index(Request $request)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $materi = Materi::query()
            ->with([
                'programPelatihan.pelatihan',
                'programPelatihan.tahap',
                'kelas',
                'files',
            ])
            ->where('status', 'published')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Peserta/Materi/Index', [
            'materi' => $materi,
        ]);
    }

    /**
     * Menampilkan detail materi.
     */
    public function show(Materi $materi)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        abort_unless($materi->status === 'published', 404);

        $materi->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'files',
        ]);

        return Inertia::render('Peserta/Materi/Show', [
            'materi' => $materi,
        ]);
    }
}
