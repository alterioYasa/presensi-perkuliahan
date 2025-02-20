<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    public $timestamps = false;

    protected $fillable = [
        'nim',
        'nama'
    ];
}
