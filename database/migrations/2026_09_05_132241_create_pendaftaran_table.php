<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peserta_id')
                ->constrained('peserta')
                ->restrictOnDelete();

            $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihan')
                ->restrictOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->restrictOnDelete();

            $table->date('tanggal_daftar');

            $table->string('status')->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};