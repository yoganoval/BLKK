<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihan')
                ->restrictOnDelete();

            // $table->foreignId('kelas_id')
            //     ->constrained('kelas')
            //     ->restrictOnDelete();

            $table->unsignedInteger('pertemuan');

            $table->date('tanggal');

            $table->time('jam_mulai');

            $table->time('jam_selesai');

            $table->string('kegiatan')->nullable();

            $table->text('keterangan')->nullable();

            $table->enum('status', [
                'belum_dimulai',
                'berlangsung',
                'selesai',
                'dibatalkan',
            ])->default('belum_dimulai');

            $table->timestamps();

            $table->unique([
                'program_pelatihan_id',
                // 'kelas_id',
                'pertemuan',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};