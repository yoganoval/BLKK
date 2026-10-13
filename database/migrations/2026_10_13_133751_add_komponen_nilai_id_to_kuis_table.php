<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            $table->foreignId('komponen_nilai_id')
                ->nullable()
                ->constrained('komponen_nilai')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('komponen_nilai_id');
        });
    }
};