@extends('layouts.app')
@section('title', 'Daftar Akun Baru')

@section('content')
<form method="POST" action="{{ route('register') }}" autocomplete="on">
  @csrf

  <div class="form-grid">
    <div class="form-field">
      <label class="form-label" for="nama">Nama Lengkap <span style="color:var(--neg-text)">*</span></label>
      <input id="nama" type="text" name="nama" class="input" value="{{ old('nama') }}" required>
    </div>
    <div class="form-field">
      <label class="form-label" for="nip">NIP <span style="color:var(--neg-text)">*</span></label>
      <input id="nip" type="text" name="nip" class="input" inputmode="numeric" minlength="3" maxlength="20" value="{{ old('nip') }}" required>
    </div>
    <div class="form-field">
      <label class="form-label" for="password">Kata Sandi <span style="color:var(--neg-text)">*</span></label>
      <input id="password" type="password" name="password" class="input" minlength="4" required>
      <span class="form-hint">Minimal 4 karakter</span>
    </div>
    <div class="form-field">
      <label class="form-label" for="role">Peran</label>
      <select id="role" name="role" class="select">
        @foreach (['Staff', 'Kepala Seksi'] as $r)
          <option value="{{ $r }}" {{ old('role', 'Staff') == $r ? 'selected' : '' }}>{{ $r }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-field">
      <label class="form-label" for="jabatan">Jabatan</label>
      <input id="jabatan" type="text" name="jabatan" class="input" value="{{ old('jabatan') }}">
    </div>
    <div class="form-field">
      <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
      <input id="tanggal_lahir" type="date" name="tanggal_lahir" class="input" value="{{ old('tanggal_lahir') }}">
    </div>
    <div class="form-field">
      <label class="form-label" for="golongan">Golongan</label>
      <select id="golongan" name="golongan" class="select">
        <option value="" selected>- Tidak Diisi -</option>
        @foreach ($golongan as $idx => $g)
          <option value="{{ $idx }}" {{ old('golongan') == $idx ? 'selected' : '' }}>{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-field">
      <label class="form-label" for="eselon">Eselon</label>
      <select id="eselon" name="eselon" class="select">
        <option value="" selected>- Tidak Diisi -</option>
        @foreach ($eselon as $idx => $e)
          <option value="{{ $idx }}" {{ old('eselon') == $idx ? 'selected' : '' }}>{{ $e }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-field form-grid--full">
      <label class="form-label" for="alamat">Alamat</label>
      <textarea id="alamat" name="alamat" class="textarea" rows="3">{{ old('alamat') }}</textarea>
    </div>
  </div>

  <div class="form-actions">
    <a href="{{ route('login') }}" class="btn btn--outline">Sudah punya akun?</a>
    <button type="submit" class="btn btn--primary">
      <x-icon name="user-plus" :size="16" /> Daftar & Buat Akun
    </button>
  </div>
</form>
@endsection
