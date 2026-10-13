<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KomponenNilai extends Model
{
    protected $table = 'komponen_nilai';

    protected $fillable = [
        'kelas_id',
        'nama',
        'bobot',
        'nilai_maksimal',
        'keterangan',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
            'nilai_maksimal' => 'decimal:2',
            'urutan' => 'integer',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function nilaiPeserta(): HasMany
    {
        return $this->hasMany(
            NilaiPeserta::class,
            'komponen_nilai_id'
        );
    }
}
