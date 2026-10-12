<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = [
        'kode',
        'barcode',
        'nama',
        'kategori_id',
        'jumlah',
        'satuan',
        'kondisi',
        'lokasi',
        'tahun_perolehan',
        'harga_perolehan',
        'sumber_dana',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tahun_perolehan' => 'integer',
            'harga_perolehan' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            KategoriInventaris::class,
            'kategori_id'
        );
    }


    public function files(): HasMany
    {
        return $this->hasMany(
            InventarisFile::class,
            'inventaris_id'
        );
    }


    public function riwayat(): HasMany
    {
        return $this->hasMany(
            InventarisRiwayat::class,
            'inventaris_id'
        )->latest('tanggal');
    }

}
