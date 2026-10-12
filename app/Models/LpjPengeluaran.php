<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpjPengeluaran extends Model
{
    protected $table = 'lpj_pengeluaran';

    protected $fillable = [
        'lpj_keuangan_id',
        'tanggal',
        'kategori',
        'uraian',
        'nomor_bukti',
        'jumlah',
        'metode_pembayaran',
        'penerima',
        'bukti_path',
        'bukti_nama',
        'bukti_mime',
        'bukti_ukuran',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
            'bukti_ukuran' => 'integer',
        ];
    }

    public function lpjKeuangan(): BelongsTo
    {
        return $this->belongsTo(
            LpjKeuangan::class,
            'lpj_keuangan_id'
        );
    }
}
