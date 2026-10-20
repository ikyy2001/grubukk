@extends('layouts.app')

@section('title', 'Keterangan')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Lengkapi Data SPPD</h1>
        <p class="page-subtitle">Langkah 3: Keterangan</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
    </div>
</div>

@include('layouts.sppdnav')

<form method="POST" action="{{ route('SPPD') }}/{{ $sppd->id }}/keterangan">
    @method('patch')
    @csrf
    <div class="card card--pad-lg">
        <div class="card-head">
            <div>
                <h2 class="card-title">Keterangan</h2>
                <p class="card-note">Opsional, isi jika ada keterangan tambahan</p>
            </div>
        </div>
        <div class="form-row">
            <label class="form-label">Keterangan</label>
            <textarea class="input" rows="3" name="Keterangan" placeholder="(Opsional. Isi bila ada)">{{ $sppd->keterangan->keterangan ?? '' }}</textarea>
        </div>
        <div style="display:flex; justify-content:flex-end; margin-top:20px">
            <button type="submit" class="btn btn--primary">
                <x-icon name="save" :size="16" /> Simpan
            </button>
        </div>
    </div>
</form>
@endsection