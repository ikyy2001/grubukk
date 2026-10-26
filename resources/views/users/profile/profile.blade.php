@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">Profil</div>
      <h1 class="page-title">Profil Saya</h1>
      <p class="page-sub muted">Informasi akun dan data pribadi Anda.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Dashboard') }}" class="btn btn--outline">Kembali</a>
      <a href="{{ route('Users/Profile/Edit') }}" class="btn btn--primary">Edit Profil</a>
    </div>
  </div>

  <section class="card card--pad-lg">
    <h3 class="section-title">Informasi Akun</h3>
    <div class="form-grid">
      <div class="form-field">
        <label for="NIP" class="form-label">NIP</label>
        <input type="number" class="input" id="NIP" readonly value="{{ $user->nip }}">
      </div>
      <div class="form-field">
        <label class="form-label">Password</label>
        <a href="{{ route('Users/Profile/ChangePassword') }}" class="btn btn--outline btn--block" style="justify-content:flex-start">Ganti Password</a>
      </div>
    </div>

    <hr class="nav-divider" style="margin: 20px 0">

    <h3 class="section-title">Data Pribadi</h3>
    <div class="form-grid">
      <div class="form-field form-grid--full">
        <label for="Nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="input" id="Nama" readonly value="{{ $user->nama }}">
      </div>
      <div class="form-field form-grid--full">
        <label for="Alamat" class="form-label">Alamat Lengkap</label>
        <textarea class="textarea" id="Alamat" readonly>{{ $user->alamat }}</textarea>
      </div>
      <div class="form-field">
        <label for="TglLahir" class="form-label">Tanggal Lahir</label>
        <input type="date" class="input" id="TglLahir" readonly value="{{ $user->tanggal_lahir }}">
      </div>
      <div class="form-field">
        <label for="Golongan" class="form-label">Golongan</label>
        <input type="text" class="input" id="Golongan" readonly value="{{ $user->golongan ? $user->golongan->golongan : '-' }}">
      </div>
      <div class="form-field">
        <label for="Eselon" class="form-label">Eselon</label>
        <input type="text" class="input" id="Eselon" readonly value="{{ $user->eselon ? $user->eselon->nama_eselon : '-' }}">
      </div>
      <div class="form-field">
        <label for="Jabatan" class="form-label">Jabatan</label>
        <input type="text" class="input" id="Jabatan" readonly value="{{ $user->jabatan ? $user->jabatan->jabatan : '-' }}">
      </div>
      <div class="form-field">
        <label for="Role" class="form-label">Sebagai</label>
        <input type="text" class="input" id="Role" readonly value="{{ $user->role }}">
      </div>
    </div>

    @if (Auth::user()->cek == 1)
      <div class="form-actions form-actions--between">
        <div></div>
        <form action="/Users/Profile/Delete" method="post" data-confirm="Anda yakin ingin menghapus {{ $user->nama }} (NIP: {{ $user->nip }})?">
          @method('delete')
          @csrf
          <button type="submit" class="btn btn--danger">Hapus User</button>
        </form>
      </div>
    @endif
  </section>
@endsection