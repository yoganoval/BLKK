<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opsi_jawaban', function (Blueprint $table) {
            $table->id();

            $table->foreignId('soal_id')
                ->constrained('soal')
                ->cascadeOnDelete();

            $table->string('opsi', 5);
            $table->longText('teks');

            $table->boolean('is_correct')
                ->default(false);

            $table->unsignedInteger('urutan')
                ->default(1);

            $table->timestamps();

            $table->unique(
                ['soal_id', 'opsi'],
                'opsi_jawaban_soal_opsi_unique'
            );

            $table->unique(
                ['soal_id', 'urutan'],
                'opsi_jawaban_soal_urutan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opsi_jawaban');
    }
};
