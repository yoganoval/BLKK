<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();
            $table->string('barcode')->unique();
            $table->string('nama');

            $table->foreignId('kategori_id')
                ->constrained('kategori_inventaris')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('jumlah')->default(1);
            $table->string('satuan')->default('Unit');

            $table->enum('kondisi', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
            ])->default('baik');

            $table->string('lokasi')->nullable();

            $table->year('tahun_perolehan')->nullable();

            $table->decimal('harga_perolehan', 15, 2)->nullable();

            $table->string('sumber_dana')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};