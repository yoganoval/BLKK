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

            $table->foreignId('kuis_id')
                ->constrained('kuis')
                ->cascadeOnDelete();

            $table->foreignId('soal_id')
                ->constrained('soal')
                ->cascadeOnDelete();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->foreignId('opsi_jawaban_id')
                ->nullable()
                ->constrained('opsi_jawaban')
                ->nullOnDelete();

            $table->boolean('benar')
                ->nullable();

            $table->decimal('nilai', 5, 2)
                ->default(0);

            $table->timestamp('answered_at')->nullable();

            $table->timestamps();

            $table->unique([
                'kuis_id',
                'soal_id',
                'peserta_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_kuis');
    }
};