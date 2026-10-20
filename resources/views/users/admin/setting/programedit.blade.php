@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Edit Program</h1>
      <p class="page-sub muted">Ubah data program.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Program') }}" class="btn btn--outline">Kembali</a>
    </div>
  </div>

  <section class="card card--pad-lg" style="max-width: 800px">
    <form action="{{ route('Program') }}/{{ $program->id }}/edit" method="post">
      @method('patch')
      @csrf
      <div class="form-grid form-grid--full">
        <div class="form-field">
          <label for="Program" class="form-label">Program</label>
          <textarea class="textarea" id="Program" name="Program" placeholder="Ketik program...">{{ $program->program }}</textarea>
        </div>
      </div>
      <div class="form-actions">
        <a href="{{ route('Program') }}" class="btn btn--outline">Batal</a>
        <button type="submit" class="btn btn--primary">Simpan</button>
      </div>
    </form>
  </section>
@endsection