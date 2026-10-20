@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Edit Users</h1>
      <p class="page-sub muted">Perbarui data pengguna.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Admin/Show') }}/{{ $user_pegawai->nip }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg">
    <form method="POST" action="{{ route('Admin/Show') }}/{{ $user_pegawai->nip }}/Edit">
      @method('patch')
      @csrf
      <h3 class="section-title">Informasi Akun</h3>
      <div class="form-grid">
        <div class="form-field form-grid--full">
          <label for="NIP" class="form-label">NIP <span class="text-danger">*</span></label>
          <input type="number" class="input @error('NIP') is-invalid @enderror" id="NIP" name="NIP" placeholder="Ketik NIP..." value="{{ $user_pegawai->nip }}">
          @error('NIP')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <hr class="nav-divider" style="margin: 20px 0">

      <h3 class="section-title">Data Pegawai</h3>
      <div class="form-grid">
        <div class="form-field form-grid--full">
          <label for="Nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" class="input @error('Nama') is-invalid @enderror" id="Nama" name="Nama" placeholder="Ketik Nama Lengkap..." value="{{ $user_pegawai->nama }}">
          @error('Nama')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-field form-grid--full">
          <label for="Alamat" class="form-label">Alamat Lengkap</label>
          <textarea class="textarea" id="Alamat" name="Alamat" placeholder="Ketik Alamat Lengkap...">{{ $user_pegawai->alamat }}</textarea>
        </div>
        <div class="form-field">
          <label for="TglLahir" class="form-label">Tanggal Lahir</label>
          <input type="date" class="input" id="TglLahir" name="TglLahir" value="{{ $user_pegawai->tgllahir }}">
        </div>
        <div class="form-field">
          <label for="Golongan" class="form-label">Golongan</label>
          <select class="select" id="Golongan" name="Golongan">
            <option value="" selected disabled>Pilih...</option>
            @php $no = 1; @endphp
            @foreach ($golongan as $gol)
              <option value="{{ $no }}" @if ($user_pegawai->golongan && $user_pegawai->golongan->golongan == $gol) selected @endif>{{ $gol }}</option>
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
              <option value="{{ $no }}" @if ($user_pegawai->eselon && $user_pegawai->eselon->nama_eselon == $eselo) selected @endif>{{ $eselo }}</option>
              @php $no++; @endphp
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label for="Jabatan" class="form-label">Jabatan</label>
          <input type="text" class="input" id="Jabatan" name="Jabatan" placeholder="Ketik Jabatan..." value="{{ $user_pegawai->jabatan ? $user_pegawai->jabatan->jabatan : '' }}">
        </div>
      </div>

      <hr class="nav-divider" style="margin: 20px 0">

      <h3 class="section-title">Role Pengguna</h3>
      <div class="form-grid">
        @if ($user_pegawai->cek == 1)
          <div class="form-field">
            <label for="Role" class="form-label">Sebagai <span class="text-danger">*</span></label>
            <select class="select @error('Role') is-invalid @enderror" id="Role" name="Role">
              <option value="" selected disabled>Pilih...</option>
              <option value="1" @if ($user_pegawai->role == 'Admin') selected @endif>Admin</option>
              <option value="2" @if ((DB::table('users')->where('role','Kepala Bidang')->count()) >= 1 && $user_pegawai->role != 'Kepala Bidang') hidden @endif @if ($user_pegawai->role == 'Kepala Bidang') selected @endif>Kepala Bidang</option>
              <option value="3" @if ($user_pegawai->role == 'Kepala Seksi') selected @endif>Kepala Seksi</option>
              <option value="4" @if ($user_pegawai->role == 'Staff') selected @endif>Staff</option>
            </select>
            @error('Role')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        @else
          <div class="form-field">
            <label for="Role" class="form-label">Sebagai</label>
            <input type="text" class="input" id="Role" name="Role" value="{{ $user_pegawai->role }}" readonly>
          </div>
        @endif
      </div>

      <div class="form-actions">
        <a href="{{ route('Admin/Show') }}/{{ $user_pegawai->nip }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Simpan</button>
      </div>
    </form>
  </section>
@endsection