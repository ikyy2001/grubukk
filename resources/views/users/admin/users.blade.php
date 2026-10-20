@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Data Pengguna</h1>
      <p class="page-sub muted">Kelola daftar pengguna sistem.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('Admin/CreateUser') }}" class="btn btn--primary">
        <x-icon name="plus" :size="16" />
        Tambah User
      </a>
    </div>
  </div>

  @if (session('Success'))
    <div class="alert alert--pos" data-autohide>
      <x-icon name="circle-check" :size="18" />
      <span>{{ session('Success') }}</span>
      <button class="icon-btn" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan">
        <x-icon name="x" :size="14" />
      </button>
    </div>
  @endif
  @if (session('Failed'))
    <div class="alert alert--neg" data-autohide>
      <x-icon name="circle-alert" :size="18" />
      <span>{{ session('Failed') }}</span>
      <button class="icon-btn" onclick="this.closest('.alert').remove()" aria-label="Tutup pesan">
        <x-icon name="x" :size="14" />
      </button>
    </div>
  @endif

  <section class="card card--pad-lg">
    <div class="card-head">
      <h2 class="card-title">Daftar Users</h2>
      <form class="search-pill" action="{{ route('Admin/Show') }}" method="GET" role="search">
        <x-icon name="search" :size="16" />
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." aria-label="Cari Nama atau NIP">
      </form>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">NIP</th>
            <th scope="col">Nama</th>
            <th scope="col">Status</th>
            <th scope="col" class="row-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @php
            $perPage = $users->perPage();
            $currentPage = $users->currentPage();
            $no = ($currentPage * $perPage) - ($perPage - 1);
            if ($no < 1) {
                $no = ($currentPage - 1) * $perPage + 1;
            }
          @endphp
          @foreach ($users as $user_peg)
            <tr>
              <td class="num">{{ $no }}</td>
              <td class="tabular">{{ $user_peg->nip }}</td>
              <td class="cell-name">{{ $user_peg->nama }}</td>
              <td>
                <span class="tag">{{ $user_peg->role }}</span>
              </td>
              <td class="row-actions">
                <a href="{{ route('Admin/Show') }}/{{ $user_peg->nip }}" class="btn btn--ghost btn--sm">Lihat</a>
                <a href="{{ route('Admin/Show') }}/{{ $user_peg->nip }}/Edit" class="btn btn--ghost btn--sm">Edit</a>
                @if ($user_peg->cek == 1)
                  <form action="{{ route('Admin/Show') }}/{{ $user_peg->nip }}/Delete" method="post" style="display:inline" data-confirm="Anda yakin ingin menghapus {{ $user_peg->nama }} (NIP: {{ $user_peg->nip }})?">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn--danger btn--sm">Hapus</button>
                  </form>
                @endif
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