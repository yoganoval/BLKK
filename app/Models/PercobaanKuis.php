<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PercobaanKuis extends Model
{
    protected $table = 'percobaan_kuis';

    protected $fillable = [
        'kuis_id',
        'peserta_id',
        'percobaan_ke',
        'mulai_pada',
        'selesai_pada',
        'nilai',
        'jumlah_benar',
        'jumlah_dijawab',
        'status',
        'lulus',
    ];

    protected function casts(): array
    {
        return [
            'percobaan_ke' => 'integer',
            'mulai_pada' => 'datetime',
            'selesai_pada' => 'datetime',
            'nilai' => 'decimal:2',
            'jumlah_benar' => 'integer',
            'jumlah_dijawab' => 'integer',
            'lulus' => 'boolean',
        ];
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'kuis_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasMany(
            JawabanKuis::class,
            'percobaan_kuis_id'
        );
    }
}
