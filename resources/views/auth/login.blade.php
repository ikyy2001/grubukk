@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<form method="POST" action="{{ route('login') }}" autocomplete="on">
  @csrf
  <div class="form-field" style="margin-bottom:16px">
    <label class="form-label" for="nip">NIP</label>
    <select id="nip" name="nip" class="select" required>
      <option value="" disabled selected>Pilih NIP / Nama Pegawai</option>
      @foreach ($values as $value)
        <option value="{{ $value->nip }}" {{ old('nip') == $value->nip ? 'selected' : '' }}>{{ $value->nip }} — {{ $value->nama }}</option>
      @endforeach
    </select>
    @error('nip')
      <span class="invalid-feedback">{{ $message }}</span>
    @enderror
  </div>

  <div class="form-field" style="margin-bottom:20px">
    <label class="form-label" for="password">Kata Sandi</label>
    <input id="password" type="password" name="password" class="input" placeholder="Masukkan kata sandi" required autocomplete="current-password">
    @error('password')
      <span class="invalid-feedback">{{ $message }}</span>
    @enderror
  </div>

  <button type="submit" class="btn btn--primary btn--block">
    <x-icon name="log-out" :size="16" /> Masuk
  </button>
</form>

<div class="form-actions--between" style="margin-top:16px; align-items:center">
  <a href="{{ route('register') }}" class="btn btn--ghost btn--sm"><x-icon name="user-plus" :size="16" /> Daftar Akun Baru</a>
  <a href="javascript:alert('Silakan hubungi Administrator untuk mereset kata sandi Anda.')" class="form-hint">Lupa kata sandi?</a>
</div>
@endsection
