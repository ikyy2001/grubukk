@extends('layouts.app')

@section('title', 'Buat SPT / SPPD')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Buat Surat Perintah Tugas / Perjalanan Dinas</h1>
        <p class="page-subtitle">Isi data untuk membuat SPT dan SPPD baru</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('SPPD') }}" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali
        </a>
    </div>
</div>

<form method="POST" action="{{ route('EntrySPPD') }}">
    @csrf
    <div class="card card--pad-lg" style="margin-bottom:16px">
        <div class="card-head">
            <div>
                <h2 class="card-title">Pejabat yang Berwenang</h2>
                <p class="card-note">Pilih pegawai yang diperintah dan pejabat pemberi perintah</p>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Yang Diperintah</label>
                <select name="Pegawai_id" class="select" required>
                    @foreach ($pegawai as $peg)
                        <option value="{{ $peg->id }}" {{ $peg->id == ($user->id ?? null) ? 'selected' : '' }}>
                            {{ $peg->nip }} - {{ $peg->nama }} ({{ $peg->role }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-row" style="grid-column: 1 / -1">
                <label class="form-label">Pejabat Pemberi Perintah (Kepala Bidang)</label>
                <select name="Kabid_id" class="select" required>
                    @foreach ($kabids as $kb)
                        <option value="{{ $kb->id }}">
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
                <input type="text" class="input" name="Acara" value="{{ old('Acara') }}" placeholder="Ketikkan maksud perjalanan dinas..." required>
            </div>
            <div class="form-row">
                <label class="form-label">Tempat Berangkat</label>
                <select name="TempatBerangkat" class="select" required>
                    @php $no = 1; @endphp
                    <option disabled>========== Dalam Daerah ==========</option>
                    @foreach ($tempatb as $tempat)
                        @if ($no == 32)
                            <option disabled>========== Luar Daerah ==========</option>
                        @endif
                        <option value="{{ $no }}" @if ($tempat == "Surabaya") selected @endif>{{ $tempat }}</option>
                        @php $no++; @endphp
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label class="form-label">Tempat Tujuan</label>
                <select name="TempatTujuan" class="select" required>
                    <option disabled selected>Pilih tempat tujuan...</option>
                    @php $no = 1; @endphp
                    <option disabled>========== Dalam Daerah ==========</option>
                    @foreach ($tempatb as $tempat)
                        @if ($no == 32)
                            <option disabled>========== Luar Daerah ==========</option>
                        @endif
                        <option value="{{ $no }}">{{ $tempat }}</option>
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
                <input type="date" class="input @error('Tanggal_Berangkat') is-invalid @enderror" name="Tanggal_Berangkat" required>
            </div>
            <div class="form-row">
                <label class="form-label">Tanggal Kembali</label>
                <input type="date" class="input @error('Tanggal_Kembali') is-invalid @enderror" name="Tanggal_Kembali" required>
                @error('Tanggal_Kembali')
                    <div style="color:var(--neg-text); font-size:12px; margin-top:4px">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; margin-top:20px">
            <button type="submit" class="btn btn--primary">
                <x-icon name="file-plus" :size="16" /> Buat SPPD
            </button>
        </div>
    </div>
</form>
@endsection