<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'pelatihan_id',
        'kelas_id',
        'tahap_id',
        'materi_id',
        'judul',
        'deskripsi',
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

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function tahap(): BelongsTo
    {
        return $this->belongsTo(Tahap::class, 'tahap_id');
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(
            PengumpulanTugas::class,
            'tugas_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}