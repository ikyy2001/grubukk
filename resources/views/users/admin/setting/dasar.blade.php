@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Dasar Surat</h1>
      <p class="page-sub muted">Kelola dasar-dasar surat SPPD.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Setting') }}" class="btn btn--outline">Kembali ke Pengaturan</a>
    </div>
  </div>

  @if (session('Success'))
    <div class="alert alert--pos" data-autohide>
      <x-icon name="circle-check" :size="18" />
      <span>{{ session('Success') }}</span>
      <button class="icon-btn" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan"><x-icon name="x" :size="14" /></button>
    </div>
  @endif
  @if (session('Failed'))
    <div class="alert alert--neg" data-autohide>
      <x-icon name="circle-alert" :size="18" />
      <span>{{ session('Failed') }}</span>
      <button class="icon-btn" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan"><x-icon name="x" :size="14" /></button>
    </div>
  @endif

  <section class="card card--pad-lg">
    <div class="card-head">
      <h2 class="card-title">Daftar Dasar Surat</h2>
      <form action="{{ route('DasarSurat') }}" method="post">
        @csrf
        <button type="submit" class="btn btn--primary btn--sm">
          <x-icon name="plus" :size="16" />
          Tambah
        </button>
      </form>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th style="width: 60px">No</th>
            <th>Dasar Surat</th>
            <th class="row-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @php $no = 1; @endphp
          @foreach ($dasar as $isi)
            <tr>
              <td class="num">{{ $no++ }}</td>
              <td>
                <div style="white-space: pre-wrap; word-break: break-word;">{{ $isi->dasar }}</div>
              </td>
              <td class="row-actions">
                <a href="{{ route('DasarSurat') }}/{{ $isi->id }}/edit" class="btn btn--ghost btn--sm">Edit</a>
                <form action="{{ route('DasarSurat') }}/{{ $isi->id }}/delete" method="post" data-confirm="Yakin ingin menghapus dasar surat ini?">
                  @method('delete')
                  @csrf
                  <button type="submit" class="btn btn--danger btn--sm">Hapus</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
@endsection