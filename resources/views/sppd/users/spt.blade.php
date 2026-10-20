@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">SPPD</div>
      <h1 class="page-title">Surat Perintah Tugas</h1>
      <p class="page-sub muted">Kelola data SPT/ SPPD Anda.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('SPPD') }}" class="btn btn--outline">
        <x-icon name="arrow-left" :size="16" />
        Kembali
      </a>
    </div>
  </div>

  <section class="card card--pad-lg" style="max-width: 800px">
    <div class="card-head">
      <h2 class="card-title">Surat Perintah Tugas</h2>
    </div>
    <div style="text-align: center">
      <p class="muted">Lengkapi data untuk melanjutkan pengisian SPT/ SPPD.</p>
      <a href="{{ route('SPPD') }}/{{ $sppd->id }}/add" class="btn btn--primary" style="margin-top: 12px">Lengkapi Data</a>
    </div>
  </section>
@endsection