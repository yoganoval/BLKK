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
     * Menampilkan daftar materi peserta.
     */
    public function index(Request $request)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $materi = Materi::query()
            ->with([
                'pelatihan',
                'kelas',
                'tahap',
                'files',
            ])
            ->where('status', 'aktif')
            ->orderBy('urutan')
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

        abort_unless($materi->status === 'aktif', 404);

        $materi->load([
            'pelatihan',
            'kelas',
            'tahap',
            'files',
        ]);

        return Inertia::render('Peserta/Materi/Show', [
            'materi' => $materi,
        ]);
    }
}
