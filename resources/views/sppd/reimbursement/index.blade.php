@extends('layouts.app')

@section('title', 'Pelaporan Biaya Riil & Reimbursement')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Pertanggungjawaban Biaya Riil</h1>
        <p class="page-subtitle">Rekap pengeluaran riil perjalanan dinas, bukti nota, dan selisih terhadap panjar yang dicairkan</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
    </div>
</div>

{{-- Alur reimbursement --}}
<div class="notice notice--info mb-gap">
    <x-icon name="info" :size="16" />
    <div>
        <strong>Alur pertanggungjawaban:</strong>
        <span class="stepper" style="margin-top:8px">
            <span class="step"><span class="step__num">1</span> Tetapkan panjar dicairkan</span>
            <span class="step-sep">&rarr;</span>
            <span class="step"><span class="step__num">2</span> Input nota pengeluaran riil</span>
            <span class="step-sep">&rarr;</span>
            <span class="step"><span class="step__num">3</span> Verifikasi keuangan</span>
            <span class="step-sep">&rarr;</span>
            <span class="step"><span class="step__num">4</span> Pencairan selisih &amp; SPPD selesai</span>
        </span>
    </div>
</div>

@php
    $totalRiil    = (float) $reimbursement->total_pengeluaran_riil;
    $uangMuka     = (float) $reimbursement->uang_muka;
    $umDitetapkan = (bool) $reimbursement->uang_muka_ditetapkan;
    $selisih      = (float) $reimbursement->selisih;
    $belumAdaNota = $totalRiil <= 0;
    $jumlahNota   = $reimbursement->items->count();

    $statusLabel = match ($reimbursement->status_pembayaran) {
        'verified' => 'Terverifikasi',
        'paid'     => 'Lunas / Selesai',
        default    => 'Menunggu Verifikasi',
    };
    $statusTagStyle = match ($reimbursement->status_pembayaran) {
        'verified' => 'background:rgba(59, 130, 246, 0.15); color:#2563eb; border:1px solid rgba(59, 130, 246, 0.3)',
        'paid'     => 'background:rgba(34, 197, 94, 0.15); color:#16a34a; border:1px solid rgba(34, 197, 94, 0.3)',
        default    => 'background:rgba(234, 179, 8, 0.15); color:#ca8a04; border:1px solid rgba(234, 179, 8, 0.3)',
    };
@endphp

{{-- Ringkasan Keuangan --}}
<div class="stat-grid stat-grid--auto mb-gap">
    {{-- Panjar / Uang Muka --}}
    <article class="stat-card">
        <div class="stat-card__label">Uang Muka Diterima</div>
        @if ($umDitetapkan)
            <div class="stat-card__value tabular">Rp {{ number_format($uangMuka, 0, ',', '.') }}</div>
            <div class="stat-card__note">Panjar yang telah dicairkan ke pegawai</div>
        @else
            <div class="stat-card__value stat-card__value--muted">Belum ditetapkan</div>
            <div class="stat-card__note">
                @if ($estimasiUangMuka > 0)
                    Estimasi pengajuan: Rp {{ number_format($estimasiUangMuka, 0, ',', '.') }}
                @else
                    Isi nilai panjar pada panel di samping
                @endif
            </div>
        @endif
    </article>

    {{-- Total Pengeluaran Riil --}}
    <article class="stat-card">
        <div class="stat-card__label">Total Pengeluaran Riil</div>
        <div class="stat-card__value tabular" style="color:var(--primary)">Rp {{ number_format($totalRiil, 0, ',', '.') }}</div>
        <div class="stat-card__note">
            @if ($belumAdaNota)
                Belum ada nota pengeluaran riil
            @else
                Akumulasi {{ $jumlahNota }} bukti nota
            @endif
        </div>
    </article>

    {{-- Selisih --}}
    <article class="stat-card">
        <div class="stat-card__label">Selisih Perhitungan</div>
        @if (!$umDitetapkan)
            <div class="stat-card__value stat-card__value--muted">&mdash;</div>
            <div class="stat-card__note">Menunggu data uang muka / panjar</div>
        @elseif ($belumAdaNota)
            <div class="stat-card__value stat-card__value--muted">&mdash;</div>
            <div class="stat-card__note">Belum dapat dihitung, belum ada pengeluaran riil</div>
        @else
            <div class="stat-card__value tabular {{ $selisih >= 0 ? 'text-pos' : 'text-neg' }}">
                Rp {{ number_format(abs($selisih), 0, ',', '.') }}
            </div>
            <div class="stat-card__note">
                @if ($selisih > 0)
                    <span class="tag tag--wrap" style="background:rgba(34,197,94,0.15); color:#16a34a; border-color:rgba(34,197,94,0.35)">Kurang Bayar</span>
                    <div style="margin-top:4px">Perlu reimburse ke pegawai</div>
                @elseif ($selisih < 0)
                    <span class="tag tag--wrap" style="background:rgba(239,68,68,0.15); color:#dc2626; border-color:rgba(239,68,68,0.35)">Lebih Bayar</span>
                    <div style="margin-top:4px">Pegawai mengembalikan kelebihan dana</div>
                @else
                    <span class="tag tag--wrap">Sesuai anggaran</span>
                @endif
            </div>
        @endif
    </article>

    {{-- Status Verifikasi --}}
    <article class="stat-card">
        <div class="stat-card__label">Status Verifikasi</div>
        <div class="stat-card__value stat-card__value--status">
            <span class="tag tag--wrap" style="{{ $statusTagStyle }}">{{ $statusLabel }}</span>
        </div>
        <div class="stat-card__note">
            @if ($reimbursement->catatan_keuangan)
                {{ $reimbursement->catatan_keuangan }}
            @else
                Verifikasi oleh tim keuangan, belum ada catatan
            @endif
        </div>
    </article>
</div>

<div class="split">
    {{-- Tabel Rincian Nota Biaya --}}
    <div class="card card--pad-lg">
        <div class="card-head">
            <div>
                <h2 class="card-title">Rincian Nota &amp; Pengeluaran Riil</h2>
                <p class="card-note">Daftar biaya riil yang dikeluarkan selama dinas beserta bukti fisik nota</p>
            </div>
            @if ($jumlahNota > 0)
                <span class="tag">{{ $jumlahNota }} nota</span>
            @endif
        </div>

        <div class="table-wrap">
            <table class="table table--min">
                <thead>
                    <tr>
                        <th style="width:44px">No</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th style="width:150px">Biaya (Rp)</th>
                        <th style="width:130px">Bukti Nota</th>
                        <th style="width:70px" class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reimbursement->items as $idx => $item)
                        <tr>
                            <td class="num">{{ $idx + 1 }}</td>
                            <td><span class="tag">{{ $item->kategori }}</span></td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="tabular"><strong>Rp {{ number_format($item->biaya, 0, ',', '.') }}</strong></td>
                            <td>
                                @if ($item->file_nota)
                                    <a href="{{ asset('storage/' . $item->file_nota) }}" target="_blank" class="btn btn--outline btn--sm" style="font-size:11px; height:26px; padding:0 10px">
                                        <x-icon name="image" :size="12" /> Lihat
                                    </a>
                                @else
                                    <span class="muted text-sm">Tanpa nota</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($bolehEditItem)
                                    <form action="{{ route('Reimbursement.deleteItem', $item->id) }}" method="POST" data-confirm="Hapus item biaya ini?">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn--danger" style="height:28px; width:28px; padding:0">
                                            <x-icon name="trash-2" :size="13" />
                                        </button>
                                    </form>
                                @else
                                    <span class="muted text-sm">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted" style="text-align:center; padding:32px 16px">Belum ada rincian pengeluaran riil. Tambahkan nota melalui form di samping.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($jumlahNota > 0)
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">Total Pengeluaran Riil</td>
                            <td class="tabular">Rp {{ number_format($totalRiil, 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Panel samping --}}
    <div class="stack">
        {{-- Uang Muka / Panjang Diterima --}}
        <div class="card card--pad-lg">
            <div class="card-head">
                <div>
                    <h3 class="card-title">Uang Muka / Panjar Diterima</h3>
                    <p class="card-note">Nilai panjar yang benar-benar dicairkan ke pegawai</p>
                </div>
            </div>

            @if ($bolehAturUangMuka)
                <form action="{{ route('Reimbursement.setUangMuka', $sppd->id) }}" method="POST">
                    @csrf
                    <div class="form-field" style="margin-bottom:12px">
                        <label class="form-label" for="uang_muka">Nominal Panjar Dicairkan (Rp)</label>
                        <input type="number" id="uang_muka" name="uang_muka" class="input" placeholder="0" min="0" step="1"
                               value="{{ $umDitetapkan ? (int) $uangMuka : '' }}">
                        @error('uang_muka')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if ($estimasiUangMuka > 0)
                        <div class="muted text-sm" style="margin-bottom:12px">
                            Estimasi pada pengajuan: Rp {{ number_format($estimasiUangMuka, 0, ',', '.') }}
                        </div>
                    @endif
                    <button type="submit" class="btn btn--primary" style="width:100%">
                        <x-icon name="wallet" :size="14" /> {{ $umDitetapkan ? 'Perbarui Panjar' : 'Simpan Panjar' }}
                    </button>
                </form>
            @else
                <div class="notice {{ $umDitetapkan ? '' : 'notice--warn' }}">
                    <x-icon name="wallet" :size="16" />
                    <div>
                        @if ($umDitetapkan)
                            Panjar dicairkan <strong>Rp {{ number_format($uangMuka, 0, ',', '.') }}</strong>
                        @else
                            Panjar dicairkan <strong>belum ditetapkan</strong>.
                        @endif
                        @if ($pesanKunciPanjar)
                            <div style="margin-top:4px">{{ $pesanKunciPanjar }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Form Input Item --}}
        @if ($bolehEditItem)
            <div class="card card--pad-lg">
                <div class="card-head">
                    <h3 class="card-title">Tambah Nota Pengeluaran</h3>
                </div>
                <form action="{{ route('Reimbursement.addItem', $sppd->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-field" style="margin-bottom:12px">
                        <label class="form-label" for="kategori">Kategori Biaya</label>
                        <select id="kategori" name="kategori" class="select" required>
                            <option value="Transportasi">Tiket / Transportasi</option>
                            <option value="Penginapan">Penginapan / Hotel</option>
                            <option value="Uang Saku">Uang Saku / Harian</option>
                            <option value="Bahan Bakar / Tol">BBM / Tol / Parkir</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom:12px">
                        <label class="form-label" for="keterangan">Keterangan Pengeluaran</label>
                        <input type="text" id="keterangan" name="keterangan" class="input" placeholder="Contoh: Tiket Kereta Api PP" required>
                    </div>

                    <div class="form-field" style="margin-bottom:12px">
                        <label class="form-label" for="biaya">Nominal Riil (Rp)</label>
                        <input type="number" id="biaya" name="biaya" class="input" placeholder="0" min="0" step="1" required>
                    </div>

                    <div class="form-field" style="margin-bottom:16px">
                        <label class="form-label" for="file_nota">Foto / Scan Bukti Nota</label>
                        <input type="file" id="file_nota" name="file_nota" class="input" accept=".pdf,.jpg,.jpeg,.png">
                        <span class="muted text-sm">PDF, JPG, PNG (Maks 5 MB)</span>
                    </div>

                    <button type="submit" class="btn btn--primary" style="width:100%">
                        <x-icon name="plus" :size="14" /> Simpan Pengeluaran
                    </button>
                </form>
            </div>
        @else
            <div class="notice notice--warn">
                <x-icon name="alert-circle" :size="16" />
                <div>{{ $pesanKunciItem }}</div>
            </div>
        @endif

        {{-- Panel Verifikasi Bagian Keuangan --}}
        @if (in_array($user->role, ['Keuangan', 'Admin']))
            <div class="card card--pad-lg" style="border-color:rgba(59, 130, 246, 0.4); background:rgba(59, 130, 246, 0.02)">
                <div class="card-head">
                    <h3 class="card-title">Verifikasi Bagian Keuangan</h3>
                </div>
                <form action="{{ route('Reimbursement.verify', $sppd->id) }}" method="POST">
                    @csrf
                    <div class="form-field" style="margin-bottom:12px">
                        <label class="form-label" for="status_pembayaran">Status Penyelesaian Dana</label>
                        <select id="status_pembayaran" name="status_pembayaran" class="select" required>
                            <option value="pending" {{ $reimbursement->status_pembayaran == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="verified" {{ $reimbursement->status_pembayaran == 'verified' ? 'selected' : '' }}>Verifikasi Nota Sah</option>
                            <option value="paid" {{ $reimbursement->status_pembayaran == 'paid' ? 'selected' : '' }}>Lunas / Pencairan Selesai</option>
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom:16px">
                        <label class="form-label" for="catatan_keuangan">Catatan Tim Keuangan</label>
                        <textarea id="catatan_keuangan" name="catatan_keuangan" class="textarea" style="min-height:70px" placeholder="Contoh: Bukti fisik nota telah dicocokkan dan dana selisih ditransfer...">{{ $reimbursement->catatan_keuangan }}</textarea>
                    </div>

                    <button type="submit" class="btn btn--primary" style="width:100%; background:#16a34a; border-color:#16a34a">
                        <x-icon name="check-circle" :size="14" /> Simpan Verifikasi
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
