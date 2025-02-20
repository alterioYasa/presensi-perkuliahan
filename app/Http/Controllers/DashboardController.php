<?php

namespace App\Http\Controllers;

use App\Models\JadwalKelasMatakuliah;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $dosen = session('dosen');

        $relations = [
            'matakuliah'
        ];

        $jadwal = JadwalKelasMatakuliah::where('nik', $dosen->nik)->with($relations)->get();

        return view('dashboard', compact('dosen', 'jadwal'));
    }
}
