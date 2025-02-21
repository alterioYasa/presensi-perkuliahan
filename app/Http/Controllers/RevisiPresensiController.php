<?php

namespace App\Http\Controllers;

use App\Models\PesertaKelasMatakuliah;
use App\Models\Presensi;
use App\Models\Realisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevisiPresensiController extends Controller
{
    public function revisiPresensi($kode_mk, $semester)
    {
        $dosen = session('dosen');

        $presensi = Presensi::where('kode_mk', $kode_mk)
            ->where('nik', $dosen->nik)
            ->where('semester', $semester)
            ->with('mahasiswa')
            ->get();

        $realisasi = Realisasi::where('kode_mk', $kode_mk)
            ->where('nik', $dosen->nik)
            ->where('semester', $semester)
            ->first();

        $daftarPertemuan = Presensi::where('kode_mk', $kode_mk)
            ->where('semester', $semester)
            ->distinct()
            ->pluck('pertemuan')
            ->sort()
            ->toArray();

        $peserta = PesertaKelasMatakuliah::where('kode_mk', $kode_mk)
            ->where('semester', $semester)
            ->with('mahasiswa')
            ->get();

        return view('presensi.input-revisi', compact('presensi', 'kode_mk', 'semester', 'dosen', 'realisasi', 'daftarPertemuan', 'peserta'));
    }

    public function simpanRevisiPresensi(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required',
            'semester' => 'required',
            'pertemuan' => 'required|integer',
            'status_presensi' => 'array',
            'alasan_revisi' => 'required'
        ]);

        $dosen = session('dosen');

        $mahasiswaKelas = PesertaKelasMatakuliah::where([
            'kode_mk' => $request->kode_mk,
            'nik' => $dosen->nik,
            'semester' => $request->semester
        ])->pluck('nim')->toArray();

        try {
            DB::beginTransaction();

            $updated = DB::connection('client')->table('realisasi')
                ->where('kode_mk', $request->kode_mk)
                ->where('nik', $dosen->nik)
                ->where('semester', $request->semester)
                ->where('pertemuan', $request->pertemuan)
                ->update([
                    'realisasi_perkuliahan' => $request->realisasi_perkuliahan,
                    'alasan_revisi' => $request->alasan_revisi
                ]);

            if ($updated === 0) {
                DB::connection('client')->table('realisasi')->insert([
                    'kode_mk' => $request->kode_mk,
                    'nik' => $dosen->nik,
                    'semester' => $request->semester,
                    'pertemuan' => $request->pertemuan,
                    'realisasi_perkuliahan' => $request->realisasi_perkuliahan,
                    'alasan_revisi' => $request->alasan_revisi
                ]);
            }

            Presensi::where([
                'kode_mk' => $request->kode_mk,
                'nik' => $dosen->nik,
                'semester' => $request->semester,
                'pertemuan' => $request->pertemuan,
            ])->delete();

            $data = [];

            foreach ($mahasiswaKelas as $nim) {
                $status = $request->status_presensi[$nim] ?? 'A';

                $data[] = [
                    'kode_mk' => $request->kode_mk,
                    'nik' => $dosen->nik,
                    'semester' => $request->semester,
                    'pertemuan' => $request->pertemuan,
                    'nim' => $nim,
                    'status_presensi' => $status
                ];
            }

            if (!empty($data)) {
                Presensi::insert($data);
            }

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Revisi presensi berhasil disimpan!');
        } catch (\Throwable $th) {
            DB::rollBack();

            dd($th->getMessage());
        }
    }
}
