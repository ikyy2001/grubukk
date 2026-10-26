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
        <form action="{{ route('SPPD') }}" method="GET" class="search-pill search-pill--action" role="search">
            <x-icon name="search" :size="16" />
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat atau acara…" aria-label="Cari nomor surat atau acara">
            @if (request()->filled('search'))
                <a href="{{ route('SPPD') }}" class="search-pill__clear" title="Hapus pencarian" aria-label="Hapus pencarian">
                    <x-icon name="x" :size="14" />
                </a>
            @endif
            <button type="submit" class="btn btn--primary search-pill__submit">Cari</button>
        </form>
    </div>

    @if (request()->filled('search'))
        <div class="search-result">
            <span>
                Menampilkan <strong>{{ $sppd->total() }}</strong> hasil untuk
                <strong>&ldquo;{{ request('search') }}&rdquo;</strong>
            </span>
            <a href="{{ route('SPPD') }}">Reset pencarian</a>
        </div>
    @endif

    <div class="table-wrap">
        <table class="table table--sppd">
            <thead>
                <tr>
                    <th style="width: 60px">No</th>
                    <th>Nomor Surat</th>
                    <th>Acara</th>
                    <th>Yang Diperintah</th>
                    <th>Status</th>
                    <th style="width: 260px">Aksi</th>
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
                            <div class="table-actions">
                                <a href="{{ route('SPPD') }}/{{ $sppd_data->id }}/SPPD" class="btn btn--outline" style="height:30px; padding:0 10px; font-size:12px" title="Lihat detail SPPD">
                                    <x-icon name="eye" :size="13" /> Detail
                                </a>
                                @if(in_array($sppd_data->status, ['approved', 'completed']))
                                <a href="{{ url('Reimbursement/'.$sppd_data->id) }}" class="btn btn--primary" style="height:30px; padding:0 10px; font-size:12px" title="Laporkan biaya riil">
                                    <x-icon name="dollar-sign" :size="13" /> Biaya Riil
                                </a>
                                @endif
                                <form action="{{ route('SPPD') }}/{{ $sppd_data->id }}/delete" method="post" data-confirm="Hapus SPPD &quot;{{ Str::limit($sppd_data->acara, 40) }}&quot;?">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn--danger" style="height:30px; padding:0 8px; font-size:12px" title="Hapus SPPD" aria-label="Hapus SPPD {{ Str::limit($sppd_data->acara, 40) }}">
                                        <x-icon name="trash-2" :size="13" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="text-align:center; padding: 40px 16px">
                            @if (request()->filled('search'))
                                Tidak ada SPPD yang cocok dengan pencarian &ldquo;{{ request('search') }}&rdquo;.
                            @else
                                Belum ada SPPD
                            @endif
                        </td>
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