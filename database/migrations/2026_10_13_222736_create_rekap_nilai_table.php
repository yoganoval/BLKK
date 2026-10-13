<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_nilai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihan')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            // Nilai akhir berdasarkan seluruh komponen berbobot
            $table->decimal('nilai_akhir', 5, 2)->nullable();

            // Predikat hasil akhir, misalnya A, B, C
            $table->string('predikat', 5)->nullable();

            // Status kelengkapan perhitungan rekap
            $table->enum('status_rekap', [
                'belum_lengkap',
                'selesai',
            ])->default('belum_lengkap');

            // Status kelulusan peserta
            $table->enum('status_kelulusan', [
                'belum_dinilai',
                'lulus',
                'tidak_lulus',
            ])->default('belum_dinilai');

            $table->timestamp('tanggal_rekap')->nullable();

            $table->timestamps();

            // Satu rekap untuk setiap peserta pada kelas dan program
            $table->unique([
                'peserta_id',
                'program_pelatihan_id',
                'kelas_id',
            ], 'rekap_nilai_peserta_program_kelas_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_nilai');
    }
};
