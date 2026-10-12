<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class Peserta extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $table = 'peserta';

    protected $guard_name = 'peserta';

    protected $fillable = [
        'nama',
        'alamat',
        'kamar',
        'lembaga',
        'username',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function programPelatihan(): BelongsTo
    {
        return $this->belongsTo(
            ProgramPelatihan::class,
            'program_pelatihan_id'
        );
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(
            Pendaftaran::class,
            'peserta_id',
            'id'
        );
    }

    public function pesertaFace(): HasOne
    {
        return $this->hasOne(
            PesertaFace::class,
            'peserta_id'
        );
    }
}