<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $connection = 'client';
    protected $table = 'presensi';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'kode_mk',
        'nik',
        'semester',
        'nim',
        'pertemuan',
        'status_presensi',
        'alasan_revisi',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
