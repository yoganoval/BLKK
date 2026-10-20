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

            $table->foreignId('kuis_id')
                ->constrained('kuis')
                ->cascadeOnDelete();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->unsignedInteger('percobaan_ke')
                ->default(1);

            $table->timestamp('mulai_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable();

            $table->decimal('nilai', 5, 2)->nullable();

            $table->unsignedInteger('jumlah_benar')
                ->default(0);

            $table->unsignedInteger('jumlah_dijawab')
                ->default(0);

            $table->enum('status', [
                'berlangsung',
                'selesai',
                'dibatalkan',
            ])->default('berlangsung');

            $table->boolean('lulus')->nullable();

            $table->timestamps();

            $table->unique(
                ['kuis_id', 'peserta_id', 'percobaan_ke'],
                'percobaan_kuis_peserta_nomor_unique'
            );

            $table->index([
                'peserta_id',
                'kuis_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('percobaan_kuis');
    }
};