@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Pegawai</h1>
      <p class="page-sub muted">Detail data pegawai.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Pegawai') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg">
    <h3 class="section-title">Data Pegawai</h3>
    <div class="form-grid">
      <div class="form-field">
        <label for="NIP" class="form-label">NIP</label>
        <input type="number" class="input" id="NIP" readonly value="{{ $user_pegawai->nip }}">
      </div>
      <div class="form-field form-grid--full">
        <label for="Nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="input" id="Nama" readonly value="{{ $user_pegawai->nama }}">
      </div>
      <div class="form-field form-grid--full">
        <label for="Alamat" class="form-label">Alamat Lengkap</label>
        <textarea class="textarea" id="Alamat" readonly>{{ $user_pegawai->alamat }}</textarea>
      </div>
      <div class="form-field">
        <label for="TglLahir" class="form-label">Tanggal Lahir</label>
        <input type="date" class="input" id="TglLahir" readonly value="{{ $user_pegawai->tgllahir }}">
      </div>
      <div class="form-field">
        <label for="Golongan" class="form-label">Golongan</label>
        <input type="text" class="input" id="Golongan" readonly value="{{ $user_pegawai->golongan ? $user_pegawai->golongan->golongan : '-' }}">
      </div>
      <div class="form-field">
        <label for="Eselon" class="form-label">Eselon</label>
        <input type="text" class="input" id="Eselon" readonly value="{{ $user_pegawai->eselon ? $user_pegawai->eselon->nama_eselon : '-' }}">
      </div>
      <div class="form-field">
        <label for="Jabatan" class="form-label">Jabatan</label>
        <input type="text" class="input" id="Jabatan" readonly value="{{ $user_pegawai->jabatan ? $user_pegawai->jabatan->jabatan : '-' }}">
      </div>
      <div class="form-field">
        <label for="Role" class="form-label">Sebagai</label>
        <input type="text" class="input" id="Role" readonly value="{{ $user_pegawai->role }}">
      </div>
    </div>
  </section>
@endsection