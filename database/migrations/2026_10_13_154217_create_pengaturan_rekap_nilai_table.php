
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_rekap_nilai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->unique()
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->enum('metode_rekap', ['rata_rata'])
                ->default('rata_rata');

            $table->decimal('bobot_tugas', 5, 2)->default(60);
            $table->decimal('bobot_kuis', 5, 2)->default(40);

            $table->boolean('otomatis')->default(true);
            $table->boolean('hitung_belum_dinilai')->default(false);
            $table->boolean('esai_harus_dinilai')->default(true);

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_rekap_nilai');
    }
};
