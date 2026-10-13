<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\JawabanKuis;
use App\Models\Kuis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaJawabanKuisController extends Controller
{
    public function store(Request $request, Kuis $kuis)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $validated = $request->validate([
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*' => ['required'],
        ]);

        foreach ($validated['jawaban'] as $soalId => $jawaban) {
            JawabanKuis::updateOrCreate(
                [
                    'peserta_id' => $peserta->id,
                    'kuis_id' => $kuis->id,
                    'soal_id' => $soalId,
                ],
                [
                    'jawaban' => is_array($jawaban)
                        ? json_encode($jawaban)
                        : $jawaban,
                ]
            );
        }

        return back()->with(
            'success',
            'Jawaban kuis berhasil disimpan.'
        );
    }
}