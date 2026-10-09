<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_staff', function (Blueprint $table) {
            $table->id();

            // Staff yang melakukan absensi
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Lokasi resmi absensi
            $table->foreignId('lokasi_absensi_id')
                ->nullable()
                ->constrained('lokasi_absensi')
                ->nullOnDelete();

            $table->date('tanggal');

            // ABSEN MASUK
            $table->time('waktu_masuk')
                ->nullable();

            $table->decimal('latitude_masuk', 10, 7)
                ->nullable();

            $table->decimal('longitude_masuk', 10, 7)
                ->nullable();

            $table->decimal('jarak_masuk', 8, 2)
                ->nullable();

            $table->decimal('face_score_masuk', 5, 2)
                ->nullable();

            // ABSEN KELUAR
            $table->time('waktu_keluar')
                ->nullable();

            $table->decimal('latitude_keluar', 10, 7)
                ->nullable();

            $table->decimal('longitude_keluar', 10, 7)
                ->nullable();

            $table->decimal('jarak_keluar', 8, 2)
                ->nullable();

            $table->decimal('face_score_keluar', 5, 2)
                ->nullable();

            $table->enum('metode', [
                'face',
                'manual',
            ])->default('face');

            $table->enum('status', [
                'hadir',
                'terlambat',
                'izin',
                'sakit',
                'dinas',
                'alpha',
            ])->default('hadir');

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            // Satu staff hanya mempunyai satu record absensi
            // untuk satu tanggal.
            $table->unique([
                'user_id',
                'tanggal',
            ]);

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_staff');
    }
};