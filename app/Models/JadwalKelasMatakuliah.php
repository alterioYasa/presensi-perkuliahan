<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKelasMatakuliah extends Model
{
    protected $table = 'jadwal_kelas_matakuliah';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_mk',
        'nik',
        'semester',
        'tanggal',
        'jam_mulai'
    ];

    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class, 'kode_mk', 'kode_mk');
    }
}
