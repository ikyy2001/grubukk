@extends('layouts.app')

@section('title', 'SPPD Saya')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<div class="page-head">
    <div>
        <h1 class="page-title">Daftar SPPD Saya</h1>
        <p class="page-subtitle">Kelola surat perintah tugas dan perjalanan dinas Anda</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('EntrySPPD') }}" class="btn btn--primary">
            <x-icon name="plus" :size="16" /> Buat SPT / SPPD
        </a>
    </div>
</div>

<div class="card card--pad-lg">
    <div class="card-head">
        <div>
            <h2 class="card-title">Daftar SPPD</h2>
            <p class="card-note">Cari berdasarkan nomor surat atau acara</p>
        </div>
        <form action="{{ route('SPPD') }}" method="GET" class="search-pill" style="min-width: 280px">
            <x-icon name="search" :size="16" />
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat atau acara…">
            <button type="submit" class="btn btn--primary" style="height: 32px; padding: 0 14px">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px">No</th>
                    <th>Nomor Surat</th>
                    <th>Acara</th>
                    <th>Yang Diperintah</th>
                    <th style="width: 200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = $sppd->firstItem() ?? 1; @endphp
                @forelse ($sppd as $sppd_data)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td class="tabular">{{ $sppd_data->no_surat }}</td>
                        <td>{{ Str::limit($sppd_data->acara, 60, '...') }}</td>
                        <td>
                            @php $no1 = 1; @endphp
                            @foreach ($sppd_data->user as $us)
                                <div class="muted">{{ $no1++ }}. {{ Str::limit($us->nama, 30, '...') }}</div>
                            @endforeach
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap">
                                <a href="{{ route('SPPD') }}/{{ $sppd_data->id }}/SPPD" class="btn btn--primary" style="height:32px; padding:0 12px">
                                    <x-icon name="eye" :size="14" /> Lihat
                                </a>
                                <form action="{{ route('SPPD') }}/{{ $sppd_data->id }}/delete" method="post" data-confirm="Hapus SPPD &quot;{{ Str::limit($sppd_data->acara, 40) }}&quot;?">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn--danger" style="height:32px; padding:0 12px">
                                        <x-icon name="trash-2" :size="14" /> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted" style="text-align:center; padding: 40px 16px">Belum ada SPPD</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($sppd->hasPages())
        <div style="display:flex; justify-content:center; margin-top: 16px">
            {{ $sppd->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection