@extends('layouts.app')

@section('title', 'Beban Biaya')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Lengkapi Data SPPD</h1>
        <p class="page-subtitle">Langkah 2: Beban Biaya</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
    </div>
</div>

@include('layouts.sppdnav')

<form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/bebanbiaya">
    @method('patch')
    @csrf
    <div class="card card--pad-lg">
        <div class="card-head">
            <div>
                <h2 class="card-title">Beban Biaya</h2>
                <p class="card-note">Pilih SKPD, Program, Kegiatan, dan Kode Rekening</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Satuan Kerja Perangkat Daerah (SKPD)</label>
                <select name="SKPD" class="select">
                    <option disabled {{ $bbsppd->skpd ? '' : 'selected' }}>Pilih SKPD...</option>
                    @foreach ($skpd as $skpd_data)
                        <option value="{{ $skpd_data->skpd }}" @if ($bbsppd->skpd == $skpd_data->skpd) selected @endif>{{ $skpd_data->skpd }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Program</label>
                <select name="Program" class="select">
                    <option disabled {{ $bbsppd->program ? '' : 'selected' }}>Pilih Program...</option>
                    @foreach ($program as $program_data)
                        <option value="{{ $program_data->program }}" @if ($bbsppd->program == $program_data->program) selected @endif>{{ $program_data->program }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Kegiatan</label>
                <select name="Kegiatan" class="select">
                    <option disabled {{ $bbsppd->kegiatan ? '' : 'selected' }}>Pilih Kegiatan...</option>
                    @foreach ($kegiatan as $kegiatan_data)
                        <option value="{{ $kegiatan_data->kegiatan }}" @if ($bbsppd->kegiatan == $kegiatan_data->kegiatan) selected @endif>{{ $kegiatan_data->kegiatan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Kode Rekening</label>
                <select name="KodeRekening" class="select">
                    <option disabled {{ $bbsppd->rekening ? '' : 'selected' }}>Pilih Kode Rekening...</option>
                    @foreach ($rekening as $rekening_data)
                        <option value="{{ $rekening_data->rekening }}" @if ($bbsppd->rekening == $rekening_data->rekening) selected @endif>{{ $rekening_data->rekening }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; margin-top:20px">
            <button type="submit" class="btn btn--primary">
                <x-icon name="save" :size="16" /> Simpan
            </button>
        </div>
    </div>
</form>
@endsection