<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_nilai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->string('nama');

            $table->decimal('bobot', 5, 2)
                ->default(0);

            $table->decimal('nilai_maksimal', 5, 2)
                ->default(100);

            $table->text('keterangan')->nullable();

            $table->unsignedInteger('urutan')->default(1);

            $table->timestamps();

            $table->index('kelas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komponen_nilai');
    }
};