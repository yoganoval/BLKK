<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PesertaTugasController extends Controller
{
    public function index()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $tugas = Tugas::query()
            ->with(['materi'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Peserta/Tugas/Index', [
            'tugas' => $tugas,
        ]);
    }

    public function show(Tugas $tugas)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $tugas->load(['materi']);

        return Inertia::render('Peserta/Tugas/Show', [
            'tugas' => $tugas,
        ]);
    }
}