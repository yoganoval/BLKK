<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'peserta_id',
        'jawaban',
        'file_path',
        'dikumpulkan_at',
        'nilai',
        'feedback',
        'status',
        'dinilai_oleh',
        'dinilai_at',
    ];

    protected function casts(): array
    {
        return [
            'dikumpulkan_at' => 'datetime',
            'dinilai_at' => 'datetime',
            'nilai' => 'decimal:2',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(
            Tugas::class,
            'tugas_id'
        );
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(
            Peserta::class,
            'peserta_id'
        );
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'dinilai_oleh'
        );
    }
}
