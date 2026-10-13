<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Kuis;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PesertaKuisController extends Controller
{
    public function index()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $kuis = Kuis::query()
            ->latest()
            ->paginate(10);

        return Inertia::render('Peserta/Kuis/Index', [
            'kuis' => $kuis,
        ]);
    }

    public function show(Kuis $kuis)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $kuis->load('soal');

        return Inertia::render('Peserta/Kuis/Show', [
            'kuis' => $kuis,
        ]);
    }
}