<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            // Akun login peserta
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();

            // Status akun
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->dropUnique(['username']);

            $table->dropColumn([
                'username',
                'password',
                'remember_token',
                'is_active',
            ]);
        });
    }
};