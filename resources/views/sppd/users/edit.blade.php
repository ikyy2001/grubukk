@extends('layouts.app')

@section('title', 'Edit & Perbaiki SPPD')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Edit Surat Perintah Perjalanan Dinas</h1>
        <p class="page-subtitle">Perbaiki data pengajuan SPPD No. {{ $sppd->no_surat }}/{{ $sppd->tahun_surat }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali
        </a>
    </div>
</div>

@if ($sppd->catatan_revisi)
    <div class="alert alert--neg" style="margin-bottom:20px; align-items:flex-start">
        <x-icon name="circle-alert" :size="20" style="margin-top:2px" />
        <div>
            <strong style="display:block; margin-bottom:4px">Catatan Revisi / Penolakan dari Atasan:</strong>
            <p style="margin:0; font-size:14px">{{ $sppd->catatan_revisi }}</p>
            <span class="muted" style="font-size:12px; margin-top:6px; display:inline-block">Silakan perbaiki isian di bawah ini lalu klik "Simpan &amp; Ajukan Ulang".</span>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('UpdateSPPD', $sppd->id) }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="card card--pad-lg" style="margin-bottom:16px">
        <div class="card-head">
            <div>
                <h2 class="card-title">Pejabat yang Berwenang</h2>
                <p class="card-note">Pilih pegawai yang diperintah dan pejabat pemberi perintah</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Yang Diperintah (Pegawai Utama)</label>
                <select name="Pegawai_id" class="select" required>
                    @foreach ($pegawai as $peg)
                        <option value="{{ $peg->id }}" {{ (old('Pegawai_id', $primaryUserId) == $peg->id) ? 'selected' : '' }}>
                            {{ $peg->nip }} - {{ $peg->nama }} ({{ $peg->role }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Pejabat Pemberi Perintah (Kepala Bidang)</label>
                <select name="Kabid_id" class="select" required>
                    @foreach ($kabids as $kb)
                        @php
                            $isKabidSelected = ($sppd->kabid && ($sppd->kabid->nip == $kb->nip || $sppd->kabid->nama_kabid == $kb->nama));
                        @endphp
                        <option value="{{ $kb->id }}" {{ $isKabidSelected ? 'selected' : '' }}>
                            {{ $kb->nip }} - {{ $kb->nama }} ({{ $kb->jabatan->jabatan ?? $kb->role }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card card--pad-lg" style="margin-bottom:16px">
        <div class="card-head">
            <div>
                <h2 class="card-title">Maksud Perjalanan Dinas</h2>
                <p class="card-note">Detail acara dan lokasi</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Acara (Maksud Perjalanan Dinas)</label>
                <input type="text" class="input" name="Acara" value="{{ old('Acara', $sppd->acara) }}" placeholder="Ketikkan maksud perjalanan dinas..." required>
            </div>
            <div class="form-row">
                <label class="form-label">Tempat Berangkat</label>
                <select name="TempatBerangkat" class="select" required>
                    @php
                        $no = 1;
                        $currentBerangkat = old('TempatBerangkat', $sppd->tempat?->tempat_berangkat);
                    @endphp
                    <option disabled>========== Dalam Daerah ==========</option>
                    @foreach ($tempatb as $tempat)
                        @if ($no == 32)
                            <option disabled>========== Luar Daerah ==========</option>
                        @endif
                        <option value="{{ $tempat }}" {{ ($currentBerangkat == $tempat || ($no == 1 && !$currentBerangkat)) ? 'selected' : '' }}>{{ $tempat }}</option>
                        @php $no++; @endphp
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label class="form-label">Tempat Tujuan</label>
                <select name="TempatTujuan" class="select" required>
                    <option disabled {{ empty($sppd->tempat?->tempat_tujuan) ? 'selected' : '' }}>Pilih tempat tujuan...</option>
                    @php
                        $no = 1;
                        $currentTujuan = old('TempatTujuan', $sppd->tempat?->tempat_tujuan);
                    @endphp
                    <option disabled>========== Dalam Daerah ==========</option>
                    @foreach ($tempatb as $tempat)
                        @if ($no == 32)
                            <option disabled>========== Luar Daerah ==========</option>
                        @endif
                        <option value="{{ $tempat }}" {{ ($currentTujuan == $tempat) ? 'selected' : '' }}>{{ $tempat }}</option>
                        @php $no++; @endphp
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card card--pad-lg">
        <div class="card-head">
            <div>
                <h2 class="card-title">Waktu Pelaksanaan</h2>
                <p class="card-note">Tanggal berangkat dan kembali</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Tanggal Berangkat</label>
                <input type="date" class="input @error('Tanggal_Berangkat') is-invalid @enderror" name="Tanggal_Berangkat" value="{{ old('Tanggal_Berangkat', optional($sppd->tgl_pergi)->format('Y-m-d')) }}" required>
            </div>
            <div class="form-row">
                <label class="form-label">Tanggal Kembali</label>
                <input type="date" class="input @error('Tanggal_Kembali') is-invalid @enderror" name="Tanggal_Kembali" value="{{ old('Tanggal_Kembali', optional($sppd->tgl_kembali)->format('Y-m-d')) }}" required>
                @error('Tanggal_Kembali')
                    <div style="color:var(--neg-text); font-size:12px; margin-top:4px">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card-head" style="margin-top:24px">
            <div>
                <h2 class="card-title">Dokumen Pendukung &amp; Estimasi Biaya</h2>
                <p class="card-note">Dokumen TOR/Undangan dan estimasi uang muka</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Dokumen Pendukung (TOR / Undangan)</label>
                <input type="file" class="input" name="file_undangan" accept=".pdf,.jpg,.jpeg,.png">
                <span class="muted" style="font-size:12px">Format didukung: PDF, JPG, PNG (Maks. 5 MB). Kosongkan jika tidak ingin mengganti.</span>
                @if ($sppd->file_undangan)
                    <div style="margin-top:6px">
                        <a href="{{ asset('storage/' . $sppd->file_undangan) }}" target="_blank" class="btn btn--outline btn--sm" style="font-size:11px; height:24px">
                            <x-icon name="file-text" :size="12" /> Berkas Saat Ini
                        </a>
                    </div>
                @endif
            </div>
            <div class="form-row">
                <label class="form-label">Estimasi Uang Muka / Panjar (Rp)</label>
                <input type="number" class="input" name="uang_muka" placeholder="Contoh: 1500000" min="0" value="{{ old('uang_muka', (int)($sppd->uang_muka ?? 0)) }}">
                <span class="muted" style="font-size:12px">Akan diverifikasi dan disetujui bagian Keuangan</span>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px">
            <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">Batal</a>
            <button type="submit" class="btn btn--primary">
                <x-icon name="check" :size="16" /> {{ $sppd->status == 'rejected' ? 'Simpan & Ajukan Ulang ke Atasan' : 'Simpan Perubahan' }}
            </button>
        </div>
    </div>
</form>
@endsection