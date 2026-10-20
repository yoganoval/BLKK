<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_kuis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('percobaan_kuis_id')
                ->constrained('percobaan_kuis')
                ->cascadeOnDelete();

            $table->foreignId('soal_id')
                ->constrained('soal')
                ->cascadeOnDelete();

            $table->foreignId('opsi_jawaban_id')
                ->nullable()
                ->constrained('opsi_jawaban')
                ->nullOnDelete();

            $table->boolean('benar')->nullable();

            $table->decimal('nilai', 7, 2)
                ->default(0);

            $table->timestamp('answered_at')->nullable();

            $table->timestamps();

            // Satu soal hanya memiliki satu jawaban
            // dalam satu percobaan tertentu.
            $table->unique(
                ['percobaan_kuis_id', 'soal_id'],
                'jawaban_kuis_percobaan_soal_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_kuis');
    }
};