<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PesertaSertifikatController extends Controller
{
    public function index()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        $sertifikat = Sertifikat::query()
            ->where('peserta_id', $peserta->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Peserta/Sertifikat/Index', [
            'sertifikat' => $sertifikat,
        ]);
    }

    public function download(Sertifikat $sertifikat)
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless($peserta, 401);

        abort_unless(
            (int) $sertifikat->peserta_id === (int) $peserta->id,
            403
        );

        abort_unless(
            Storage::disk('public')->exists($sertifikat->file_path),
            404
        );

        return Storage::disk('public')->download(
            $sertifikat->file_path
        );
    }
}