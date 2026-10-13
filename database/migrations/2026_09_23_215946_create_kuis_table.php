<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuis', function (Blueprint $table) {
            $table->id();

             $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihan')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('materi_id')
                ->nullable()
                ->constrained('materi')
                ->nullOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();

            $table->unsignedInteger('waktu_menit')->nullable();

            $table->decimal('nilai_lulus', 5, 2)
                ->default(70);

            $table->unsignedInteger('percobaan_maksimal')
                ->default(1);

            $table->enum('status', [
                'draft',
                'published',
                'closed'
            ])->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('kelas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuis');
    }
};