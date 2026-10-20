@extends('layouts.app')

@section('title', 'Persetujuan SPPD')

@section('content')
@php
    use Illuminate\Support\Str;
    $currentTab = request('tab', 'all');
@endphp

<div class="page-head">
    <div>
        <h1 class="page-title">Persetujuan Perjalanan Dinas</h1>
        <p class="page-subtitle">Verifikasi dan setujui pengajuan SPPD berjenjang (Atasan & Keuangan)</p>
    </div>
</div>

<div class="card card--pad-lg">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px">
        <div style="display:flex; gap:8px">
            <a href="{{ route('Approval') }}" class="btn {{ $currentTab == 'all' ? 'btn--primary' : 'btn--outline' }}" style="height:32px; padding:0 12px; font-size:13px">Semua</a>
            <a href="{{ route('Approval') }}?tab=pending" class="btn {{ $currentTab == 'pending' ? 'btn--primary' : 'btn--outline' }}" style="height:32px; padding:0 12px; font-size:13px">Menunggu Persetujuan</a>
            <a href="{{ route('Approval') }}?tab=approved" class="btn {{ $currentTab == 'approved' ? 'btn--primary' : 'btn--outline' }}" style="height:32px; padding:0 12px; font-size:13px">Disetujui</a>
            <a href="{{ route('Approval') }}?tab=rejected" class="btn {{ $currentTab == 'rejected' ? 'btn--primary' : 'btn--outline' }}" style="height:32px; padding:0 12px; font-size:13px">Ditolak</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th>No. Surat / Tanggal</th>
                    <th>Acara & Dokumen</th>
                    <th>Pegawai & Uang Muka</th>
                    <th>Status Saat Ini</th>
                    <th style="width: 220px">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @php $no = $sppdList->firstItem() ?? 1; @endphp
                @forelse ($sppdList as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>
                            <div class="tabular"><strong>#{{ $item->no_surat }}/{{ $item->tahun_surat }}</strong></div>
                            <div class="muted" style="font-size:12px">{{ optional($item->tgl_pergi)->format('d M') }} - {{ optional($item->tgl_kembali)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div>{{ Str::limit($item->acara, 50) }}</div>
                            <div class="muted" style="font-size:12px">Tujuan: {{ optional($item->tempat)->tempat_tujuan ?? '-' }}</div>
                            @if ($item->file_undangan)
                                <div style="margin-top:4px">
                                    <a href="{{ asset('storage/' . $item->file_undangan) }}" target="_blank" class="btn btn--outline btn--sm" style="height:24px; padding:0 8px; font-size:11px">
                                        <x-icon name="file-text" :size="12" /> Berkas TOR/Undangan
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td>
                            @foreach ($item->user as $u)
                                <div style="font-size:13px">{{ $u->nama }}</div>
                            @endforeach
                            <div class="muted" style="font-size:12px; margin-top:4px">
                                Panjar: Rp {{ number_format($item->uang_muka, 0, ',', '.') }}
                            </div>
                        </td>
                        <td>
                            @if ($item->status == 'pending_atasan')
                                <span class="tag" style="background:rgba(234, 179, 8, 0.15); color:#ca8a04; border:1px solid rgba(234, 179, 8, 0.3)">Menunggu Atasan</span>
                            @elseif ($item->status == 'pending_keuangan')
                                <span class="tag" style="background:rgba(59, 130, 246, 0.15); color:#2563eb; border:1px solid rgba(59, 130, 246, 0.3)">Menunggu Keuangan</span>
                            @elseif ($item->status == 'approved')
                                <span class="tag" style="background:rgba(34, 197, 94, 0.15); color:#16a34a; border:1px solid rgba(34, 197, 94, 0.3)">Disetujui</span>
                            @elseif ($item->status == 'rejected')
                                <span class="tag" style="background:rgba(239, 68, 68, 0.15); color:#dc2626; border:1px solid rgba(239, 68, 68, 0.3)">Ditolak</span>
                            @else
                                <span class="tag">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:6px">
                                @if ($item->status == 'pending_atasan' && in_array($user->role, ['Kepala Bidang', 'Admin']))
                                    <form action="{{ route('Approval.atasan', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn--primary" style="height:28px; width:100%; font-size:12px; justify-content:center">
                                            <x-icon name="check" :size="13" /> Setujui Atasan
                                        </button>
                                    </form>
                                @endif

                                @if ($item->status == 'pending_keuangan' && in_array($user->role, ['Keuangan', 'Admin']))
                                    <form action="{{ route('Approval.keuangan', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn--primary" style="height:28px; width:100%; font-size:12px; justify-content:center; background:#16a34a; border-color:#16a34a">
                                            <x-icon name="check-circle" :size="13" /> Sahkan Keuangan
                                        </button>
                                    </form>
                                @endif

                                @if (in_array($item->status, ['pending_atasan', 'pending_keuangan']) && in_array($user->role, ['Kepala Bidang', 'Keuangan', 'Admin']))
                                    <details style="position:relative">
                                        <summary class="btn btn--danger" style="height:28px; width:100%; font-size:12px; cursor:pointer; list-style:none; text-align:center">
                                            <x-icon name="x" :size="13" /> Tolak / Revisi
                                        </summary>
                                        <div style="position:absolute; right:0; top:32px; background:var(--surface); border:1px solid var(--border); padding:12px; border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:10; width:220px">
                                            <form action="{{ route('Approval.reject', $item->id) }}" method="POST">
                                                @csrf
                                                <label style="font-size:11px; display:block; margin-bottom:4px">Alasan Penolakan:</label>
                                                <textarea name="catatan_revisi" class="input" style="font-size:12px; height:60px; margin-bottom:8px" placeholder="Tulis alasan revisi..." required></textarea>
                                                <button type="submit" class="btn btn--danger" style="height:26px; width:100%; font-size:11px">Kirim Penolakan</button>
                                            </form>
                                        </div>
                                    </details>
                                @endif

                                <a href="{{ route('SPPD') }}/{{ $item->id }}/SPPD" class="btn btn--outline" style="height:28px; width:100%; font-size:12px; justify-content:center">
                                    <x-icon name="eye" :size="13" /> Detail SPPD
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="text-align:center; padding:32px 16px">Tidak ada pengajuan dalam kategori ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($sppdList->hasPages())
        <div style="display:flex; justify-content:center; margin-top: 16px">
            {{ $sppdList->links() }}
        </div>
    @endif
</div>
@endsection
