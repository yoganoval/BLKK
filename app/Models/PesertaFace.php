<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaFace extends Model
{
    use HasFactory;

    protected $table = 'peserta_faces';

    protected $fillable = [
        'peserta_id',
        'face_embedding',
        'photo_path',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(
            Peserta::class,
            'peserta_id'
        );
    }
}
