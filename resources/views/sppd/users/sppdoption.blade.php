@extends('layouts.app')

@section('title', 'Aksi SPPD')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Surat Perintah Perjalanan Dinas</h1>
        <p class="page-subtitle">Pilih tindakan untuk SPPD ini</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali
        </a>
    </div>
</div>

<div class="card card--pad-lg" style="max-width: 640px; margin: 0 auto">
    <div class="card-head">
        <div>
            <h2 class="card-title">Status: 
                @if ($sppd->status == 'pending_atasan')
                    <span class="tag" style="background:rgba(234, 179, 8, 0.15); color:#ca8a04; border:1px solid rgba(234, 179, 8, 0.3)">Menunggu Persetujuan Atasan</span>
                @elseif ($sppd->status == 'pending_keuangan')
                    <span class="tag" style="background:rgba(59, 130, 246, 0.15); color:#2563eb; border:1px solid rgba(59, 130, 246, 0.3)">Menunggu Persetujuan Keuangan</span>
                @elseif ($sppd->status == 'approved')
                    <span class="tag" style="background:rgba(34, 197, 94, 0.15); color:#16a34a; border:1px solid rgba(34, 197, 94, 0.3)">Disetujui</span>
                @elseif ($sppd->status == 'rejected')
                    <span class="tag" style="background:rgba(239, 68, 68, 0.15); color:#dc2626; border:1px solid rgba(239, 68, 68, 0.3)">Ditolak</span>
                @else
                    <span class="tag">{{ $sppd->status }}</span>
                @endif
            </h2>
            <p class="card-note">No. Surat: <strong>{{ $sppd->no_surat }}/{{ $sppd->tahun_surat }}</strong> &bull; Acara: {{ $sppd->acara }}</p>
            @if ($sppd->file_undangan)
                <div style="margin-top:8px">
                    <a href="{{ asset('storage/' . $sppd->file_undangan) }}" target="_blank" class="btn btn--outline btn--sm">
                        <x-icon name="file-text" :size="14" /> Unduh Dokumen Pendukung (TOR/Undangan)
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px">
        @if (in_array($sppd->status, ['rejected', 'pending_atasan']))
            <a href="{{ route('EditSPPD', $sppd->id) }}" class="btn btn--primary" style="justify-content:flex-start">
                <x-icon name="file-edit" :size="16" /> Edit &amp; Perbaiki Pengajuan SPPD
            </a>
            <hr style="border:none; border-top:1px solid var(--border); margin: 4px 0">
        @endif

        @if (in_array($sppd->status, ['approved', 'completed']))
            <a href="{{ route('CetakSPT') }}/{{ $sppd->id }}" target="_blank" class="btn btn--outline" style="justify-content:flex-start">
                <x-icon name="printer" :size="16" /> Cetak SPT
            </a>
            <a href="{{ route('CetakSPPD') }}/{{ $sppd->id }}" target="_blank" class="btn btn--outline" style="justify-content:flex-start">
                <x-icon name="printer" :size="16" /> Cetak SPPD
            </a>
            <a href="{{ route('CetakKWITANSI') }}/{{ $sppd->id }}" target="_blank" class="btn btn--outline" style="justify-content:flex-start">
                <x-icon name="receipt" :size="16" /> Cetak Kwitansi
            </a>
            <hr style="border:none; border-top:1px solid var(--border); margin: 8px 0">
            <a href="{{ url('Reimbursement/'.$sppd->id) }}" class="btn btn--primary" style="justify-content:flex-start">
                <x-icon name="dollar-sign" :size="16" /> Lapor Biaya Riil & Reimbursement Nota
            </a>
        @else
            <div style="padding:12px; background:rgba(234, 179, 8, 0.1); border-radius:6px; color:#854d0e; font-size:13px">
                <x-icon name="alert-circle" :size="16" /> Dokumen cetak (SPT/SPPD/Kwitansi) dan Pelaporan Biaya Riil hanya dapat diakses setelah SPPD mendapatkan persetujuan penuh dari Atasan dan Keuangan.
            </div>
        @endif
        <hr style="border:none; border-top:1px solid var(--border); margin: 8px 0">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/add" class="btn btn--outline" style="justify-content:flex-start">
            <x-icon name="file-edit" :size="16" /> Kelola Pengikut & Data Teknis
        </a>
    </div>
</div>
@endsection