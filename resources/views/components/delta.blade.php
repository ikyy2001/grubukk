@props(['trend' => 'flat', 'text' => ''])
{{-- Delta badge. $trend: pos|neg|flat. Meaning never depends on color alone:
     direction is conveyed by the icon, sign by the text. --}}
@php
$icon = $trend === 'pos' ? 'trending-up' : ($trend === 'neg' ? 'trending-down' : 'minus');
@endphp
<span {{ $attributes->merge(['class' => 'delta delta--' . $trend]) }}>
  <x-icon :name="$icon" :size="12" />{{ $text }}
</span>
