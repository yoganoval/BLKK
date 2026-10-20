<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuis extends Model
{
    protected $table = 'kuis';

    protected $fillable = [
        'program_pelatihan_id',
        'kelas_id',
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

    public function programPelatihan(): BelongsTo
    {
        return $this->belongsTo(
            ProgramPelatihan::class,
            'program_pelatihan_id'
        );
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'kuis_id')
            ->orderBy('urutan');
    }

    public function percobaanKuis(): HasMany
    {
        return $this->hasMany(PercobaanKuis::class, 'kuis_id');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasManyThrough(
            JawabanKuis::class,
            PercobaanKuis::class,
            'kuis_id',
            'percobaan_kuis_id'
        );
    }
}
