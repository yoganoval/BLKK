<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalStaff extends Model
{
    use HasFactory;

    protected $table = 'jadwal_staff';

    protected $fillable = [
        'nama',
        'hari',
        'jam_masuk',
        'jam_pulang',
        'toleransi_menit',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'toleransi_menit' => 'integer',
        'status' => 'boolean',
    ];
}