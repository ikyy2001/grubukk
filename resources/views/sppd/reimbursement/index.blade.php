@extends('layouts.app')

@section('title', 'Pelaporan Biaya Riil & Reimbursement')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Pertanggungjawaban Biaya Riil (Reimbursement)</h1>
        <p class="page-subtitle">Input rincian pengeluaran riil perjalanan dinas dan unggah bukti nota transaksi</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
    </div>
</div>

{{-- Ringkasan Keuangan --}}
<div class="stat-grid" style="margin-bottom:20px">
    <article class="stat-card">
        <div class="stat-card__label">Uang Muka / Panjar Diterima</div>
        <div class="stat-card__value tabular">Rp {{ number_format($reimbursement->uang_muka, 0, ',', '.') }}</div>
        <div class="stat-card__note">Pencairan di awal kegiatan</div>
    </article>

    <article class="stat-card">
        <div class="stat-card__label">Total Pengeluaran Riil</div>
        <div class="stat-card__value tabular" style="color:var(--primary)">Rp {{ number_format($reimbursement->total_pengeluaran_riil, 0, ',', '.') }}</div>
        <div class="stat-card__note">Akumulasi seluruh bukti nota</div>
    </article>

    <article class="stat-card">
        <div class="stat-card__label">Selisih Perhitungan</div>
        @php
            $selisih = $reimbursement->selisih;
        @endphp
        <div class="stat-card__value tabular" style="color: {{ $selisih >= 0 ? 'var(--pos-text, #16a34a)' : 'var(--neg-text, #dc2626)' }}">
            Rp {{ number_format(abs($selisih), 0, ',', '.') }}
        </div>
        <div class="stat-card__note">
            @if ($selisih > 0)
                <span class="tag" style="background:rgba(34,197,94,0.15); color:#16a34a">Kurang Bayar (Reimburse ke Pegawai)</span>
            @elseif ($selisih < 0)
                <span class="tag" style="background:rgba(239,68,68,0.15); color:#dc2626">Lebih Bayar (Pegawai Mengembalikan)</span>
            @else
                <span class="tag">Sesuai Anggaran</span>
            @endif
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-card__label">Status Verifikasi Keuangan</div>
        <div style="margin-top:8px">
            @if ($reimbursement->status_pembayaran == 'pending')
                <span class="tag" style="background:rgba(234, 179, 8, 0.15); color:#ca8a04; border:1px solid rgba(234, 179, 8, 0.3)">Menunggu Verifikasi</span>
            @elseif ($reimbursement->status_pembayaran == 'verified')
                <span class="tag" style="background:rgba(59, 130, 246, 0.15); color:#2563eb; border:1px solid rgba(59, 130, 246, 0.3)">Terverifikasi</span>
            @elseif ($reimbursement->status_pembayaran == 'paid')
                <span class="tag" style="background:rgba(34, 197, 94, 0.15); color:#16a34a; border:1px solid rgba(34, 197, 94, 0.3)">Lunas / Selesai</span>
            @endif
        </div>
        @if ($reimbursement->catatan_keuangan)
            <div class="stat-card__note" style="margin-top:4px">{{ $reimbursement->catatan_keuangan }}</div>
        @endif
    </article>
</div>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:20px; align-items:start">
    {{-- Tabel Rincian Nota Biaya --}}
    <div class="card card--pad-lg">
        <div class="card-head">
            <div>
                <h2 class="card-title">Rincian Nota & Pengeluaran Riil</h2>
                <p class="card-note">Daftar biaya riil yang dikeluarkan selama dinas beserta bukti fisik nota</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Biaya (Rp)</th>
                        <th>Bukti Nota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reimbursement->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><span class="tag">{{ $item->kategori }}</span></td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="tabular"><strong>Rp {{ number_format($item->biaya, 0, ',', '.') }}</strong></td>
                            <td>
                                @if ($item->file_nota)
                                    <a href="{{ asset('storage/' . $item->file_nota) }}" target="_blank" class="btn btn--outline btn--sm" style="font-size:11px; height:24px; padding:0 8px">
                                        <x-icon name="image" :size="12" /> Lihat Bukti
                                    </a>
                                @else
                                    <span class="muted" style="font-size:11px">Tanpa Nota</span>
                                @endif
                            </td>
                            <td>
                                @if ($reimbursement->status_pembayaran != 'paid')
                                    <form action="{{ route('Reimbursement.deleteItem', $item->id) }}" method="POST" data-confirm="Hapus item biaya ini?">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn--danger" style="height:26px; padding:0 8px; font-size:11px">
                                            <x-icon name="trash-2" :size="12" />
                                        </button>
                                    </form>
                                @else
                                    <span class="muted" style="font-size:11px">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted" style="text-align:center; padding:32px 16px">Belum ada rincian pengeluaran riil. Tambahkan nota melalui form di samping.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Tambah Pengeluaran & Panel Verifikasi Keuangan --}}
    <div style="display:flex; flex-direction:column; gap:16px">
        {{-- Form Input Item --}}
        @if ($reimbursement->status_pembayaran != 'paid')
            <div class="card card--pad-lg">
                <div class="card-head">
                    <h3 class="card-title" style="font-size:16px">Tambah Nota Pengeluaran</h3>
                </div>
                <form action="{{ route('Reimbursement.addItem', $sppd->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row" style="margin-bottom:12px">
                        <label class="form-label">Kategori Biaya</label>
                        <select name="kategori" class="select" required>
                            <option value="Transportasi">Tiket / Transportasi</option>
                            <option value="Penginapan">Penginapan / Hotel</option>
                            <option value="Uang Saku">Uang Saku / Harian</option>
                            <option value="Bahan Bakar / Tol">BBM / Tol / Parkir</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                    </div>

                    <div class="form-row" style="margin-bottom:12px">
                        <label class="form-label">Keterangan Pengeluaran</label>
                        <input type="text" name="keterangan" class="input" placeholder="Contoh: Tiket Kereta Api PP" required>
                    </div>

                    <div class="form-row" style="margin-bottom:12px">
                        <label class="form-label">Nominal Riil (Rp)</label>
                        <input type="number" name="biaya" class="input" placeholder="0" min="0" required>
                    </div>

                    <div class="form-row" style="margin-bottom:16px">
                        <label class="form-label">Foto / Scan Bukti Nota</label>
                        <input type="file" name="file_nota" class="input" accept=".pdf,.jpg,.jpeg,.png">
                        <span class="muted" style="font-size:11px">PDF, JPG, PNG (Maks 5 MB)</span>
                    </div>

                    <button type="submit" class="btn btn--primary" style="width:100%; justify-content:center">
                        <x-icon name="plus" :size="14" /> Simpan Pengeluaran
                    </button>
                </form>
            </div>
        @endif

        {{-- Panel Verifikasi Bagian Keuangan --}}
        @if (in_array($user->role, ['Keuangan', 'Admin']))
            <div class="card card--pad-lg" style="border:1px solid rgba(59, 130, 246, 0.4); background:rgba(59, 130, 246, 0.02)">
                <div class="card-head">
                    <h3 class="card-title" style="font-size:16px">Verifikasi Bagian Keuangan</h3>
                </div>
                <form action="{{ route('Reimbursement.verify', $sppd->id) }}" method="POST">
                    @csrf
                    <div class="form-row" style="margin-bottom:12px">
                        <label class="form-label">Status Penyelesaian Dana</label>
                        <select name="status_pembayaran" class="select" required>
                            <option value="pending" {{ $reimbursement->status_pembayaran == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="verified" {{ $reimbursement->status_pembayaran == 'verified' ? 'selected' : '' }}>Verifikasi Nota Sah</option>
                            <option value="paid" {{ $reimbursement->status_pembayaran == 'paid' ? 'selected' : '' }}>Lunas / Pencairan Selesai</option>
                        </select>
                    </div>

                    <div class="form-row" style="margin-bottom:16px">
                        <label class="form-label">Catatan Tim Keuangan</label>
                        <textarea name="catatan_keuangan" class="input" style="height:60px" placeholder="Contoh: Bukti fisik nota telah dicocokkan dan dana selisih ditransfer...">{{ $reimbursement->catatan_keuangan }}</textarea>
                    </div>

                    <button type="submit" class="btn btn--primary" style="width:100%; justify-content:center; background:#16a34a; border-color:#16a34a">
                        <x-icon name="check-circle" :size="14" /> Simpan Verifikasi
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
