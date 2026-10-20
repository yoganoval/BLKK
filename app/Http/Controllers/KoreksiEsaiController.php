<?php

namespace App\Http\Controllers;

use App\Models\JawabanKuis;
use App\Models\PercobaanKuis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class KoreksiEsaiController extends Controller
{
    /**
     * Daftar percobaan yang memiliki esai untuk dikoreksi.
     */
    public function index(): Response
    {
        $percobaan = PercobaanKuis::query()
            ->with([
                'peserta',
                'kuis',
                'jawabanKuis.soal',
            ])
            ->where('status', 'selesai')
            ->whereHas('jawabanKuis', function ($query) {
                $query->whereHas('soal', function ($soal) {
                    $soal->where('tipe', 'esai');
                });
            })
            ->latest('selesai_pada')
            ->paginate(10)
            ->withQueryString();

        $percobaan->getCollection()->transform(function ($item) {
            $esai = $item->jawabanKuis->filter(
                fn ($jawaban) => $jawaban->soal?->tipe === 'esai'
            );

            $item->jumlah_esai = $esai->count();
            $item->esai_belum_dikoreksi = $esai->filter(
                fn ($jawaban) => $jawaban->dikoreksi_at === null
            )->count();

            return $item;
        });

        return Inertia::render('KoreksiEsai/Index', [
            'percobaan' => $percobaan,
        ]);
    }

    /**
     * Menampilkan jawaban esai dari satu percobaan.
     */
    public function show(PercobaanKuis $percobaanKuis): Response
    {
        abort_unless($percobaanKuis->status === 'selesai', 404);

        $percobaanKuis->load([
            'peserta',
            'kuis.soal',
            'jawabanKuis.soal',
        ]);

        $jawabanEsai = $percobaanKuis->jawabanKuis
            ->filter(fn ($jawaban) => $jawaban->soal?->tipe === 'esai')
            ->sortBy(fn ($jawaban) => $jawaban->soal->urutan)
            ->values()
            ->map(fn ($jawaban) => [
                'id' => $jawaban->id,
                'soal_id' => $jawaban->soal_id,
                'pertanyaan' => $jawaban->soal->pertanyaan,
                'bobot' => (float) $jawaban->soal->bobot,
                'jawaban_text' => $jawaban->jawaban_text,
                'nilai' => $jawaban->dikoreksi_at
                    ? (float) $jawaban->nilai
                    : null,
                'catatan_koreksi' => $jawaban->catatan_koreksi,
                'dikoreksi_at' => $jawaban->dikoreksi_at,
            ]);

        return Inertia::render('KoreksiEsai/Show', [
            'percobaanKuis' => [
                'id' => $percobaanKuis->id,
                'percobaan_ke' => $percobaanKuis->percobaan_ke,
                'status' => $percobaanKuis->status,
                'nilai' => $percobaanKuis->nilai,
                'lulus' => $percobaanKuis->lulus,
                'peserta' => [
                    'id' => $percobaanKuis->peserta->id,
                    'nama' => $percobaanKuis->peserta->nama,
                ],
                'kuis' => [
                    'id' => $percobaanKuis->kuis->id,
                    'judul' => $percobaanKuis->kuis->judul,
                    'nilai_lulus' => $percobaanKuis->kuis->nilai_lulus,
                ],
            ],
            'jawabanEsai' => $jawabanEsai,
        ]);
    }

    /**
     * Simpan nilai dan catatan koreksi, kemudian hitung nilai akhir.
     */
    public function update(
        Request $request,
        PercobaanKuis $percobaanKuis
    ) {
        abort_unless($percobaanKuis->status === 'selesai', 404);

        $data = $request->validate([
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*.nilai' => [
                'required',
                'numeric',
                'min:0',
            ],
            'jawaban.*.catatan_koreksi' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use ($data, $percobaanKuis) {
            $percobaan = PercobaanKuis::query()
                ->whereKey($percobaanKuis->id)
                ->lockForUpdate()
                ->firstOrFail();

            $jawabanIds = array_map(
                'intval',
                array_keys($data['jawaban'])
            );

            $jawabanEsai = JawabanKuis::query()
                ->where('percobaan_kuis_id', $percobaan->id)
                ->whereIn('id', $jawabanIds)
                ->whereHas('soal', fn ($query) => $query->where('tipe', 'esai'))
                ->with('soal')
                ->get()
                ->keyBy('id');

            // Tolak ID jawaban yang bukan esai atau bukan milik percobaan ini.
            if ($jawabanEsai->count() !== count($data['jawaban'])) {
                throw ValidationException::withMessages([
                    'jawaban' => 'Ada jawaban esai yang tidak valid.',
                ]);
            }

            foreach ($data['jawaban'] as $id => $input) {
                $jawaban = $jawabanEsai->get((int) $id);
                $bobot = max(0, (float) $jawaban->soal->bobot);
                $nilai = (float) $input['nilai'];

                if ($nilai > $bobot) {
                    throw ValidationException::withMessages([
                        "jawaban.$id.nilai" =>
                            "Nilai tidak boleh melebihi bobot soal ($bobot).",
                    ]);
                }

                $jawaban->update([
                    'nilai' => $nilai,
                    'benar' => null,
                    'catatan_koreksi' => $input['catatan_koreksi'] ?? null,
                    'dikoreksi_at' => now(),
                ]);
            }

            $kuis = $percobaan->kuis()
                ->with('soal')
                ->firstOrFail();

            $semuaJawabanEsai = JawabanKuis::query()
                ->where('percobaan_kuis_id', $percobaan->id)
                ->whereHas('soal', fn ($query) => $query->where('tipe', 'esai'))
                ->get();

            $masihMenunggu = $semuaJawabanEsai->contains(
                fn ($jawaban) => $jawaban->dikoreksi_at === null
            );

            // Ambil semua nilai soal untuk menghitung hasil akhir.
            $semuaJawaban = JawabanKuis::query()
                ->where('percobaan_kuis_id', $percobaan->id)
                ->with('soal')
                ->get();

            $totalBobot = $kuis->soal->sum(
                fn ($soal) => max(0, (float) $soal->bobot)
            );

            $nilaiDiperoleh = 0;
            $jumlahBenar = 0;
            $jumlahDijawab = 0;

            foreach ($semuaJawaban as $jawaban) {
                $soal = $jawaban->soal;

                if (! $soal) {
                    continue;
                }

                if ($soal->tipe === 'esai') {
                    if ($jawaban->dikoreksi_at === null) {
                        continue;
                    }

                    $nilaiDiperoleh += min(
                        max(0, (float) $soal->bobot),
                        max(0, (float) $jawaban->nilai)
                    );

                    if ($jawaban->answered_at) {
                        $jumlahDijawab++;
                    }

                    continue;
                }

                $nilaiDiperoleh += max(0, (float) $jawaban->nilai);

                if ($jawaban->opsi_jawaban_id) {
                    $jumlahDijawab++;

                    if ($jawaban->benar) {
                        $jumlahBenar++;
                    }
                }
            }

            $nilaiAkhir = $masihMenunggu
                ? null
                : ($totalBobot > 0
                    ? round(($nilaiDiperoleh / $totalBobot) * 100, 2)
                    : 0);

            $percobaan->update([
                'nilai' => $nilaiAkhir,
                'jumlah_benar' => $jumlahBenar,
                'jumlah_dijawab' => $jumlahDijawab,
                'lulus' => $nilaiAkhir === null
                    ? null
                    : $nilaiAkhir >= (float) $kuis->nilai_lulus,
            ]);
        });

        return redirect()
            ->route('koreksi-esai.show', [
                'percobaanKuis' => $percobaanKuis->id,
            ])
            ->with('success', 'Koreksi esai berhasil disimpan.');
    }
}