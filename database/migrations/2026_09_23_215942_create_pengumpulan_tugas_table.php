<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tugas_id')
                ->constrained('tugas')
                ->cascadeOnDelete();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->longText('jawaban')->nullable();

            $table->string('file_path')->nullable();

            $table->timestamp('dikumpulkan_at')->nullable();

            $table->decimal('nilai', 5, 2)->nullable();

            $table->text('feedback')->nullable();

            $table->enum('status', [
                'belum_dikumpulkan',
                'dikumpulkan',
                'dinilai',
                'terlambat'
            ])->default('belum_dikumpulkan');

            $table->foreignId('dinilai_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('dinilai_at')->nullable();

            $table->timestamps();

            $table->unique(['tugas_id', 'peserta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};