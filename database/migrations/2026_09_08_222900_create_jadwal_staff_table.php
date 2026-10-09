<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_staff', function (Blueprint $table) {
            $table->id();

            $table->string('nama');

            $table->enum('hari', [
                'senin',
                'selasa',
                'rabu',
                'kamis',
                'jumat',
                'sabtu',
                'minggu',
            ]);

            $table->time('jam_masuk');
            $table->time('jam_pulang');

            // Toleransi keterlambatan dalam menit
            $table->unsignedInteger('toleransi_menit')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_staff');
    }
};