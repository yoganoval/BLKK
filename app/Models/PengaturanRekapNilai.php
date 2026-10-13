<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaturanRekapNilai extends Model
{
    protected $table = 'pengaturan_rekap_nilai';

    protected $fillable = [
        'kelas_id',
        'metode_rekap',
        'bobot_tugas',
        'bobot_kuis',
        'otomatis',
        'hitung_belum_dinilai',
        'esai_harus_dinilai',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'bobot_tugas' => 'decimal:2',
            'bobot_kuis' => 'decimal:2',
            'otomatis' => 'boolean',
            'hitung_belum_dinilai' => 'boolean',
            'esai_harus_dinilai' => 'boolean',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
