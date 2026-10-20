<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Grubukk') }} — @yield('title', 'Dashboard')</title>
  <link rel="icon" href="{{ asset('img/logo.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/grubukk.css') }}">
  <script>
    // Apply saved theme before paint to avoid a flash of the wrong theme.
    (function () {
      try {
        var t = localStorage.getItem('grubukk-theme');
        if (t) document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
    })();
  </script>
</head>
<body class="{{ Auth::check() ? '' : 'is-guest' }}">
@auth
  {{-- ============ App shell: fixed header + sidebar ============ --}}
  <header class="topbar">
    <button class="icon-btn hamburger" data-sidebar-toggle aria-label="Buka menu navigasi">
      <x-icon name="menu" />
    </button>
    <a href="{{ route('Dashboard') }}" class="brand">
      <span class="logo-mark">G</span>
      <span class="wordmark">Grubukk</span>
    </a>
    <div class="topbar-search">
      <form class="search-pill" action="{{ route('SPPD') }}" method="GET" role="search">
        <x-icon name="search" :size="16" />
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat atau acara…" aria-label="Cari SPPD">
      </form>
    </div>
    <div class="topbar-right">
      <button class="icon-btn" data-theme-toggle aria-label="Ganti tema"></button>
      <a href="{{ route('Users/Profile') }}" class="topbar-user" title="Profil saya">
        <span class="avatar">{{ \Illuminate\Support\Str::of($user->nama ?? Auth::user()->nama)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}</span>
      </a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="topbar-user">
          <x-icon name="log-out" :size="16" /> Keluar
        </button>
      </form>
    </div>
  </header>

  <button class="sidebar-overlay" aria-label="Tutup menu navigasi"></button>

  <aside class="sidebar" aria-label="Menu utama">
    <div class="nav-label">Menu</div>
    <a href="{{ route('Dashboard') }}" class="nav-row {{ request()->is('Dashboard') ? 'active' : '' }}">
      <x-icon name="layout-dashboard" /> Dashboard
    </a>

    @php
      $mySppdCount = Auth::user()->sppd()->count();
      $isSppd = request()->is('SPPD*');
    @endphp
    <a href="{{ route('SPPD') }}" class="nav-row {{ $isSppd && !request()->is('SPPD/Entry') ? 'active' : '' }}">
      <x-icon name="file-text" /> SPPD Saya
      <span class="nav-badge">{{ $mySppdCount }}</span>
    </a>
    <a href="{{ route('EntrySPPD') }}" class="nav-row {{ request()->is('SPPD/Entry') ? 'active' : '' }}">
      <x-icon name="plus" /> Buat SPT / SPPD
    </a>

    @if (\App\Models\Auth\User::role_check(['Staff', 'Kepala Bidang', 'Kepala Seksi']))
      <a href="{{ route('Pegawai') }}" class="nav-row {{ request()->is('Pegawai*') ? 'active' : '' }}">
        <x-icon name="users" /> Pegawai
      </a>
    @endif

    @if (\App\Models\Auth\User::role_check(['Admin']))
      <hr class="nav-divider">
      <div class="nav-label">Administrator</div>
      <a href="{{ route('Admin/Show') }}" class="nav-row {{ request()->is('Admin/Show') || request()->is('Admin/CreateUser') ? 'active' : '' }}">
        <x-icon name="user" /> Data Pengguna
      </a>
      <a href="{{ route('AdminSPPD') }}" class="nav-row {{ request()->is('Admin/SPPD') ? 'active' : '' }}">
        <x-icon name="clipboard-list" /> Seluruh SPPD
      </a>
      <a href="{{ route('Setting') }}" class="nav-row {{ request()->is('Admin/Setting*') ? 'active' : '' }}">
        <x-icon name="settings" /> Pengaturan
      </a>
    @endif

    <div class="sidebar-foot">
      <hr class="nav-divider">
      <div class="nav-label">Akun</div>
      <a href="{{ route('Users/Profile') }}" class="nav-row {{ request()->is('Users/Profile*') ? 'active' : '' }}">
        <x-icon name="user" /> Profil Saya
      </a>
      <a href="{{ route('Users/Profile/ChangePassword') }}" class="nav-row {{ request()->is('Users/Profile/ChangePassword') ? 'active' : '' }}">
        <x-icon name="key-round" /> Ganti Kata Sandi
      </a>
    </div>
  </aside>

  <main class="main">
    <div class="page">
      @if (session('Login'))
        <div class="alert alert--pos" data-autohide>
          <x-icon name="circle-check" :size="18" />
          <span>{{ session('Login') }}, {{ $user->nama ?? Auth::user()->nama }}. Selamat bertugas! 🎯</span>
        </div>
      @endif
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
      @if ($errors->any())
        <div class="alert alert--neg">
          <x-icon name="circle-alert" :size="18" />
          <div>
            <strong>Periksa kembali isian Anda:</strong>
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      @yield('content')
    </div>
  </main>
@else
  {{-- ============ Guest shell (login / register) ============ --}}
  <div class="guest-wrap">
    <div class="guest-brand">
      <span class="logo-mark">G</span>
      <span class="wordmark">Grubukk</span>
      <span class="muted" style="font-size:13px">Sistem SPT &amp; SPPD — Dinas Pekerjaan Umum Sumber Daya Air</span>
    </div>

    @if (session('Success'))
      <div class="alert alert--pos" style="width:100%" data-autohide>
        <x-icon name="circle-check" :size="18" />
        <span>{{ session('Success') }}</span>
      </div>
    @endif
    @if ($errors->any())
      <div class="alert alert--neg" style="width:100%">
        <x-icon name="circle-alert" :size="18" />
        <div>
          <strong>Terjadi kesalahan:</strong>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="guest-card">
      @yield('content')
    </div>

    <p class="guest-foot">&copy; {{ date('Y') }} Grubukk. Seluruh hak cipta dilindungi.</p>
  </div>
@endauth

{{-- Shared confirm modal, used by any form carrying data-confirm --}}
<div class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal">
    <h2 class="modal__title" id="modal-title">Konfirmasi</h2>
    <p class="modal__text">Apakah Anda yakin?</p>
    <div class="modal__actions">
      <button class="btn btn--outline" data-modal-cancel>Batal</button>
      <button class="btn btn--danger" data-modal-ok>Ya, lanjutkan</button>
    </div>
  </div>
</div>

<script src="{{ asset('js/grubukk.js') }}"></script>
@yield('script')
</body>
</html>
