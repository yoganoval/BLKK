<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_peserta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->foreignId('materi_id')
                ->constrained('materi')
                ->cascadeOnDelete();

            $table->decimal('persentase', 5, 2)
                ->default(0);

            $table->enum('status', [
                'belum_mulai',
                'sedang_dipelajari',
                'selesai'
            ])->default('belum_mulai');

            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();

            $table->timestamps();

            $table->unique([
                'peserta_id',
                'materi_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_peserta');
    }
};