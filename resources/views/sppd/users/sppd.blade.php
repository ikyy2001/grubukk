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
                    <th>Status</th>
                    <th style="width: 220px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = $sppd->firstItem() ?? 1; @endphp
                @forelse ($sppd as $sppd_data)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td class="tabular">{{ $sppd_data->no_surat }} / {{ $sppd_data->tahun_surat }}</td>
                        <td>
                            <div><strong>{{ Str::limit($sppd_data->acara, 50, '...') }}</strong></div>
                            @if ($sppd_data->catatan_revisi && in_array($sppd_data->status, ['rejected', 'draft']))
                                <div style="color:var(--neg-text); font-size:12px; margin-top:2px">Catatan: {{ $sppd_data->catatan_revisi }}</div>
                            @endif
                        </td>
                        <td>
                            @php $no1 = 1; @endphp
                            @foreach ($sppd_data->user as $us)
                                <div class="muted">{{ $no1++ }}. {{ Str::limit($us->nama, 30, '...') }}</div>
                            @endforeach
                        </td>
                        <td>
                            @if ($sppd_data->status == 'pending_atasan')
                                <span class="tag" style="background:rgba(234, 179, 8, 0.15); color:#ca8a04; border:1px solid rgba(234, 179, 8, 0.3)">Menunggu Atasan</span>
                            @elseif ($sppd_data->status == 'pending_keuangan')
                                <span class="tag" style="background:rgba(59, 130, 246, 0.15); color:#2563eb; border:1px solid rgba(59, 130, 246, 0.3)">Menunggu Keuangan</span>
                            @elseif ($sppd_data->status == 'approved')
                                <span class="tag" style="background:rgba(34, 197, 94, 0.15); color:#16a34a; border:1px solid rgba(34, 197, 94, 0.3)">Disetujui</span>
                            @elseif ($sppd_data->status == 'rejected')
                                <span class="tag" style="background:rgba(239, 68, 68, 0.15); color:#dc2626; border:1px solid rgba(239, 68, 68, 0.3)">Ditolak</span>
                            @elseif ($sppd_data->status == 'completed')
                                <span class="tag" style="background:rgba(99, 102, 241, 0.15); color:#4f46e5; border:1px solid rgba(99, 102, 241, 0.3)">Selesai</span>
                            @else
                                <span class="tag">{{ $sppd_data->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap">
                                <a href="{{ route('SPPD') }}/{{ $sppd_data->id }}/SPPD" class="btn btn--outline" style="height:30px; padding:0 10px; font-size:12px">
                                    <x-icon name="eye" :size="13" /> Detail
                                </a>
                                @if(in_array($sppd_data->status, ['approved', 'completed']))
                                <a href="{{ url('Reimbursement/'.$sppd_data->id) }}" class="btn btn--primary" style="height:30px; padding:0 10px; font-size:12px">
                                    <x-icon name="dollar-sign" :size="13" /> Biaya Riil
                                </a>
                                @endif
                                <form action="{{ route('SPPD') }}/{{ $sppd_data->id }}/delete" method="post" data-confirm="Hapus SPPD &quot;{{ Str::limit($sppd_data->acara, 40) }}&quot;?">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn--danger" style="height:30px; padding:0 8px; font-size:12px">
                                        <x-icon name="trash-2" :size="13" />
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