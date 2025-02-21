<?php

namespace App\Http\Controllers;

use App\Models\KelasMatakuliah;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use TCPDF;
use Undika\PdfGenerator\PdfRekapPresensi;
use ZipArchive;

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

    public function generateRekapHarian()
    {
        $mkWithPresensi = Presensi::select('kode_mk')
            ->distinct()
            ->pluck('kode_mk')
            ->toArray();

        $kelasList = KelasMatakuliah::with(['matakuliah', 'dosen'])
            ->whereIn('kode_mk', $mkWithPresensi)
            ->get();

        $files = [];
        $storagePath = storage_path('app/rekap_harian');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        foreach ($kelasList as $kelas) {
            $presensiPerPertemuan = Presensi::where('kode_mk', $kelas->kode_mk)
                ->where('semester', $kelas->semester)
                ->where('nik', $kelas->nik)
                ->orderBy('pertemuan')
                ->get()
                ->groupBy('pertemuan');

            foreach ($presensiPerPertemuan as $pertemuan => $presensiData) {
                $pdf = new PdfRekapPresensi(
                    new TCPDF(),
                    $kelas->semester,
                    $kelas->kode_mk,
                    $kelas->matakuliah->nama_mk ?? '-',
                    $kelas->nik,
                    $kelas->dosen->nama ?? '-',
                    $pertemuan
                );

                foreach ($presensiData as $presensi) {
                    $pdf->addDataPresensi(
                        $presensi->nim,
                        $presensi->mahasiswa->nama ?? '-',
                        $presensi->status_presensi === 'H'
                    );
                }

                $fileName = $pdf->generatePdfAsFile($storagePath);
                $pdfPath = "{$storagePath}/{$fileName}.pdf";

                if (file_exists($pdfPath)) {
                    $files[] = $pdfPath;
                } else {
                    \Log::error("File PDF tidak ditemukan: " . $pdfPath);
                }
            }
        }


        $zipPath = storage_path('app/rekap_harian/rekap_harian.zip');
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $zip->addFile($file, basename($file));
                } else {
                    \Log::error("File tidak ditemukan: " . $file);
                }
            }
            $zip->close();
        } else {
            \Log::error("Gagal membuka file ZIP untuk ditulis.");
        }

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function downloadRekapHarian()
    {
        $zipPath = storage_path('app/rekap_harian/rekap_harian.zip');

        if (file_exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend();
        }

        return response()->json(['message' => 'File tidak ditemukan'], 404);
    }
}
