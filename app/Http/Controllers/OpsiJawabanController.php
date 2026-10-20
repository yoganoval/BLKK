<?php

namespace App\Http\Controllers;

use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OpsiJawabanController extends Controller
{

    /**
     * Menampilkan semua opsi jawaban.
     */
    public function globalIndex(): \Inertia\Response
    {
        $opsiJawaban = \App\Models\OpsiJawaban::query()
            ->with('soal')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return \Inertia\Inertia::render('OpsiJawaban/Index', [
            'opsiJawaban' => $opsiJawaban,
        ]);
    }
    public function index(Soal $soal): Response
    {
        $soal->load([
            'kuis',
            'opsiJawaban',
        ]);

        return Inertia::render('Kuis/OpsiJawaban/Index', [
            'soal' => $soal,
            'opsiJawaban' => $soal->opsiJawaban,
        ]);
    }

    public function create(Soal $soal): Response
    {
        return Inertia::render('Kuis/OpsiJawaban/Create', [
            'soal' => $soal->load('kuis'),
        ]);
    }

    public function store(
        Request $request,
        Soal $soal
    ): RedirectResponse {
        $validated = $request->validate([
            'opsi' => ['required', 'string', 'max:5'],
            'teks' => ['required', 'string'],
            'is_correct' => ['required', 'boolean'],
            // 'urutan' => ['required', 'integer', 'min:1'],
        ]);

        $this->ensureOpsiBelumDipakaiUntukJawabanBenar(
            $soal,
            (bool) $validated['is_correct']
        );

        DB::transaction(function () use ($soal, $validated) {
            if ($validated['is_correct']) {
                $soal->opsiJawaban()->update([
                    'is_correct' => false,
                ]);
            }

            $soal->opsiJawaban()->create($validated);
        });

        return to_route('soal.opsi-jawaban.index', $soal)
            ->with('success', 'Opsi jawaban berhasil ditambahkan.');
    }

    public function show(
        Soal $soal,
        OpsiJawaban $opsiJawaban
    ): Response {
        $this->ensureOpsiMilikSoal($soal, $opsiJawaban);

        return Inertia::render('Kuis/OpsiJawaban/Show', [
            'soal' => $soal->load('kuis'),
            'opsiJawaban' => $opsiJawaban,
        ]);
    }

    public function edit(
        Soal $soal,
        OpsiJawaban $opsiJawaban
    ): Response {
        $this->ensureOpsiMilikSoal($soal, $opsiJawaban);

        return Inertia::render('Kuis/OpsiJawaban/Edit', [
            'soal' => $soal->load('kuis'),
            'opsiJawaban' => $opsiJawaban,
        ]);
    }

    public function update(
        Request $request,
        Soal $soal,
        OpsiJawaban $opsiJawaban
    ): RedirectResponse {
        $this->ensureOpsiMilikSoal($soal, $opsiJawaban);

        $validated = $request->validate([
            'opsi' => ['required', 'string', 'max:5'],
            'teks' => ['required', 'string'],
            'is_correct' => ['required', 'boolean'],
            // 'urutan' => ['required', 'integer', 'min:1'],
        ]);

        $this->ensureOpsiBelumDipakaiUntukJawabanBenar(
            $soal,
            (bool) $validated['is_correct'],
            $opsiJawaban
        );

        DB::transaction(function () use (
            $soal,
            $opsiJawaban,
            $validated
        ) {
            if ($validated['is_correct']) {
                $soal->opsiJawaban()
                    ->where('id', '!=', $opsiJawaban->id)
                    ->update(['is_correct' => false]);
            }

            $opsiJawaban->update($validated);
        });

        return to_route('soal.opsi-jawaban.index', $soal)
            ->with('success', 'Opsi jawaban berhasil diperbarui.');
    }

    public function destroy(
        Soal $soal,
        OpsiJawaban $opsiJawaban
    ): RedirectResponse {
        $this->ensureOpsiMilikSoal($soal, $opsiJawaban);

        if ($opsiJawaban->jawabanKuis()->exists()) {
            return back()->with(
                'error',
                'Opsi tidak dapat dihapus karena sudah digunakan dalam jawaban peserta.'
            );
        }

        DB::transaction(function () use ($opsiJawaban) {
            $opsiJawaban->delete();
        });

        return to_route('soal.opsi-jawaban.index', $soal)
            ->with('success', 'Opsi jawaban berhasil dihapus.');
    }

    private function ensureOpsiMilikSoal(
        Soal $soal,
        OpsiJawaban $opsiJawaban
    ): void {
        abort_unless(
            (int) $opsiJawaban->soal_id === (int) $soal->id,
            404
        );
    }

    private function ensureOpsiBelumDipakaiUntukJawabanBenar(
        Soal $soal,
        bool $isCorrect,
        ?OpsiJawaban $opsiJawaban = null
    ): void {
        if (! $isCorrect || ! $opsiJawaban) {
            return;
        }

        // Opsi yang sudah pernah dipilih peserta tidak boleh
        // diubah menjadi salah jika sedang menjadi kunci jawaban.
        $pernahDipilih = $opsiJawaban->jawabanKuis()
            ->whereNotNull('answered_at')
            ->exists();

        if ($pernahDipilih && $opsiJawaban->is_correct) {
            throw ValidationException::withMessages([
                'is_correct' => 'Kunci jawaban yang sudah digunakan peserta tidak dapat diubah sembarangan.',
            ]);
        }
    }
}
