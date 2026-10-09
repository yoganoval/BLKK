<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris_riwayat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventaris_id')
                ->constrained('inventaris')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->enum('kondisi', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
            ]);

            $table->text('keterangan')->nullable();

            $table->decimal('biaya_perbaikan', 15, 2)->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris_riwayat');
    }
};