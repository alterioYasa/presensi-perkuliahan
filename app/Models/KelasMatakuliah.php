<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasMatakuliah extends Model
{
    protected $table = 'kelas_matakuliah';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_mk',
        'nik',
        'semester',
        'nama_mk'
    ];
}
