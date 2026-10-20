@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
  <div class="page-head">
    <div>
      <div class="eyebrow">Dashboard</div>
      <h1 class="page-title">Ringkasan</h1>
      <p class="page-sub muted">Lihat performa perjalanan dinas dalam satu tampilan.</p>
    </div>
    <div class="page-actions">
      <a href="{{ route('SPPD') }}" class="btn btn--outline"><x-icon name="file-text" :size="16" /> SPPD Saya</a>
      <a href="{{ route('EntrySPPD') }}" class="btn btn--primary"><x-icon name="plus" :size="16" /> Buat SPT/SPPD</a>
    </div>
  </div>

  {{-- Stat grid --}}
  <div class="stat-grid">
    @php
      $trendMth = $deltaPct >= 0 ? 'pos' : 'neg';
      $deltaText = abs($deltaPct) . '%';
    @endphp
    <article class="stat-card">
      <div class="stat-card__label">Total SPPD</div>
      <x-delta trend="{{ $trendMth }}" text="{{ $deltaText }}" class="stat-card__badge" />
      <div class="stat-card__value tabular">{{ number_format($totalSppd, 0, ',', '.') }}</div>
      <div class="stat-card__note">Naik/turun vs bulan lalu</div>
      <x-spark :points="$spark->toArray()" trend="{{ $trendMth }}" class="stat-card__spark" />
    </article>

    <article class="stat-card">
      <div class="stat-card__label">SPPD Bulan Ini</div>
      <x-delta trend="{{ $trendMth }}" text="{{ $deltaText }}" class="stat-card__badge" />
      <div class="stat-card__value tabular">{{ number_format($bulanIni, 0, ',', '.') }}</div>
      <div class="stat-card__note">{{ \Carbon\Carbon::now()->isoFormat('MMM YYYY') }}</div>
      <x-spark :points="$spark->toArray()" trend="{{ $trendMth }}" class="stat-card__spark" />
    </article>

    <article class="stat-card">
      <div class="stat-card__label">Sedang Berjalan</div>
      <x-delta trend="pos" text="{{ $berjalan > 0 ? '+'.number_format($berjalan,0,',','.') : '0' }}" class="stat-card__badge" />
      <div class="stat-card__value tabular">{{ number_format($berjalan, 0, ',', '.') }}</div>
      <div class="stat-card__note">Dalam perjalanan hari ini</div>
    </article>

    <article class="stat-card">
      <div class="stat-card__label">Pegawai Aktif</div>
      <x-delta trend="flat" text="—" class="stat-card__badge" />
      <div class="stat-card__value tabular">{{ number_format($pegawaiAktif, 0, ',', '.') }}</div>
      <div class="stat-card__note">Terdaftar dalam sistem</div>
    </article>
  </div>

  {{-- Chart + recent SPPD --}}
  <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:16px; align-items:start">
    <section class="card card--pad-lg" style="grid-column:1/-1">
      <div class="card-head">
        <div>
          <h2 class="card-title">SPPD per Bulan</h2>
          <p class="card-note muted">Perbandingan 8 bulan terakhir vs periode yang sama tahun lalu</p>
        </div>
        <div class="tag">{{ $periodeLabel }}</div>
      </div>

      <div class="chart-stat-row">
        <div class="chart-stat">
          <div class="chart-stat__label"><span class="dot dot--pos"></span> Tahun Ini <x-icon name="info" :size="14" /></div>
          <div class="chart-stat__value tabular">{{ number_format($thisYearTotal, 0, ',', '.') }}</div>
        </div>
        <div class="chart-stat">
          <div class="chart-stat__label">Tahun Lalu <span class="muted">{{ now()->subYear()->isoFormat('YYYY') }}</span></div>
          <div class="chart-stat__value tabular">{{ number_format($lastYearTotal, 0, ',', '.') }}</div>
        </div>
      </div>

      <div class="chart-legend">
        <span class="legend-item"><span class="legend-dash"></span> 8 Bulan Terakhir</span>
        <span class="legend-item"><span class="legend-dash legend-dash--secondary"></span> Tahun Lalu (periode sama)</span>
      </div>

      @php
        // Grafik: 8 bulan terakhir (garis solid) vs periode yang sama tahun lalu (garis putus-putus).
        $w=1200; $h=220; $pad=24;
        $s1 = collect($series)->map(fn ($v) => (float) $v)->values();
        $s2 = collect($seriesLastYear ?? [])->map(fn ($v) => (float) $v)->values();
        $allv = $s1->concat($s2);
        $vmin = 0;
        $vmax = max((float) ($allv->max() ?? 0), 1);
        $range = ($vmax - $vmin) ?: 1;
        $n = max($s1->count(), $s2->count());
        $stepX = $n>1 ? ($w-$pad*2)/($n-1) : 0;

        $toCoords = function ($s) use ($pad, $h, $stepX, $vmin, $range) {
            return $s->values()->map(function ($v, $i) use ($pad, $h, $stepX, $vmin, $range) {
                $x = $pad + $i*$stepX;
                $y = $h - $pad - (($v - $vmin)/$range)*($h - $pad*2);
                return round($x,2).' '.round($y,2);
            })->all();
        };

        $coords1 = $toCoords($s1);
        $coords2 = $toCoords($s2);
        $line1 = $coords1 ? 'M'.implode(' L',$coords1) : '';
        $line2 = $coords2 ? 'M'.implode(' L',$coords2) : '';
        $area1 = $coords1
            ? $line1.' L'.round($pad+($n-1)*$stepX,2).' '.$h.' L'.$pad.' '.$h.' Z'
            : '';
      @endphp
      <svg class="chart-svg" viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
        <defs>
          <linearGradient id="c1" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="var(--primary)" stop-opacity=".12"/>
            <stop offset="1" stop-color="var(--primary)" stop-opacity="0"/>
          </linearGradient>
        </defs>
        @if ($area1)
          <path d="{{ $area1 }}" fill="url(#c1)"/>
        @endif
        @if ($line2)
          <path d="{{ $line2 }}" fill="none" stroke="var(--chart-line-secondary)" stroke-width="2" stroke-dasharray="6 6" stroke-linecap="round" stroke-linejoin="round"/>
        @endif
        @if ($line1)
          <path d="{{ $line1 }}" fill="none" stroke="var(--primary)" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"/>
        @endif
      </svg>
      <div class="chart-xlabels">
        @foreach ($bulanLabels as $lbl)
          <span>{{ $lbl }}</span>
        @endforeach
      </div>
    </section>

    <section class="card card--pad-lg" style="grid-column:1/-1">
      <div class="card-head">
        <h2 class="card-title">SPPD Terbaru</h2>
        <a href="{{ route('SPPD') }}" class="btn btn--ghost btn--sm">Lihat Semua <x-icon name="arrow-right" :size="16" /></a>
      </div>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>No. Surat</th>
              <th>Acara</th>
              <th>Yang Diperintah</th>
              <th>Tanggal</th>
              <th>Tujuan</th>
              <th class="row-actions">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($recent as $s)
              <tr>
                <td class="num">{{ $s->no_surat }}/{{ $s->tahun_surat }}</td>
                <td class="cell-name">{{ Str::limit($s->acara, 36) }}</td>
                <td>
                  @foreach ($s->user as $us)
                    <div class="cell-sub">{{ $us->nama }}</div>
                  @endforeach
                </td>
                <td class="cell-sub">{{ optional($s->tgl_pergi)->format('d M Y') }} — {{ optional($s->tgl_kembali)->format('d M Y') }}</td>
                <td class="cell-sub">{{ optional($s->tempat)->tempat_tujuan ?? '-' }}</td>
                <td class="row-actions">
                  <a href="{{ route('SPPD').'/'.$s->id.'/SPPD' }}" class="btn btn--ghost btn--sm">Detail <x-icon name="arrow-right" :size="16" /></a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="empty">Belum ada SPPD. <a href="{{ route('EntrySPPD') }}">Buat SPT/SPPD pertama Anda</a></td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
