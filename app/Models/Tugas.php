<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'program_pelatihan_id',
        'kelas_id',
        'judul',
        'instruksi',
        'deadline',
        'nilai_maksimal',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'nilai_maksimal' => 'decimal:2',
        ];
    }

    public function programPelatihan(): BelongsTo
    {
        return $this->belongsTo(
            ProgramPelatihan::class,
            'program_pelatihan_id'
        );
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(
            Kelas::class,
            'kelas_id'
        );
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(
            PengumpulanTugas::class,
            'tugas_id'
        );
    }
}
