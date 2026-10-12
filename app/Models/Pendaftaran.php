<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $fillable = [
        'peserta_id',
        'program_pelatihan_id',
        'kelas_id',
        'tanggal_daftar',
        'status',
    ];

    public function programPelatihan(): BelongsTo
    {
        return $this->belongsTo(
            ProgramPelatihan::class,
            'program_pelatihan_id'
        );
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(
            Peserta::class,
            'peserta_id'
        );
    }
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(
            Kelas::class,
            'kelas_id'
        );
    }
    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(
            Pelatihan::class,
            'pelatihan_id'
        );
    }
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pendaftaran_id', 'id');
    }
}