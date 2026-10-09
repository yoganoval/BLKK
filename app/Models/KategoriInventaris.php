<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriInventaris extends Model
{
    use HasFactory;

    protected $table = 'kategori_inventaris';

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    public function inventaris(): HasMany
    {
        return $this->hasMany(Inventaris::class, 'kategori_id');
    }
}