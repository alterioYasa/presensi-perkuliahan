<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    protected $connection = 'client';
    protected $table = 'realisasi';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'kode_mk',
        'nik',
        'semester',
        'pertemuan',
        'realisasi_perkuliahan',
        'alasan_revisi'
    ];
}
