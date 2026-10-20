<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'kuis_id',
        'pertanyaan',
        'tipe',
        'bobot',
        // 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
            // 'urutan' => 'integer',
        ];
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'kuis_id');
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class, 'soal_id');
            // ->orderBy('urutan');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasMany(JawabanKuis::class, 'soal_id');
    }
}
