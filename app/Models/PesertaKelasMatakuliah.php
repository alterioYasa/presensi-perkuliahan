<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaKelasMatakuliah extends Model
{
    protected $table = 'peserta_kelas_matakuliah';
    public $timestamps = false;

    protected $fillable = [
        'kode_mk',
        'nik',
        'semester',
        'nim'
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
