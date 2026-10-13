<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\ProgresPeserta;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PesertaProgresBelajarController extends Controller
{
    public function index()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $progres = ProgresPeserta::query()
            ->where('peserta_id', $peserta->id)
            ->with(['materi'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Peserta/Progres/Index', [
            'progres' => $progres,
        ]);
    }
}