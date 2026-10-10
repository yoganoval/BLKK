<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramPelatihan extends Model
{
    use HasFactory;

    protected $table = 'program_pelatihan';

    protected $fillable = [
        'pelatihan_id',
        'tahap_id',
        'nama',
        'kuota',
        'status',
    ];

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(
            Pelatihan::class,
            'pelatihan_id'
        );
    }

    public function tahap(): BelongsTo
    {
        return $this->belongsTo(
            Tahap::class,
            'tahap_id'
        );
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(
            Peserta::class,
            'program_pelatihan_id'
        );
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(
            Pendaftaran::class,
            'program_pelatihan_id',
            'id'
        );
    }
}
