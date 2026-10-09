<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('face_embedding')
                ->nullable()
                ->after('password');

            $table->string('face_photo_path')
                ->nullable()
                ->after('face_embedding');

            $table->timestamp('face_registered_at')
                ->nullable()
                ->after('face_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'face_embedding',
                'face_photo_path',
                'face_registered_at',
            ]);
        });
    }
};