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
        Schema::create('materis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihans')
                ->cascadeOnDelete();

            $table->foreignId('instruktur_id')
                ->constrained('instrukturs')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->longText('isi')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('urutan')->default(1);

            $table->enum('status', ['draft', 'publish'])
                ->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
