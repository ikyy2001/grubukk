@props(['points' => [], 'trend' => 'pos', 'id' => null])
{{-- Decorative sparkline. $points: array of numbers, 7-9 values.
     Pair with a delta badge + footnote that carry the real information. --}}
@php
$points = array_values(array_map('floatval', $points));
$w = 96; $h = 36; $pad = 2;
$min = min($points); $max = max($points);
$range = ($max - $min) ?: 1;
$n = count($points);
$stepX = $n > 1 ? ($w - $pad * 2) / ($n - 1) : 0;
$coords = [];
foreach ($points as $i => $v) {
    $x = $pad + $i * $stepX;
    $y = $h - $pad - (($v - $min) / $range) * ($h - $pad * 2);
    $coords[] = round($x, 1) . ' ' . round($y, 1);
}
$line = 'M' . implode(' L', $coords);
$area = $line . ' L' . round($pad + ($n - 1) * $stepX, 1) . ' ' . $h . ' L' . $pad . ' ' . $h . ' Z';
$gradId = 'spark-' . ($id ?? uniqid());
$color = $trend === 'neg' ? 'var(--neg-line)' : 'var(--pos-line)';
@endphp
<svg viewBox="0 0 {{ $w }} {{ $h }}" width="{{ $w }}" height="{{ $h }}" aria-hidden="true" {{ $attributes }}>
  <defs>
    <linearGradient id="{{ $gradId }}" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="{{ $color }}" stop-opacity=".16"/>
      <stop offset="1" stop-color="{{ $color }}" stop-opacity="0"/>
    </linearGradient>
  </defs>
  <path d="{{ $area }}" fill="url(#{{ $gradId }})"/>
  <path d="{{ $line }}" fill="none" stroke="{{ $color }}" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
