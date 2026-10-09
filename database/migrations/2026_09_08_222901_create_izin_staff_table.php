<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_staff', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->enum('jenis', [
                'izin',
                'sakit',
                'cuti',
                'dinas',
            ]);

            $table->text('alasan');

            $table->string('bukti')->nullable();

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
            ])->default('menunggu');

            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('disetujui_pada')
                ->nullable();

            $table->text('catatan_admin')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'tanggal_mulai',
                'tanggal_selesai',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_staff');
    }
};