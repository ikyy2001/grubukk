@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="card card--pad-lg" style="max-width: 560px; margin: 40px auto; text-align:center">
    <div style="display:flex; flex-direction:column; align-items:center; gap:12px">
        <x-icon name="lock" :size="48" />
        <h2>Akses Ditolak</h2>
        <p class="muted">Maaf, Anda tidak memiliki akses untuk membuka halaman ini.</p>
        <a href="{{ route('SPPD') }}" class="btn btn--primary">Kembali ke SPPD Saya</a>
    </div>
</div>
@endsection