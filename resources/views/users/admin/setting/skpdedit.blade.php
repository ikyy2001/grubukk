@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Edit SKPD</h1>
      <p class="page-sub muted">Ubah data SKPD.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('SKPD') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg" style="max-width: 800px">
    <form action="{{ route('SKPD') }}/{{ $skpd->id }}/edit" method="post">
      @method('patch')
      @csrf
      <div class="form-grid form-grid--full">
        <div class="form-field">
          <label for="SKPD" class="form-label">SKPD</label>
          <textarea class="textarea" id="SKPD" name="SKPD" placeholder="Ketik SKPD...">{{ $skpd->skpd }}</textarea>
        </div>
      </div>
      <div class="form-actions">
        <a href="{{ route('SKPD') }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Simpan</button>
      </div>
    </form>
  </section>
@endsection