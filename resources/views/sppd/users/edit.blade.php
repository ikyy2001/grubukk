@extends('layouts.app')

@section('title', 'Ringkasan SPPD')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Ringkasan SPPD</h1>
        <p class="page-subtitle">Detail surat perintah perjalanan dinas</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali
        </a>
    </div>
</div>

<div class="card card--pad-lg" style="margin-bottom:16px">
    <div class="card-head">
        <div>
            <h2 class="card-title">Nomor & Tahun Surat</h2>
        </div>
    </div>
    <p class="muted">Nomor Surat : <span class="tabular">{{ $sppd->no_surat }}</span> / {{ $sppd->tahun_surat }}</p>
</div>

<div class="card card--pad-lg" style="margin-bottom:16px">
    <div class="card-head">
        <div>
            <h2 class="card-title">Dasar Surat</h2>
        </div>
    </div>
    <ol style="margin:0; padding-left:20px; display:flex; flex-direction:column; gap:6px">
        @php $no = 1; @endphp
        @foreach ($sppd->dasar_surat as $sppd_data)
            <li>{{ $sppd_data->dasar_surat }}</li>
        @endforeach
    </ol>
</div>

<div class="card card--pad-lg" style="margin-bottom:16px">
    <div class="card-head">
        <div>
            <h2 class="card-title">Daftar Yang Diperintah</h2>
        </div>
    </div>
    <ol style="margin:0; padding-left:20px; display:flex; flex-direction:column; gap:6px">
        @php $no = 1; @endphp
        @foreach ($sppd->user as $sppd_data)
            <li>{{ $sppd_data->nama }} / {{ $sppd_data->nip }}</li>
        @endforeach
    </ol>
</div>

<div class="card card--pad-lg" style="margin-bottom:16px">
    <div class="card-head">
        <div>
            <h2 class="card-title">Acara (Maksud Perjalanan Dinas)</h2>
        </div>
    </div>
    <p>{{ $sppd->acara }}</p>
</div>

<div class="card card--pad-lg" style="margin-bottom:16px">
    <div class="card-head">
        <div>
            <h2 class="card-title">Tempat</h2>
        </div>
    </div>
    <div style="display:flex; flex-direction:column; gap:6px">
        <p><span class="muted">Berangkat :</span> {{ $sppd->tempat->tempat_berangkat }}</p>
        <p><span class="muted">Tujuan &nbsp;&nbsp;&nbsp;&nbsp;:</span> {{ $sppd->tempat->tempat_tujuan }}</p>
    </div>
</div>

<div class="card card--pad-lg">
    <div class="card-head">
        <div>
            <h2 class="card-title">Tanggal</h2>
        </div>
    </div>
    <div style="display:flex; flex-direction:column; gap:6px">
        <p><span class="muted">Berangkat :</span> {{ $sppd->tgl_pergi }}</p>
        <p><span class="muted">Kembali &nbsp;&nbsp;:</span> {{ $sppd->tgl_kembali }}</p>
    </div>
</div>
@endsection