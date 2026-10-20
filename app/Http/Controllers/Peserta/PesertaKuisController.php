<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\PercobaanKuis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PesertaKuisController extends Controller
{
    /**
     * Mengacak SELURUH soal dan pilihan jawaban.
     * Pengacakan disimpan satu kali untuk setiap percobaan.
     */
    private function siapkanPengacakan(
        PercobaanKuis $percobaanKuis,
        Kuis $kuis
    ): void {
        if (! empty($percobaanKuis->soal_ids)) {
            return;
        }

        // Ambil seluruh soal, tanpa membatasi jumlahnya.
        $soalIds = $kuis->soal()
            ->inRandomOrder()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        abort_if(
            empty($soalIds),
            422,
            'Kuis belum memiliki soal.'
        );

        $urutanOpsi = [];

        // Acak pilihan jawaban untuk setiap soal.
        foreach ($soalIds as $soalId) {
            $soal = $kuis->soal()->findOrFail($soalId);

            $urutanOpsi[(string) $soalId] = $soal
                ->opsiJawaban()
                ->inRandomOrder()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        $percobaanKuis->forceFill([
            'soal_ids' => $soalIds,
            'urutan_opsi' => $urutanOpsi,
        ])->save();
    }

    /**
     * Menampilkan daftar kuis yang dapat diakses peserta.
     */
    public function index(): Response
    {
        $peserta = $this->pesertaLogin();

        $pendaftaran = $peserta->pendaftarans()
            ->where('status', 'aktif')
            ->with(['programPelatihan', 'kelas'])
            ->get();

        $programPelatihanIds = $pendaftaran
            ->pluck('program_pelatihan_id')
            ->filter()
            ->unique()
            ->values();

        $kelasIds = $pendaftaran
            ->pluck('kelas_id')
            ->filter()
            ->unique()
            ->values();

        $kuis = Kuis::query()
            ->with([
                'programPelatihan.pelatihan',
                'programPelatihan.tahap',
                'kelas',
                'materi',
            ])
            ->withCount('soal')
            ->where('status', 'published')
            ->whereIn('program_pelatihan_id', $programPelatihanIds)
            ->whereIn('kelas_id', $kelasIds)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $percobaanPerKuis = PercobaanKuis::query()
            ->where('peserta_id', $peserta->id)
            ->whereIn(
                'kuis_id',
                $kuis->getCollection()->pluck('id')
            )
            ->orderByDesc('percobaan_ke')
            ->get([
                'id',
                'kuis_id',
                'percobaan_ke',
                'nilai',
                'status',
                'lulus',
                'mulai_pada',
                'selesai_pada',
            ])
            ->groupBy('kuis_id');

        $kuis->getCollection()->transform(
            function ($item) use ($percobaanPerKuis) {
                $percobaan = $percobaanPerKuis->get(
                    $item->id,
                    collect()
                );

                $item->setAttribute(
                    'percobaan_terakhir',
                    $percobaan->first()
                );

                $item->setAttribute(
                    'jumlah_percobaan',
                    $percobaan->count()
                );

                return $item;
            }
        );

        return Inertia::render('Peserta/Kuis/Index', [
            'kuis' => $kuis,
        ]);
    }

    /**
     * Menampilkan detail kuis tanpa membocorkan soal.
     */
    public function show(Kuis $kuis): Response
    {
        abort_unless($kuis->status === 'published', 404);

        $this->pastikanTerdaftar($kuis);

        $kuis->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'materi',
        ]);

        $kuis->loadCount('soal');

        return Inertia::render('Peserta/Kuis/Show', [
            'kuis' => $kuis,
        ]);
    }

    /**
     * Memastikan peserta sudah login.
     */
    private function pesertaLogin()
    {
        $peserta = Auth::guard('peserta')->user();

        abort_unless(
            $peserta,
            401,
            'Silakan login sebagai peserta.'
        );

        return $peserta;
    }

    /**
     * Memastikan percobaan dimiliki peserta yang login.
     */
    private function pastikanPemilikPercobaan(
        PercobaanKuis $percobaanKuis
    ): void {
        $peserta = $this->pesertaLogin();

        abort_unless(
            (int) $percobaanKuis->peserta_id === (int) $peserta->id,
            403,
            'Anda tidak memiliki akses ke percobaan ini.'
        );
    }

    /**
     * Memastikan peserta terdaftar aktif pada program dan kelas kuis.
     */
    private function pastikanTerdaftar(Kuis $kuis): void
    {
        $peserta = $this->pesertaLogin();

        $terdaftar = $peserta->pendaftarans()
            ->where('status', 'aktif')
            ->where(
                'program_pelatihan_id',
                $kuis->program_pelatihan_id
            )
            ->where('kelas_id', $kuis->kelas_id)
            ->exists();

        abort_unless(
            $terdaftar,
            403,
            'Anda tidak terdaftar pada pelatihan dan kelas kuis ini.'
        );
    }

    /**
     * Memulai kuis atau melanjutkan percobaan aktif.
     */
    public function start(Kuis $kuis)
    {
        $peserta = $this->pesertaLogin();

        abort_unless($kuis->status === 'published', 404);

        $this->pastikanTerdaftar($kuis);

        $percobaanAktif = PercobaanKuis::query()
            ->where('kuis_id', $kuis->id)
            ->where('peserta_id', $peserta->id)
            ->where('status', 'berlangsung')
            ->latest('percobaan_ke')
            ->first();

        if ($percobaanAktif) {
            $this->siapkanPengacakan($percobaanAktif, $kuis);

            return redirect()->route('peserta.kuis.attempt', [
                'percobaanKuis' => $percobaanAktif->id,
            ]);
        }

        $jumlahPercobaan = PercobaanKuis::query()
            ->where('kuis_id', $kuis->id)
            ->where('peserta_id', $peserta->id)
            ->count();

        $maksimal = max(
            1,
            (int) $kuis->percobaan_maksimal
        );

        if ($jumlahPercobaan >= $maksimal) {
            return redirect()
                ->route('peserta.kuis.show', [
                    'kuis' => $kuis->id,
                ])
                ->with(
                    'error',
                    'Batas percobaan kuis sudah habis.'
                );
        }

        abort_if(
            ! $kuis->soal()->exists(),
            422,
            'Kuis belum memiliki soal.'
        );

        $percobaanKuis = DB::transaction(
            function () use ($kuis, $peserta, $jumlahPercobaan) {
                $percobaan = PercobaanKuis::create([
                    'kuis_id' => $kuis->id,
                    'peserta_id' => $peserta->id,
                    'percobaan_ke' => $jumlahPercobaan + 1,
                    'mulai_pada' => now(),
                    'status' => 'berlangsung',
                ]);

                $this->siapkanPengacakan(
                    $percobaan,
                    $kuis
                );

                return $percobaan;
            }
        );

        return redirect()->route('peserta.kuis.attempt', [
            'percobaanKuis' => $percobaanKuis->id,
        ]);
    }

    /**
     * Menampilkan seluruh soal dalam urutan acak tersimpan.
     */
    public function attempt(
        PercobaanKuis $percobaanKuis
    ): Response {
        $this->pastikanPemilikPercobaan($percobaanKuis);

        $percobaanKuis->load('kuis');

        $kuis = $percobaanKuis->kuis;

        abort_unless($kuis, 404);

        if ($percobaanKuis->status !== 'berlangsung') {
            abort_unless(
                $percobaanKuis->status === 'selesai',
                403,
                'Percobaan kuis ini tidak dapat dilanjutkan.'
            );

            return redirect()->route('peserta.kuis.result', [
                'percobaanKuis' => $percobaanKuis->id,
            ]);
        }

        $this->pastikanTerdaftar($kuis);

        $this->siapkanPengacakan($percobaanKuis, $kuis);

        $batasWaktu = $percobaanKuis->mulai_pada
            ? $percobaanKuis->mulai_pada->copy()->addMinutes(
                max(1, (int) $kuis->waktu_menit)
            )
            : now();

        $sisaDetik = max(
            0,
            now()->diffInSeconds($batasWaktu, false)
        );

        if ($sisaDetik <= 0) {
            $this->selesaikanPercobaan($percobaanKuis);

            return redirect()->route('peserta.kuis.result', [
                'percobaanKuis' => $percobaanKuis->id,
            ]);
        }

        $soalIds = array_map(
            'intval',
            $percobaanKuis->soal_ids ?? []
        );

        $urutanOpsi = $percobaanKuis->urutan_opsi ?? [];

        $kuis->load([
            'programPelatihan.pelatihan',
            'programPelatihan.tahap',
            'kelas',
            'materi',
        ]);

        // Ambil seluruh soal yang tersimpan dalam percobaan.
        $soalTerpilih = $kuis->soal()
            ->whereIn('id', $soalIds)
            ->with([
                'opsiJawaban' => function ($query) {
                    // Jangan pernah kirim kunci jawaban ke peserta.
                    $query->select([
                        'id',
                        'soal_id',
                        'opsi',
                        'teks',
                        // 'urutan',
                    ]);
                },
            ])
            ->get()
            ->sortBy(function ($soal) use ($soalIds) {
                $posisi = array_search(
                    (int) $soal->id,
                    $soalIds,
                    true
                );

                return $posisi === false
                    ? PHP_INT_MAX
                    : $posisi;
            })
            ->values();

        // Urutkan opsi berdasarkan hasil pengacakan tersimpan.
        $soalTerpilih->each(
            function ($soal) use ($urutanOpsi) {
                $idsOpsi = $urutanOpsi[(string) $soal->id] ?? [];

                $opsiTerurut = $soal->opsiJawaban
                    ->sortBy(function ($opsi) use ($idsOpsi) {
                        $posisi = array_search(
                            (int) $opsi->id,
                            $idsOpsi,
                            true
                        );

                        return $posisi === false
                            ? PHP_INT_MAX
                            : $posisi;
                    })
                    ->values();

                $soal->setRelation(
                    'opsiJawaban',
                    $opsiTerurut
                );
            }
        );

        $kuis->setRelation('soal', $soalTerpilih);

        $jawabanTersimpan = JawabanKuis::query()
            ->where(
                'percobaan_kuis_id',
                $percobaanKuis->id
            )
            ->get([
                'soal_id',
                'opsi_jawaban_id',
                'jawaban_text',
            ])
            ->keyBy('soal_id')
            ->map(fn ($item) => [
                'opsi_jawaban_id' => $item->opsi_jawaban_id,
                'jawaban_text' => $item->jawaban_text,
            ]);

        return Inertia::render('Peserta/Kuis/Attempt', [
            'kuis' => $kuis,
            'percobaanKuis' => $percobaanKuis,
            'jawabanTersimpan' => $jawabanTersimpan,
            'sisaDetik' => (int) $sisaDetik,
        ]);
    }

    /**
     * Menyimpan atau memperbarui jawaban peserta.
     */
    public function saveAnswer(
        Request $request,
        PercobaanKuis $percobaanKuis
    ) {
        $this->pastikanPemilikPercobaan($percobaanKuis);

        $percobaanKuis->load('kuis');

        $kuis = $percobaanKuis->kuis;

        abort_unless($kuis, 404);

        abort_unless(
            $percobaanKuis->status === 'berlangsung',
            422,
            'Percobaan kuis sudah tidak aktif.'
        );

        $this->pastikanTerdaftar($kuis);

        $this->siapkanPengacakan($percobaanKuis, $kuis);

        $data = $request->validate([
            'soal_id' => [
                'required',
                'integer',
                'exists:soal,id',
            ],
            'opsi_jawaban_id' => [
                'nullable',
                'integer',
                'exists:opsi_jawaban,id',
            ],
            'jawaban_text' => [
                'nullable',
                'string',
                'max:50000',
            ],
        ]);

        $soalIds = array_map(
            'intval',
            $percobaanKuis->soal_ids ?? []
        );

        abort_unless(
            in_array((int) $data['soal_id'], $soalIds, true),
            403,
            'Soal ini tidak termasuk dalam percobaan kuis Anda.'
        );

        $batasWaktu = $percobaanKuis->mulai_pada
            ->copy()
            ->addMinutes(max(1, (int) $kuis->waktu_menit));

        if (now()->greaterThanOrEqualTo($batasWaktu)) {
            $this->selesaikanPercobaan($percobaanKuis);

            return back()->withErrors([
                'jawaban' => 'Waktu kuis telah habis.',
            ]);
        }

        $soal = $kuis->soal()
            ->whereKey($data['soal_id'])
            ->firstOrFail();

        $opsiId = $data['opsi_jawaban_id'] ?? null;
        $jawabanText = $data['jawaban_text'] ?? null;

        if ($soal->tipe === 'esai') {
            if ($opsiId !== null) {
                throw ValidationException::withMessages([
                    'opsi_jawaban_id' =>
                        'Soal esai harus dijawab menggunakan teks.',
                ]);
            }

            if (trim((string) $jawabanText) === '') {
                $jawabanText = null;
            }
        } else {
            if ($opsiId === null) {
                throw ValidationException::withMessages([
                    'opsi_jawaban_id' =>
                        'Pilih salah satu jawaban.',
                ]);
            }

            $opsiValid = $soal->opsiJawaban()
                ->whereKey($opsiId)
                ->exists();

            if (! $opsiValid) {
                throw ValidationException::withMessages([
                    'opsi_jawaban_id' =>
                        'Pilihan jawaban tidak sesuai dengan soal.',
                ]);
            }

            $jawabanText = null;
        }

        JawabanKuis::updateOrCreate(
            [
                'percobaan_kuis_id' => $percobaanKuis->id,
                'soal_id' => $soal->id,
            ],
            [
                'opsi_jawaban_id' => $opsiId,
                'jawaban_text' => $jawabanText,
                'benar' => null,
                'nilai' => 0,
                'answered_at' => (
                    $opsiId !== null || $jawabanText !== null
                ) ? now() : null,
            ]
        );

        return back()->with(
            'success',
            'Jawaban berhasil disimpan.'
        );
    }

    /**
     * Mengakhiri percobaan kuis.
     */
    public function submit(PercobaanKuis $percobaanKuis)
    {
        $this->pastikanPemilikPercobaan($percobaanKuis);

        $percobaanKuis->load('kuis');

        abort_unless($percobaanKuis->kuis, 404);

        $this->pastikanTerdaftar($percobaanKuis->kuis);

        $this->selesaikanPercobaan($percobaanKuis);

        return redirect()->route('peserta.kuis.result', [
            'percobaanKuis' => $percobaanKuis->id,
        ]);
    }

    /**
     * Menghitung nilai berdasarkan seluruh soal dalam percobaan.
     * Nilai akhir menunggu koreksi jika ada esai belum dinilai.
     */
    private function selesaikanPercobaan(
        PercobaanKuis $percobaanKuis
    ): void {
        DB::transaction(function () use ($percobaanKuis) {
            $percobaan = PercobaanKuis::query()
                ->whereKey($percobaanKuis->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($percobaan->status === 'selesai') {
                return;
            }

            $kuis = Kuis::query()
                ->findOrFail($percobaan->kuis_id);

            $soalIds = array_map(
                'intval',
                $percobaan->soal_ids ?? []
            );

            $soalList = $kuis->soal()
                ->whereIn('id', $soalIds)
                ->with('opsiJawaban')
                ->get();

            $totalBobot = $soalList->sum(
                fn ($soal) => max(0, (float) $soal->bobot)
            );

            $jawaban = JawabanKuis::query()
                ->where(
                    'percobaan_kuis_id',
                    $percobaan->id
                )
                ->whereIn('soal_id', $soalIds)
                ->get()
                ->keyBy('soal_id');

            $jumlahBenar = 0;
            $jumlahDijawab = 0;
            $nilaiDiperoleh = 0;
            $adaEsaiBelumDikoreksi = false;

            foreach ($soalList as $soal) {
                $record = $jawaban->get($soal->id);
                $bobot = max(0, (float) $soal->bobot);

                if ($soal->tipe === 'esai') {
                    if (! $record) {
                        $record = JawabanKuis::create([
                            'percobaan_kuis_id' => $percobaan->id,
                            'soal_id' => $soal->id,
                            'jawaban_text' => null,
                            'opsi_jawaban_id' => null,
                            'benar' => null,
                            'nilai' => 0,
                            'catatan_koreksi' => null,
                            'dikoreksi_at' => null,
                            'answered_at' => null,
                        ]);
                    }

                    if ($record->answered_at) {
                        $jumlahDijawab++;
                    }

                    if (! $record->dikoreksi_at) {
                        $adaEsaiBelumDikoreksi = true;
                    } else {
                        $nilaiDiperoleh += min(
                            $bobot,
                            max(0, (float) $record->nilai)
                        );
                    }

                    continue;
                }

                if (! $record || ! $record->opsi_jawaban_id) {
                    if ($record) {
                        $record->update([
                            'benar' => false,
                            'nilai' => 0,
                        ]);
                    }

                    continue;
                }

                $opsi = $soal->opsiJawaban->firstWhere(
                    'id',
                    (int) $record->opsi_jawaban_id
                );

                $benar = $opsi !== null
                    && (bool) $opsi->is_correct;

                $nilaiSoal = $benar ? $bobot : 0;

                $record->update([
                    'benar' => $benar,
                    'nilai' => $nilaiSoal,
                ]);

                $jumlahDijawab++;

                if ($benar) {
                    $jumlahBenar++;
                }

                $nilaiDiperoleh += $nilaiSoal;
            }

            $nilaiAkhir = $adaEsaiBelumDikoreksi
                ? null
                : (
                    $totalBobot > 0
                        ? round(
                            ($nilaiDiperoleh / $totalBobot) * 100,
                            2
                        )
                        : 0
                );

            $percobaan->update([
                'selesai_pada' => now(),
                'jumlah_benar' => $jumlahBenar,
                'jumlah_dijawab' => $jumlahDijawab,
                'nilai' => $nilaiAkhir,
                'status' => 'selesai',
                'lulus' => $nilaiAkhir === null
                    ? null
                    : $nilaiAkhir >= (float) $kuis->nilai_lulus,
            ]);
        });
    }

    /**
     * Menampilkan hasil percobaan.
     */
    public function result(
        PercobaanKuis $percobaanKuis
    ): Response {
        $this->pastikanPemilikPercobaan($percobaanKuis);

        abort_unless(
            $percobaanKuis->status === 'selesai',
            403,
            'Kuis belum diselesaikan.'
        );

        $percobaanKuis->load([
            'kuis.programPelatihan.pelatihan',
            'kuis.programPelatihan.tahap',
            'kuis.kelas',
        ]);

        return Inertia::render('Peserta/Kuis/Result', [
            'percobaanKuis' => $percobaanKuis,
        ]);
    }
}
