<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelasMatakuliah extends Model
{
    protected $connection = "mysql";
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

    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class, 'kode_mk', 'kode_mk');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKelasMatakuliah::class, 'kode_mk', 'kode_mk');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nik', 'nik');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'kode_mk', 'kode_mk')
            ->whereColumn('semester', 'semester')
            ->whereColumn('nik', 'nik');
    }
}
