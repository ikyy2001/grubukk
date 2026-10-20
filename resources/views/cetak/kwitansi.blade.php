<?php
include_once(app_path('FPDF/fpdf.php'));

$border = 0;
$path = public_path('img/logo.png');
$pdf = new FPDF('P', 'mm', array(330, 210));
$pdf->SetTitle('Kwitansi - ' . $sppd->no_surat . '/' . $sppd->tahun_surat . ' - ' . strtotime(now()));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Image($path, 15, 12, 16);
$pdf->Cell(0, 2, '', $border, 1);
$pdf->Cell(0, 6, 'PT Navigasi Tripindo Utama', $border, 1, 'C');
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 6, 'TRIPNAV TECHNOLOGY PERKASA', $border, 1, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 4, 'Jl. Kelurahan No.15, Telp. (031) 8292419, 8292234, 8291711, 8295822', $border, 1, 'C');
$pdf->Cell(0, 4, 'Faks.(031) 8292047 E-mail : sapa@tripnav.id Website : www.tripnav.id', $border, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, 'BOGOR', $border, 1, 'C');
$pdf->Cell(0, 6, 'Kode Pos 60235               ', $border, 1, 'R');
$pdf->Cell(0, 6, '', $border, 1, 'C');

$pdf->SetFont('Arial', 'BU', 15);
$pdf->Cell(0, 8, 'KWITANSI', $border, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Nomor : KW / ' . $sppd->no_surat . ' / 104.2 / ' . $sppd->tahun_surat, $border, 1, 'C');
$pdf->Cell(0, 4, '', $border, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, 'Sudah Terima Dari', $border, 0, 'L');
$pdf->Cell(5, 6, ':', $border, 0, 'L');
$pdf->Cell(0, 6, 'Kuasa Pengguna Anggaran Dinas Pekerjaan Umum Sumber Daya Air', $border, 1, 'L');

$pdf->Cell(40, 6, 'Uang Sejumlah', $border, 0, 'L');
$pdf->Cell(5, 6, ':', $border, 0, 'L');
$pdf->Cell(0, 6, 'Rp. ___________________________ (___________________________)', $border, 1, 'L');

$pdf->Cell(40, 6, 'Untuk Pembayaran', $border, 0, 'L');
$pdf->Cell(5, 6, ':', $border, 0, 'L');
$pdf->Multicell(0, 6, 'Biaya Perjalanan Dinas berdasarkan SPT/SPPD Nomor 094 / ' . $sppd->no_surat . ' / 104.2 / ' . $sppd->tahun_surat . ' tanggal ' . date('d-m-Y', strtotime($sppd->tgl_surat)) . ' untuk pelaksanaan "' . $sppd->acara . '" ke ' . ($sppd->tempat->tempat_tujuan ?? '-') . ', ' . $lama . ' hari.', $border, 'J');

$pdf->Cell(0, 6, '', $border, 1, 'C');
$pdf->Cell(40, 6, 'Terbilang', $border, 0, 'L');
$pdf->Cell(5, 6, ':', $border, 0, 'L');
$pdf->Cell(0, 6, '_______________________________________________________________', $border, 1, 'L');

$pdf->Cell(0, 12, '', $border, 1, 'C');

$pdf->Cell(95, 6, '', $border, 0, 'L');
$pdf->Cell(95, 6, 'Surabaya, ' . date('d-m-Y', strtotime($sppd->tgl_surat)), $border, 1, 'C');
$pdf->Cell(95, 6, '', $border, 0, 'L');
$pdf->Cell(95, 6, 'Yang Menerima', $border, 1, 'C');
$pdf->Cell(0, 20, '', $border, 1, 'C');

$first = $sppduser->first();
$nama = '-';
$nip = '-';
if ($first) {
    foreach ($sppduserpegawai as $p) {
        if ($p->id == $first->users_id) {
            $nama = $p->nama;
            $nip = $p->nip;
            break;
        }
    }
}

$pdf->Cell(95, 6, '', $border, 0, 'L');
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(95, 6, $nama, $border, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, '', $border, 0, 'L');
$pdf->Cell(95, 6, 'NIP. ' . $nip, $border, 1, 'C');

$pdf->Cell(0, 16, '', $border, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'MENGETAHUI / MENYETUJUI', $border, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Kuasa Pengguna Anggaran', $border, 1, 'C');
$pdf->Cell(0, 18, '', $border, 1, 'C');
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(0, 6, $sppd->kabid->nama_kabid ?? '-', $border, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, ($sppd->kabid->jabatan ?? '-') . '  NIP. ' . ($sppd->kabid->nip ?? '-'), $border, 1, 'C');

$pdf->Output('I', 'Kwitansi - ' . $sppd->no_surat . '/' . $sppd->tahun_surat);
exit;
