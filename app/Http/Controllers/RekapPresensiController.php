<?php

namespace App\Http\Controllers;

use App\Models\KelasMatakuliah;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class RekapPresensiController extends Controller
{
    public function generatePDF($kode_mk, $semester)
    {
        $dosen = session('dosen');

        $mataKuliah = KelasMatakuliah::where('kode_mk', $kode_mk)
            ->where('nik', $dosen->nik)
            ->where('semester', $semester)
            ->with('matakuliah')
            ->first();

        $presensi = Presensi::where('kode_mk', $kode_mk)
            ->where('nik', $dosen->nik)
            ->where('semester', $semester)
            ->get()
            ->groupBy('nim');

        $totalPertemuan = Presensi::where('kode_mk', $kode_mk)
            ->where('nik', $dosen->nik)
            ->where('semester', $semester)
            ->distinct('pertemuan')
            ->count();


        $data = "";

        $n = 1;
        foreach ($presensi as $nim => $dataPresensi) {
            $data .= '<tr>
                    <td>' . $n++ . '</td> 
                    <td>' . $nim . '</td>
                    <td>' . ($dataPresensi->first()->mahasiswa->nama ?? 'Tidak Diketahui') . '</td>
                    <td>' . $dataPresensi->where('status_presensi', 'H')->count() . '</td>
                    <td>' . $dataPresensi->where('status_presensi', 'A')->count() . '</td>
                </tr>';
        }

        $html = '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Rekap Presensi</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class="title">Rekap Presensi Mata Kuliah</div>
            <p><strong>Kode MK:</strong>' . $mataKuliah->kode_mk . '</p>
            <p><strong>Nama Mata Kuliah:</strong>' . $mataKuliah->matakuliah->nama_mk . '</p>
            <p><strong>Semester:</strong>' . $mataKuliah->semester . '</p>
            <p><strong>Dosen:</strong>' . $dosen->nama . '</p>
            <p><strong>Total Pertemuan:</strong> ' . $totalPertemuan . '</p>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jumlah Hadir</th>
                        <th>Jumlah Tidak Hadir</th>
                    </tr>
                </thead>
                <tbody>
                        ' . $data . '
                </tbody>
            </table>
        </body>
        </html>';

        $mpdf = new Mpdf(
            [
                'orientation' => 'P',
                'default_font_size' => 10,
                'default_font' => 'Sans-Serif',
                'format' => 'legal',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 5,
                'margin_bottom' => 0,
            ]
        );

        $mpdf->WriteHTML($html);
        $pdf = $mpdf->Output('test', \Mpdf\Output\Destination::STRING_RETURN);

        return response()->json([
            'pdf_base64' => base64_encode($pdf)
        ]);
    }
}
