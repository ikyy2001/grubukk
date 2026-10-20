@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Ganti Password</h1>
      <p class="page-sub muted">Ubah password untuk {{ $user_pegawai->nama }}.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Admin/Show') }}/{{ $user_pegawai->nip }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  @if (session('Failed'))
    <div class="alert alert--neg">
      <x-icon name="circle-alert" :size="18" />
      <span>{{ session('Failed') }}</span>
    </div>
  @endif

  <section class="card card--pad-lg" style="max-width: 600px">
    <form method="POST" action="/Admin/Show/{{ $user_pegawai->nip }}/ChangePassword">
      @method('patch')
      @csrf
      <div class="form-grid form-grid--full">
        <div class="form-field">
          <label for="password" class="form-label">Password Baru</label>
          <input id="password" type="password" class="input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Masukkan password baru">
          @error('password')
            <span class="invalid-feedback">{!! $message !!}</span>
          @enderror
        </div>
        <div class="form-field">
          <label for="password-confirm" class="form-label">Konfirmasi Password</label>
          <input id="password-confirm" type="password" class="input @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru">
        </div>
      </div>
      <p class="form-hint">Minimal 5 Karakter</p>
      <div class="form-actions">
        <a href="{{ route('Admin/Show') }}/{{ $user_pegawai->nip }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Ganti Password</button>
      </div>
    </form>
  </section>
@endsection