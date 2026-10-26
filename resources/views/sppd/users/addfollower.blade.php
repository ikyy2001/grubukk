@extends('layouts.app')

@section('title', 'Tambah Pengikut')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Tambah Pengikut SPPD</h1>
        <p class="page-subtitle">Nomor Surat - {{ $sppd->no_surat }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
    </div>
</div>

@include('layouts.sppdnav')

@php
    $existing = collect();
    foreach ($sppd->user as $sppd_data) {
        $existing->push($sppd_data->nip);
    }
@endphp

<div class="card card--pad-lg" style="margin-bottom:16px">
    <div class="card-head">
        <div>
            <h2 class="card-title">Daftar Pengikut Saat Ini</h2>
            <p class="card-note">Pegawai yang ikut dalam perjalanan dinas</p>
        </div>
    </div>
    @if ($sppduser->count() > 0)
        <div style="display:flex; flex-direction:column; gap:10px">
            @php
                $no = 1;
                $primaryUserId = optional($sppduser->first())->users_id;
            @endphp
            @foreach ($sppduser as $sppd_data2)
                @foreach ($sppduserpegawai as $sppd_data)
                    @if ($sppd_data->id == $sppd_data2->users_id)
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; border:1px solid var(--border); border-radius:10px">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px">
                                    <strong>{{ $no++ }}. {{ $sppd_data->nama }}</strong>
                                    @if ($sppd_data->id == $primaryUserId)
                                        <span class="tag" style="background:rgba(59, 130, 246, 0.15); color:#2563eb; font-size:11px">Pegawai Utama</span>
                                    @else
                                        <span class="tag" style="background:rgba(107, 114, 128, 0.15); color:var(--text); font-size:11px">Pengikut</span>
                                    @endif
                                </div>
                                <div class="muted">NIP. {{ $sppd_data->nip }}</div>
                            </div>
                            @if ($sppd_data->id != $primaryUserId)
                                <form action="{{ route('SPPD') }}/{{ $sppd_data2->sppd_id }}/{{ $sppd_data->id }}/delete" method="post" data-confirm="Hapus {{ $sppd_data->nama }} dari pengikut?">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn--danger" style="height:32px; padding:0 12px">
                                        <x-icon name="trash-2" :size="14" /> Hapus
                                    </button>
                                </form>
                            @else
                                <span class="muted" style="font-size:12px; font-style:italic">Tidak dapat dihapus</span>
                            @endif
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    @else
        <p class="muted">Belum ada pengikut ditambahkan</p>
    @endif
</div>

<div class="card card--pad-lg">
    <div class="card-head">
        <div>
            <h2 class="card-title">Tambah Pengikut</h2>
            <p class="card-note">Cari berdasarkan nama atau NIP</p>
        </div>
    </div>
    <form action="{{ route('SPPD') }}/{{ $sppd->id }}/add" method="post" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap">
        @csrf
        <div class="form-row" style="flex:1; min-width:280px">
            <label class="form-label">Pilih Pegawai</label>
            <select name="nip" class="select" required>
                <option disabled selected>Pilih pegawai...</option>
                @foreach ($all_user as $this_user)
                    <option value="{{ $this_user->nip }}" @if ($existing->contains($this_user->nip)) disabled @endif>
                        {{ $this_user->nip }} - {{ $this_user->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn--primary" style="height:40px">
            <x-icon name="user-plus" :size="16" /> Tambah
        </button>
    </form>
</div>
@endsection