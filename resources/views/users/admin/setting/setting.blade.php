@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Pengaturan</h1>
      <p class="page-sub muted">Pengaturan sistem SPPD.</p>
    </div>
  </div>

  @if (session('Success'))
    <div class="alert alert--pos" data-autohide>
      <x-icon name="circle-check" :size="18" />
      <span>{{ session('Success') }}</span>
      <button class="icon-btn" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan"><x-icon name="x" :size="14" /></button>
    </div>
  @endif
  @if (session('Failed'))
    <div class="alert alert--neg" data-autohide>
      <x-icon name="circle-alert" :size="18" />
      <span>{{ session('Failed') }}</span>
      <button class="icon-btn" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan"><x-icon name="x" :size="14" /></button>
    </div>
  @endif

  <section class="card card--pad-lg">
    <h3 class="section-title">Pengaturan Nomor & Tahun Surat</h3>
    <div class="form-grid">
      <div class="form-field">
        <label for="NomorSurat" class="form-label">Nomor Surat</label>
        <form action="{{ route('SetNomorSurat') }}" method="post" style="display:flex; gap:8px; align-items:center">
          @method('patch')
          @csrf
          <input type="number" class="input" id="NomorSurat" name="NomorSurat" value="{{ old('NomorSurat') ?? $surat->no_surat }}">
          <button type="submit" class="btn btn--primary btn--sm">Simpan</button>
        </form>
      </div>
      <div class="form-field">
        <label for="TahunSurat" class="form-label">Tahun Surat</label>
        <form action="{{ route('SetTahunSurat') }}" method="post" style="display:flex; gap:8px; align-items:center">
          @method('patch')
          @csrf
          <input type="number" class="input" id="TahunSurat" name="TahunSurat" value="{{ old('TahunSurat') ?? $surat->tahun_surat }}" min="1900" max="3000" step="1">
          <button type="submit" class="btn btn--primary btn--sm">Simpan</button>
        </form>
      </div>
    </div>

    <hr class="nav-divider" style="margin: 20px 0">

    <h3 class="section-title">Pengaturan Bidang & Referensi</h3>
    <div class="form-grid">
      <div class="form-field">
        <label class="form-label">Dasar SPPD</label>
        <a href="{{ route('DasarSurat') }}" class="btn btn--outline btn--block" style="justify-content:flex-start">Ganti Dasar SPPD</a>
      </div>
      <div class="form-field">
        <label for="Bidang" class="form-label">Bidang</label>
        <form action="{{ route('SetBidang') }}" method="post" style="display:flex; gap:8px; align-items:center">
          @method('patch')
          @csrf
          <input type="text" class="input" id="Bidang" name="Nama_Bidang" value="{{ old('Nama_Bidang') ?? $bidang->nama_bidang }}">
          <button type="submit" class="btn btn--primary btn--sm">Simpan</button>
        </form>
      </div>
      <div class="form-field">
        <label class="form-label">SKPD</label>
        <a href="{{ route('SKPD') }}" class="btn btn--outline btn--block" style="justify-content:flex-start">Set SKPD</a>
      </div>
      <div class="form-field">
        <label class="form-label">Program</label>
        <a href="{{ route('Program') }}" class="btn btn--outline btn--block" style="justify-content:flex-start">Set Program</a>
      </div>
      <div class="form-field">
        <label class="form-label">Kegiatan</label>
        <a href="{{ route('Kegiatan') }}" class="btn btn--outline btn--block" style="justify-content:flex-start">Set Kegiatan</a>
      </div>
      <div class="form-field">
        <label class="form-label">Rekening</label>
        <a href="{{ route('Rekening') }}" class="btn btn--outline btn--block" style="justify-content:flex-start">Set Rekening</a>
      </div>
    </div>
  </section>
@endsection