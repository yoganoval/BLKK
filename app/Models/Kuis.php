<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuis extends Model
{
    protected $table = 'kuis';

    protected $fillable = [
        'pelatihan_id',
        'kelas_id',
        'tahap_id',
        'materi_id',
        'judul',
        'deskripsi',
        'waktu_menit',
        'nilai_lulus',
        'percobaan_maksimal',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'waktu_menit' => 'integer',
            'nilai_lulus' => 'decimal:2',
            'percobaan_maksimal' => 'integer',
        ];
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahap(): BelongsTo
    {
        return $this->belongsTo(Tahap::class);
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pertanyaan(): HasMany
    {
        return $this->hasMany(PertanyaanKuis::class, 'kuis_id');
    }
}
