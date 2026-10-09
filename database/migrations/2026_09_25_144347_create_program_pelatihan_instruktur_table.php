<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_pelatihan_instruktur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihans')
                ->cascadeOnDelete();

            $table->foreignId('instruktur_id')
                ->constrained('instrukturs')
                ->cascadeOnDelete();

            $table->enum('peran', ['utama', 'asisten'])->nullable();
            $table->timestamps();

            $table->unique([
                'program_pelatihan_id',
                'instruktur_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_pelatihan_instruktur');
    }
};
