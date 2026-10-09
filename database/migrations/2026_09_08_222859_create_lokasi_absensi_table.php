<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_absensi', function (Blueprint $table) {
            $table->id();

            $table->string('nama');

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Radius dalam meter
            $table->unsignedInteger('radius')->default(100);

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_absensi');
    }
};