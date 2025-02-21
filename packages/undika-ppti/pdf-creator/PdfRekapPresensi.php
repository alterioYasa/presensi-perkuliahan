<?php

namespace Undika\PdfGenerator;

use TCPDF;

class PdfRekapPresensi
{
    const PDF_TITLE = 'Rekap Presensi Perkuliahan';

    protected $pdf = null;
    protected $semester = '';
    protected $kodeMk = '';
    protected $namaMk = '';
    protected $nik = '';
    protected $namaDosen = '';
    protected $pertemuan = '';

    protected $tableContent = '';
    protected $dataRow = 0;

    public function __construct(
        TCPDF $pdf,
        $semester, $kodeMk, $namaMk, $nik, $namaDosen, $pertemuan
    ) {
        $this->semester = $semester;
        $this->kodeMk = $kodeMk;
        $this->namaMk = $namaMk;
        $this->nik = $nik;
        $this->namaDosen = $namaDosen;
        $this->pertemuan = $pertemuan;

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Universitas Dinamika');
        $pdf->SetSubject('Rekap Presensi Perkuliahan');

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+10, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);

        $this->pdf = $pdf;
    }

    public function addDataPresensi($nim, $nama, $isHadir = false)
    {
        ++$this->dataRow;
        $statusKehadiran = $isHadir ? 'Hadir' : '<span style="color:red">Alpa</span>';

        $this->tableContent .= <<<HTML
<tr>
    <td width="30">{$this->dataRow}</td>
    <td width="100">{$nim}</td>
    <td width="330">{$nama}</td>
    <td width="50">{$statusKehadiran}</td>
</tr>
HTML;

        return $this;
    }

    private function buildTable()
    {
        $table = <<<HTML
<table cellspacing="0" cellpadding="1" border="1">
    <thead>
        <tr>
            <th width="30">#</th>
            <th width="100">NIM</th>
            <th width="330">Nama</th>
            <th width="50">Presensi</th>
        </tr>
    </thead>
    <tbody>{$this->tableContent}</tbody>
</table>
HTML;
        $this->pdf->writeHTML($table);
    }

    protected function preparePdf()
    {
        $currentTime = new \DateTime();
        $titleTime = $currentTime->format('d-m-Y H:i:s');

        $this->pdf->SetHeaderData(
            '', 0,
            self::PDF_TITLE,
            "{$this->namaMk} ({$this->semester}) pert.{$this->pertemuan}\n{$this->namaDosen} ({$this->nik})\nDibuat pada {$titleTime}",
            [0, 64, 255],
            [0, 64, 128]
        );

        $fileTime = $currentTime->format('YmdHis');
        $fileName = "rekap_presensi-{$this->semester}-{$this->kodeMk}-{$this->nik}-{$this->pertemuan}-{$fileTime}";

        $this->pdf->SetTitle($fileName);

        $this->pdf->AddPage();
        $this->buildTable();

        return $fileName;
    }

    public function generatePdfAsInline()
    {
        $fileName = $this->preparePdf();

        return $this->pdf->Output("{$fileName}.pdf", 'I');
    }

    public function generatePdfAsFile($dir = __DIR__)
    {
        $fileName = $this->preparePdf();

        return $this->pdf->Output("{$dir}/{$fileName}.pdf", 'F');
    }
}
