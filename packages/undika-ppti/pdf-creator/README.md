# Komponen Untuk Generate PDF Rekap Presensi

## Dependency

Membutuhkan TCPDF versi 6.3 atau lebih tinggi.

## Contoh Penggunaan

```php

// inisialisasi komponen dependency
$engine = new TCPDF();

// inisialisasi komponen
$pdf = new PdfRekapPresensi(
    $engine, 
    $semester, 
    $kode_mk,
    $nama_mk, 
    $nomor_induk_dosen,
    $nama_dosen, 
    $pertemuan_ke
);

// tambahkan satu per satu data presensi mahasiswa
$pdf->addDataPresensi(
    $nomor_induk_mahasiswa, 
    $nama_mahasiswa, 
    $status_kehadiran // true atau false, default false
);

// output ke browser
$pdf->generatePdfAsInline();

// atau output sebagai file di server
$pdf->generatePdfAsFile('../../rekapPresensi/');


```
