<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'durasi_hari',
        'keterangan',
    ];

    public function peserta(): HasMany
    {
        return $this->hasMany(
            Peserta::class,
            'kelas_id',
            'id'
        );
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(
            Pendaftaran::class,
            'kelas_id',
            'id'
        );
    }

    public function jadwals(): BelongsToMany
    {
        return $this->belongsToMany(
            Jadwal::class,
            'jadwal_kelas',
            'kelas_id',
            'jadwal_id'
        )->withTimestamps();
    }
}