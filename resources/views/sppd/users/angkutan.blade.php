@extends('layouts.app')

@section('title', 'Alat Angkutan')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Lengkapi Data SPPD</h1>
        <p class="page-subtitle">Langkah 1: Alat Angkutan</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
    </div>
</div>

@include('layouts.sppdnav')

@php
    $dd = 0;
    $no = 1;
    foreach ($tempatb as $tempat) {
        if ($tempat == $sppd->tempat->tempat_tujuan) {
            if ($no >= 32) { $dd = 0; } else { $dd = 1; }
        }
        $no++;
    }
@endphp

<div class="card card--pad-lg">
    <div class="card-head">
        <div>
            <h2 class="card-title">Alat Angkutan</h2>
            <p class="card-note">{{ $dd == 1 ? '(Dalam Daerah)' : '(Luar Daerah)' }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/angkutan">
        @method('patch')
        @csrf
        <div class="form-row" style="margin-bottom:16px">
            <label class="form-label">Alat Angkutan</label>
            <div style="display:flex; gap:10px; align-items:center">
                <select name="Angkutan" class="select" style="flex:1">
                    <option disabled {{ $sppd->angkutan->angkutan ? '' : 'selected' }}>Pilih Alat Angkutan...</option>
                    <option value="1" @if ($sppd->angkutan->angkutan == "Angkutan Dinas") selected @endif>Angkutan Dinas</option>
                    <option value="2" @if ($sppd->angkutan->angkutan == "Angkutan Pribadi") selected @endif>Angkutan Pribadi</option>
                    <option value="3" @if ($sppd->angkutan->angkutan == "Angkutan Umum") selected @endif>Angkutan Umum</option>
                    <option value="4" @if ($sppd->angkutan->angkutan == "Angkutan Sewa") selected @endif>Angkutan Sewa</option>
                </select>
                <button type="submit" class="btn btn--primary">Simpan</button>
            </div>
        </div>
    </form>

    @if ($sppd->angkutan->angkutan == 'Angkutan Dinas' || $sppd->angkutan->angkutan == 'Angkutan Pribadi')
        <hr style="border:none; border-top:1px solid var(--border); margin:16px 0">
        <form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/jenis">
            @method('patch')
            @csrf
            <div class="form-row" style="margin-bottom:16px">
                <label class="form-label">Jenis Kendaraan</label>
                <div style="display:flex; gap:10px; align-items:center">
                    <select name="Jenis" class="select" style="flex:1">
                        <option disabled {{ $sppd->angkutan->jenis ? '' : 'selected' }}>Pilih Jenis Kendaraan...</option>
                        <option value="1" @if ($sppd->angkutan->jenis == "Roda Dua") selected @endif>Roda Dua</option>
                        <option value="2" @if ($sppd->angkutan->jenis == "Roda Empat") selected @endif>Roda Empat</option>
                    </select>
                    <button type="submit" class="btn btn--primary">Simpan</button>
                </div>
            </div>
        </form>

        @if (!is_null($sppd->angkutan->jenis))
            <form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/plat">
                @method('patch')
                @csrf
                <div class="form-row">
                    <label class="form-label">Plat Nomor</label>
                    <div style="display:flex; gap:10px; align-items:center">
                        <input type="text" class="input" name="Plat" value="{{ $sppd->angkutan->plat }}" placeholder="Plat Nomor" style="flex:1">
                        <button type="submit" class="btn btn--primary">Simpan</button>
                    </div>
                </div>
            </form>
        @endif
    @endif

    @if ($sppd->angkutan->angkutan == "Angkutan Umum")
        <hr style="border:none; border-top:1px solid var(--border); margin:16px 0">
        <form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/umum">
            @method('patch')
            @csrf
            <div class="form-row">
                <label class="form-label">Angkutan Umum</label>
                <div style="display:flex; gap:10px; align-items:center">
                    <select name="Umum" class="select" style="flex:1">
                        <option disabled {{ $sppd->angkutan->angkutan_umum ? '' : 'selected' }}>Pilih Alat Angkutan Umum...</option>
                        <option value="1" @if ($sppd->angkutan->angkutan_umum == "Pesawat") selected @endif>Pesawat</option>
                        <option value="2" @if ($sppd->angkutan->angkutan_umum == "Kereta") selected @endif>Kereta</option>
                        <option value="3" @if ($sppd->angkutan->angkutan_umum == "Kapal") selected @endif>Kapal</option>
                    </select>
                    <button type="submit" class="btn btn--primary">Simpan</button>
                </div>
            </div>
        </form>
    @endif

    @if ($sppd->angkutan->angkutan == "Angkutan Sewa")
        <hr style="border:none; border-top:1px solid var(--border); margin:16px 0">
        <form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/sewa">
            @method('patch')
            @csrf
            <div class="form-row">
                <label class="form-label">Alat Angkutan yang Disewa</label>
                <div style="display:flex; gap:10px; align-items:center">
                    <select name="Sewa" class="select" style="flex:1">
                        <option disabled {{ $sppd->angkutan->sewa ? '' : 'selected' }}>Pilih Alat Angkutan yang Disewa...</option>
                        <option value="1" @if ($sppd->angkutan->sewa == "Roda Enam/Bus Besar") selected @endif>Roda Enam/Bus Besar</option>
                        <option value="2" @if ($sppd->angkutan->sewa == "Roda Enam/Bus Sedang") selected @endif>Roda Enam/Bus Sedang</option>
                        <option value="3" @if ($sppd->angkutan->sewa == "Roda Empat/Bus Mini") selected @endif>Roda Empat/Bus Mini</option>
                        <option value="4" @if ($sppd->angkutan->sewa == "Roda Empat") selected @endif>Roda Empat</option>
                        @if ($dd == 1)
                            <option value="5" @if ($sppd->angkutan->sewa == "Roda Dua") selected @endif>Roda Dua</option>
                        @endif
                    </select>
                    <button type="submit" class="btn btn--primary">Simpan</button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection