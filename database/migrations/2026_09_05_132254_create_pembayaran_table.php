<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftaran_id')
                ->constrained('pendaftaran')
                ->cascadeOnDelete();

            $table->decimal('jumlah', 15, 2);

            $table->date('tanggal_bayar')->nullable();

            $table->string('metode')->nullable();

            $table->string('status')->default('pending');

            $table->string('bukti')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};