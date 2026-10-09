<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lpj_pengeluaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lpj_keuangan_id')
                ->constrained('lpj_keuangan')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->string('kategori');

            $table->string('uraian');

            $table->string('nomor_bukti')->nullable();

            $table->decimal('jumlah', 15, 2);

            $table->enum('metode_pembayaran', [
                'cash',
                'transfer',
                'qris',
            ])->default('cash');

            $table->string('penerima')->nullable();

            // Foto/scan nota, kuitansi, dll.
            $table->string('bukti_path')->nullable();

            $table->string('bukti_nama')->nullable();

            $table->string('bukti_mime')->nullable();

            $table->unsignedBigInteger('bukti_ukuran')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpj_pengeluaran');
    }
};