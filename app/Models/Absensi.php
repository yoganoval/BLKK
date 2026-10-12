<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'jadwal_id',
        'pendaftaran_id',
        'waktu_masuk',
        'waktu_keluar',
        'status',
        'metode',
        'face_score',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'waktu_keluar' => 'datetime',
            'face_score' => 'decimal:2',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(
            Jadwal::class,
            'jadwal_id'
        );
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(
            Pendaftaran::class,
            'pendaftaran_id'
        );
    }
}
