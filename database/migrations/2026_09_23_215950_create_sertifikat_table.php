<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->foreignId('pelatihan_id')
                ->constrained('pelatihan')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->nullable()
                ->constrained('kelas')
                ->nullOnDelete();

            $table->string('nomor_sertifikat')
                ->unique();

            $table->date('tanggal_terbit');

            $table->decimal('nilai_akhir', 5, 2)
                ->nullable();

            $table->string('predikat')
                ->nullable();

            $table->string('file_path')
                ->nullable();

            $table->string('qr_code')
                ->nullable();

            $table->timestamps();

            $table->index([
                'peserta_id',
                'pelatihan_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};