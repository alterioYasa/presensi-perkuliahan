<?php

use Undika\PdfGenerator\PdfRekapPresensi;

require __DIR__.'/../vendor/autoload.php';

$dataListPresensi = [
    ['nim' => '1241010001', 'status_hadir' => 'H'],
    ['nim' => '1241010002', 'status_hadir' => 'H'],
    ['nim' => '1241010003', 'status_hadir' => 'A'],
    ['nim' => '1241010004', 'status_hadir' => 'H'],
    ['nim' => '1241010006', 'status_hadir' => 'H'],
    ['nim' => '1241010011', 'status_hadir' => 'A'],
    ['nim' => '1241010012', 'status_hadir' => 'H'],
    ['nim' => '1241010013', 'status_hadir' => 'H'],
];

$tcpdf = new TCPDF('P');
$pdf = new PdfRekapPresensi($tcpdf, '201', '11001', 'Pengantar ABC', 'Q2', 9);
foreach ($dataListPresensi as $dataPresensi) {
    $pdf->addDataPresensi($dataPresensi['nim'], 'H' == $dataPresensi['status_hadir']);
}

$pdf->generatePdfAsFile(__DIR__.'/pdf');
exit;
