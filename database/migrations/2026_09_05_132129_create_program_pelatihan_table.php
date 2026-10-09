<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_pelatihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pelatihan_id')
                ->constrained('pelatihan')
                ->restrictOnDelete();

            $table->foreignId('tahap_id')
                ->constrained('tahap')
                ->restrictOnDelete();

            $table->string('nama');

            $table->unsignedInteger('kuota')->default(16);

            $table->string('status')->default('dibuka');

            $table->timestamps();

            $table->unique([
                'pelatihan_id',
                'tahap_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_pelatihan');
    }
};