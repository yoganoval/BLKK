<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiPeserta extends Model
{
    protected $table = 'nilai_peserta';

    protected $fillable = [
        'peserta_id',
        'program_pelatihan_id',
        'kelas_id',
        'komponen_nilai_id',
        'nilai',
        'catatan',
        'dinilai_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
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

    public function komponenNilai(): BelongsTo
    {
        return $this->belongsTo(
            KomponenNilai::class,
            'komponen_nilai_id'
        );
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }
}
