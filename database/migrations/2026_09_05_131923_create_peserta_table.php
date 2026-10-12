<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();

            // Identitas peserta
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('kamar')->nullable();
            $table->string('lembaga')->nullable();

            // Akun login peserta
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();

            // Status akun
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};