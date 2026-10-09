<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahap', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->unsignedInteger('periode');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // $table->unique('urutan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahap');
    }
};