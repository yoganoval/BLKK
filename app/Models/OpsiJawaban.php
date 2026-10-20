<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpsiJawaban extends Model
{
    protected $table = 'opsi_jawaban';

    protected $fillable = [
        'soal_id',
        'opsi',
        'teks',
        'is_correct',
        // 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            // 'urutan' => 'integer',
        ];
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasMany(
            JawabanKuis::class,
            'opsi_jawaban_id'
        );
    }
}
