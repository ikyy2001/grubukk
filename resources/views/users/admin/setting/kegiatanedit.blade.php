@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Edit Kegiatan</h1>
      <p class="page-sub muted">Ubah data kegiatan.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Kegiatan') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg" style="max-width: 800px">
    <form action="{{ route('Kegiatan') }}/{{ $kegiatan->id }}/edit" method="post">
      @method('patch')
      @csrf
      <div class="form-grid form-grid--full">
        <div class="form-field">
          <label for="Kegiatan" class="form-label">Kegiatan</label>
          <textarea class="textarea" id="Kegiatan" name="Kegiatan" placeholder="Ketik kegiatan...">{{ $kegiatan->kegiatan }}</textarea>
        </div>
      </div>
      <div class="form-actions">
        <a href="{{ route('Kegiatan') }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Simpan</button>
      </div>
    </form>
  </section>
@endsection