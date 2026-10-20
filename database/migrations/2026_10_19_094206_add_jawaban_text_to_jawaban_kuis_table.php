<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jawaban_kuis', function (Blueprint $table) {
            $table->longText('jawaban_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jawaban_kuis', function (Blueprint $table) {
            $table->dropColumn('jawaban_text');
        });
    }
};
