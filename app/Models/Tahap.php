<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tahap extends Model
{
    use HasFactory;

    protected $table = 'tahap';

    protected $fillable = [
        'nama',
        'periode',
        'keterangan',
    ];

    public function programPelatihans(): HasMany
    {
        return $this->hasMany(
            ProgramPelatihan::class,
            'tahap_id',
            'id'
        );
    }

    public function tahap(): BelongsTo
    {
        return $this->belongsTo(Tahap::class, 'tahap_id');
    }

}
