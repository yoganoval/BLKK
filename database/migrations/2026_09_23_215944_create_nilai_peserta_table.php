<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_peserta', function (Blueprint $table) {
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

            $table->foreignId('komponen_nilai_id')
                ->constrained('komponen_nilai')
                ->cascadeOnDelete();

            $table->decimal('nilai', 5, 2)
                ->default(0);

            $table->text('catatan')->nullable();

            $table->foreignId('dinilai_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'peserta_id',
                'komponen_nilai_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_peserta');
    }
};