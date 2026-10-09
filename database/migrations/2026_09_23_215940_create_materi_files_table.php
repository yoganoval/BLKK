<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('materi_id')
                ->constrained('materi')
                ->cascadeOnDelete();

            $table->string('nama_file');
            $table->string('file_path');
            $table->string('tipe_file')->nullable();
            $table->unsignedBigInteger('ukuran')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_files');
    }
};