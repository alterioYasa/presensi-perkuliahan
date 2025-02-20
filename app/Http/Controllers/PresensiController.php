<?php

namespace App\Http\Controllers;

use App\Models\JadwalKelasMatakuliah;
use App\Models\PesertaKelasMatakuliah;
use App\Models\Presensi;
use App\Models\Realisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function inputPresensi($kode_mk, $semester)
    {
        $dosen = session('dosen');

        $jadwalMatakuliah = JadwalKelasMatakuliah::where([
            ['nik', $dosen->nik],
            ['kode_mk', $kode_mk],
            ['semester', $semester]
        ])->with('matakuliah')->first();

        $peserta = PesertaKelasMatakuliah::where('kode_mk', $kode_mk)
            ->where('nik', $dosen->nik)
            ->where('semester', $semester)
            ->with('mahasiswa')
            ->get();

        return view('presensi.input-presensi', compact('peserta', 'kode_mk', 'semester', 'dosen', 'jadwalMatakuliah'));
    }

    public function simpanPresensi(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required',
            'semester' => 'required',
            'pertemuan' => 'required|integer',
            'status_presensi' => 'array'
        ]);

        $dosen = session('dosen');

        $mahasiswaKelas = PesertaKelasMatakuliah::where([
            'kode_mk' => $request->kode_mk,
            'nik' => $dosen->nik,
            'semester' => $request->semester
        ])->pluck('nim')->toArray();

        try {
            DB::beginTransaction();

            Realisasi::updateOrCreate(
                [
                    'kode_mk' => $request->kode_mk,
                    'nik' => $dosen->nik,
                    'semester' => $request->semester,
                    'pertemuan' => $request->pertemuan
                ],
                [
                    'realisasi_perkuliahan' => $request->realisasi_perkuliahan
                ]
            );

            foreach ($mahasiswaKelas as $nim) {
                $status = $request->status_presensi[$nim] ?? 'A';

                Presensi::updateOrCreate(
                    [
                        'kode_mk' => $request->kode_mk,
                        'nik' => $dosen->nik,
                        'semester' => $request->semester,
                        'pertemuan' => $request->pertemuan,
                        'nim' => $nim
                    ],
                    [
                        'status_presensi' => $status
                    ]
                );
            }

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Presensi berhasil disimpan!');
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
