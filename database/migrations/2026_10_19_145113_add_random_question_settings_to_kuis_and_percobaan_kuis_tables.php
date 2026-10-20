
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_soal')
                ->default(5)
                ->after('percobaan_maksimal');
        });

        Schema::table('percobaan_kuis', function (Blueprint $table) {
            $table->json('soal_ids')->nullable();
            $table->json('urutan_opsi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('percobaan_kuis', function (Blueprint $table) {
            $table->dropColumn([
                'soal_ids',
                'urutan_opsi',
            ]);
        });

        Schema::table('kuis', function (Blueprint $table) {
            $table->dropColumn('jumlah_soal');
        });
    }
};
