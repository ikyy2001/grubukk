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
            <h2 class="card-title">Opsi Cetak & Pengisian</h2>
            <p class="card-note">Cetak dokumen atau lengkapi data perjalanan dinas</p>
        </div>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px">
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
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/add" class="btn btn--primary" style="justify-content:flex-start">
            <x-icon name="file-edit" :size="16" /> Lengkapi Data
        </a>
    </div>
</div>
@endsection