<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $connection = 'client';
    protected $table = 'presensi';
    public $timestamps = false;

    protected $fillable = [
        'kode_mk',
        'nik',
        'semester',
        'nim',
        'pertemuan',
        'status_presensi'
    ];
}
