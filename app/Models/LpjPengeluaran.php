<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pelatihan_id')
                ->constrained('pelatihan')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('tahap_id')
                ->nullable()
                ->constrained('tahap')
                ->nullOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->longText('isi_materi')->nullable();

            $table->unsignedInteger('urutan')->default(1);

            $table->enum('status', [
                'draft',
                'published',
            ])->default('draft');

            $table->timestamps();

            $table->index(
                ['pelatihan_id', 'kelas_id', 'tahap_id'],
                'materi_program_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
