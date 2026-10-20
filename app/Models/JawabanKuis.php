<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanKuis extends Model
{
    protected $table = 'jawaban_kuis';

    protected $fillable = [
        'percobaan_kuis_id',
        'soal_id',
        'opsi_jawaban_id',
        'jawaban_text',
        'benar',
        'nilai',
        'answered_at',
        'catatan_koreksi',
        'dikoreksi_at',
    ];

    protected function casts(): array
    {
        return [
            'benar' => 'boolean',
            'nilai' => 'decimal:2',
            'answered_at' => 'datetime',
        ];
    }

    public function percobaanKuis(): BelongsTo
    {
        return $this->belongsTo(
            PercobaanKuis::class,
            'percobaan_kuis_id'
        );
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function opsiJawaban(): BelongsTo
    {
        return $this->belongsTo(
            OpsiJawaban::class,
            'opsi_jawaban_id'
        );
    }
}
