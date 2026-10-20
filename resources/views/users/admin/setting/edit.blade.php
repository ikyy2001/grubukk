@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Edit Dasar Surat</h1>
      <p class="page-sub muted">Ubah dasar surat SPPD.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('DasarSurat') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg" style="max-width: 800px">
    <form action="{{ route('DasarSurat') }}/{{ $dasar->id }}/edit" method="post">
      @method('patch')
      @csrf
      <div class="form-grid form-grid--full">
        <div class="form-field">
          <label for="DasarSurat" class="form-label">Dasar Surat</label>
          <textarea class="textarea" id="DasarSurat" name="DasarSurat" placeholder="Ketik dasar surat...">{{ $dasar->dasar }}</textarea>
        </div>
      </div>
      <div class="form-actions">
        <a href="{{ route('DasarSurat') }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Simpan</button>
      </div>
    </form>
  </section>
@endsection