<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresPeserta extends Model
{
    protected $table = 'progres_peserta';

    protected $fillable = [
        'peserta_id',
        'materi_id',
        'persentase',
        'status',
        'mulai_at',
        'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'persentase' => 'decimal:2',
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}