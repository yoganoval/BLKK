<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_faces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peserta_id')
                ->unique()
                ->constrained('peserta')
                ->cascadeOnDelete();

            $table->longText('face_embedding')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamp('registered_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_faces');
    }
};
