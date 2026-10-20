@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Pegawai</h1>
      <p class="page-sub muted">Daftar pegawai.</p>
    </div>
  </div>

  <section class="card card--pad-lg">
    <div class="card-head">
      <h2 class="card-title">Daftar Pegawai</h2>
      <form class="search-pill" action="/Pegawai" method="GET" role="search">
        <x-icon name="search" :size="16" />
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." aria-label="Cari Nama atau NIP">
      </form>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">Nama</th>
            <th scope="col">NIP</th>
            <th scope="col">Jabatan</th>
            <th scope="col">Golongan</th>
            <th scope="col" class="row-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @php
            $perPage = $users->perPage();
            $currentPage = $users->currentPage();
            $no = ($currentPage - 1) * $perPage + 1;
          @endphp
          @foreach ($users as $pegawai)
            <tr>
              <td class="num">{{ $no }}</td>
              <td class="cell-name">{{ $pegawai->nama }}</td>
              <td class="tabular">{{ $pegawai->nip }}</td>
              <td class="cell-sub">{{ $pegawai->jabatan ? $pegawai->jabatan->jabatan : '-' }}</td>
              <td>{{ $pegawai->golongan ? $pegawai->golongan->golongan : '-' }}</td>
              <td class="row-actions">
                <a href="{{ route('Pegawai') }}/{{ $pegawai->nip }}" class="btn btn--ghost btn--sm">Lihat</a>
              </td>
            </tr>
            @php $no++; @endphp
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="pagination">
      {{ $users->render() }}
    </div>
  </section>
@endsection