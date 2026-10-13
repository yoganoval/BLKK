<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();

             $table->foreignId('program_pelatihan_id')
                ->constrained('program_pelatihan')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->string('judul');
            // $table->text('deskripsi')->nullable();
            $table->longText('instruksi')->nullable();

            $table->timestamp('deadline')->nullable();

            $table->decimal('nilai_maksimal', 5, 2)
                ->default(100);

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

            $table->index(['kelas_id', 'deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};