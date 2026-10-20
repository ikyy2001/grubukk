@extends('layouts.app')

@section('title', request()->path())

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $user->role ?? '' }}</div>
      <h1 class="page-title">Daftar SPPD (Admin)</h1>
      <p class="page-sub muted">Seluruh data SPPD.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('EntrySPPD') }}" class="btn btn--primary">
        <x-icon name="plus" :size="16" />
        Tambah SPPD
      </a>
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

  @if ($user->role_check(['Admin','Staff','Kepala Bidang', 'Kepala Seksi']))
    <section class="card card--pad-lg">
      <div class="card-head">
        <h2 class="card-title">Daftar SPPD</h2>
      </div>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th style="width: 60px">No</th>
              <th>No Surat</th>
              <th>Acara</th>
              <th>Yang diperintah</th>
              <th class="row-actions">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @php $no = $sppd->currentPage() * $sppd->perPage() - ($sppd->perPage() - 1); @endphp
            @foreach ($sppd as $item)
              <tr>
                <td class="num">{{ $no++ }}</td>
                <td>{{ $item->no_surat }}</td>
                <td>{{ $item->acara }}</td>
                <td>
                  @php $no1 = 1; @endphp
                  @foreach ($item->user as $usr)
                    <div>{{ $no1++ }}. {{ \Illuminate\Support\Str::limit($usr->nama, 30, '...') }}</div>
                  @endforeach
                </td>
                <td class="row-actions">
                  <a href="{{ route('SPPD') }}/{{ $item->id }}/SPPD" class="btn btn--primary btn--sm">SPPD</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if ($sppd->hasPages())
        <div style="margin-top: 16px">
          {{ $sppd->links() }}
        </div>
      @endif
    </section>
  @else
    @include('layouts.akses')
  @endif
@endsection