@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Membuat User</h1>
      <p class="page-sub muted">Tambah pengguna baru ke dalam sistem.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Admin/Show') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg">
    <form method="POST" action="{{ route('Admin/CreateUser') }}">
      @csrf
      <h3 class="section-title">Informasi Akun</h3>
      <div class="form-grid">
        <div class="form-field">
          <label for="NIP" class="form-label">NIP <span class="text-danger">*</span></label>
          <input type="number" class="input @error('NIP') is-invalid @enderror" id="NIP" name="NIP" placeholder="Ketik NIP..." value="{{ old('NIP') }}">
          @error('NIP')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-field">
          <label for="Password" class="form-label">Password <span class="text-danger">*</span></label>
          <input type="password" class="input @error('Password') is-invalid @enderror" id="Password" name="Password" placeholder="Ketik Password..." autocomplete="new-password">
          @error('Password')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <hr class="nav-divider" style="margin: 20px 0">

      <h3 class="section-title">Data Pegawai</h3>
      <div class="form-grid">
        <div class="form-field form-grid--full">
          <label for="Nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" class="input @error('Nama') is-invalid @enderror" id="Nama" name="Nama" placeholder="Ketik Nama Lengkap (dengan gelar)..." value="{{ old('Nama') }}">
          @error('Nama')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-field form-grid--full">
          <label for="Alamat" class="form-label">Alamat Lengkap</label>
          <textarea class="textarea" id="Alamat" name="Alamat" placeholder="Ketik Alamat Lengkap...">{{ old('Alamat') }}</textarea>
        </div>
        <div class="form-field">
          <label for="TglLahir" class="form-label">Tanggal Lahir</label>
          <input type="date" class="input" id="TglLahir" name="TglLahir" value="{{ old('TglLahir') }}">
        </div>
        <div class="form-field">
          <label for="Golongan" class="form-label">Golongan</label>
          <select class="select" id="Golongan" name="Golongan">
            <option value="" selected disabled>Pilih...</option>
            @php $no = 1; @endphp
            @foreach ($golongan as $gol)
              <option value="{{ $no }}">{{ $gol }}</option>
              @php $no++; @endphp
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label for="Eselon" class="form-label">Eselon</label>
          <select class="select" id="Eselon" name="Eselon">
            <option value="" selected disabled>Pilih...</option>
            @php $no = 1; @endphp
            @foreach ($eselon as $eselo)
              <option value="{{ $no }}">{{ $eselo }}</option>
              @php $no++; @endphp
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label for="Jabatan" class="form-label">Jabatan</label>
          <input type="text" class="input" id="Jabatan" name="Jabatan" placeholder="Ketik Jabatan..." value="{{ old('Jabatan') }}">
        </div>
      </div>

      <hr class="nav-divider" style="margin: 20px 0">

      <h3 class="section-title">Role Pengguna</h3>
      <div class="form-grid">
        <div class="form-field">
          <label for="Role" class="form-label">Sebagai <span class="text-danger">*</span></label>
          <select class="select @error('Role') is-invalid @enderror" id="Role" name="Role">
            <option value="" selected disabled>Pilih...</option>
            <option value="1">Admin</option>
            <option value="2" @if ((DB::table('users')->where('role','Kepala Bidang')->count()) >= 1) hidden @endif>Kepala Bidang</option>
            <option value="3">Kepala Seksi</option>
            <option value="4">Staff</option>
          </select>
          @error('Role')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <p class="form-hint"><span class="text-danger">*</span> Harus diisi.</p>

      <div class="form-actions">
        <a href="{{ route('Admin/Show') }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Buat User</button>
      </div>
    </form>
  </section>
@endsection