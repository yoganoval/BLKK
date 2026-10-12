<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriFile extends Model
{
    use HasFactory;

    protected $table = 'materi_files';

    protected $fillable = [
        'materi_id',
        'nama_file',
        'file_path',
        'tipe_file',
        'ukuran',
    ];

    protected function casts(): array
    {
        return [
            'ukuran' => 'integer',
        ];
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}
