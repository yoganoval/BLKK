<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('percobaan_kuis', function (Blueprint $table) {
            $table->id();

            // Kuis yang dikerjakan
            $table->foreignId('kuis_id')
                ->constrained('kuis')
                ->cascadeOnDelete();

            // Peserta yang mengerjakan
            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            // Nomor percobaan: 1, 2, 3, dan seterusnya
            $table->unsignedInteger('percobaan_ke')->default(1);

            // Waktu pengerjaan
            $table->timestamp('mulai_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable();

            // Nilai hasil koreksi kuis
            $table->decimal('nilai', 5, 2)->nullable();

            // Jumlah jawaban yang benar
            $table->unsignedInteger('jumlah_benar')->default(0);

            // Jumlah soal yang dikerjakan
            $table->unsignedInteger('jumlah_dijawab')->default(0);

            // Status pengerjaan
            $table->enum('status', [
                'berlangsung',
                'selesai',
                'dibatalkan',
            ])->default('berlangsung');

            // Apakah peserta lulus berdasarkan nilai_lulus pada tabel kuis
            $table->boolean('lulus')->nullable();

            $table->timestamps();

            // Nomor percobaan tidak boleh ganda untuk kuis dan peserta yang sama
            $table->unique(
                ['kuis_id', 'peserta_id', 'percobaan_ke'],
                'percobaan_kuis_peserta_nomor_unique'
            );

            $table->index(['peserta_id', 'kuis_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('percobaan_kuis');
    }
};
