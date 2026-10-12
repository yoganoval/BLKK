<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'program_pelatihan_id',
        'pertemuan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'kegiatan',
        'keterangan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'pertemuan' => 'integer',
        ];
    }

    public function programPelatihan(): BelongsTo
    {
        return $this->belongsTo(
            ProgramPelatihan::class,
            'program_pelatihan_id'
        );
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(
            Absensi::class,
            'jadwal_id'
        );
    }
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(
            Kelas::class,
            'jadwal_kelas',
            'jadwal_id',
            'kelas_id'
        )->withTimestamps();
    }
}
