<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_materi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('materi_id')
                ->constrained('materi')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['materi_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_materi');
    }
};
