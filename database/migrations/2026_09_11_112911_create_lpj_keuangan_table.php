<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lpj_keuangan', function (Blueprint $table) {
            $table->id();

            $table->string('kode_lpj')->unique();

            $table->date('periode_mulai');
            $table->date('periode_selesai');

            $table->enum('status', [
                'draft',
                'diajukan',
                'disetujui',
                'ditolak',
                'selesai',
            ])->default('draft');

            $table->text('keterangan')->nullable();

            // User yang membuat LPJ
            $table->foreignId('dibuat_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // User yang menyetujui LPJ
            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_pengesahan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpj_keuangan');
    }
};