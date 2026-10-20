@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">Profil</div>
      <h1 class="page-title">Edit Profil</h1>
      <p class="page-sub muted">Perbarui data profil Anda.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Users/Profile') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg">
    <form method="POST" action="{{ route('Users/Profile/Edit') }}">
      @method('patch')
      @csrf
      <h3 class="section-title">Informasi Akun</h3>
      <div class="form-grid">
        <div class="form-field">
          <label for="NIP" class="form-label">NIP <span class="text-danger">*</span></label>
          <input type="number" class="input @error('NIP') is-invalid @enderror" id="NIP" name="NIP" placeholder="Ketik NIP..." value="{{ $user->nip }}">
          @error('NIP')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <hr class="nav-divider" style="margin: 20px 0">

      <h3 class="section-title">Data Pribadi</h3>
      <div class="form-grid">
        <div class="form-field form-grid--full">
          <label for="Nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" class="input @error('Nama') is-invalid @enderror" id="Nama" name="Nama" placeholder="Ketik Nama Lengkap..." value="{{ $user->nama }}">
          @error('Nama')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-field form-grid--full">
          <label for="Alamat" class="form-label">Alamat Lengkap</label>
          <textarea class="textarea" id="Alamat" name="Alamat" placeholder="Ketik Alamat Lengkap...">{{ $user->alamat }}</textarea>
        </div>
        <div class="form-field">
          <label for="TglLahir" class="form-label">Tanggal Lahir</label>
          <input type="date" class="input" id="TglLahir" name="TglLahir" value="{{ $user->tgllahir }}">
        </div>
        <div class="form-field">
          <label for="Golongan" class="form-label">Golongan</label>
          <select class="select" id="Golongan" name="Golongan">
            <option value="" selected disabled>Pilih...</option>
            @php $no = 1; @endphp
            @foreach ($golongan as $gol)
              <option value="{{ $no }}" @if ($user->golongan && $user->golongan->golongan == $gol) selected @endif>{{ $gol }}</option>
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
              <option value="{{ $no }}" @if ($user->eselon && $user->eselon->nama_eselon == $eselo) selected @endif>{{ $eselo }}</option>
              @php $no++; @endphp
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label for="Jabatan" class="form-label">Jabatan</label>
          <input type="text" class="input" id="Jabatan" name="Jabatan" placeholder="Ketik Jabatan..." value="{{ $user->jabatan ? $user->jabatan->jabatan : '' }}">
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('Users/Profile') }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Simpan</button>
      </div>
    </form>
  </section>
@endsection