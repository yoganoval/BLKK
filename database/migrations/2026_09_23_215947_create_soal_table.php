<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kuis_id')
                ->constrained('kuis')
                ->cascadeOnDelete();

            $table->longText('pertanyaan');

            $table->enum('tipe', [
                'pilihan_ganda',
                'benar_salah',
                'esai',
            ])->default('pilihan_ganda');

            $table->decimal('bobot', 5, 2)
                ->default(1);

            $table->unsignedInteger('urutan')
                ->default(1);

            $table->timestamps();

            $table->index(['kuis_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};