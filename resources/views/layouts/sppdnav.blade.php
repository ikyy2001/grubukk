<div style="margin-bottom: 16px">
    <div style="display:flex; justify-content:center; margin-bottom:12px">
        <span style="padding:8px 16px; border-radius:9999px; background:var(--nav-active); font-weight:600">Lengkapi Data SPPD</span>
    </div>
    <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap">
        <a href="{{ route('SPPD') }}/{{ $sppd->id }}/SPPD" class="btn btn--outline">
            <x-icon name="arrow-left" :size="16" /> Kembali ke SPPD
        </a>
        @if (request()->is('SPPD/'.$sppd->id."/add"))
            <a href="{{ route('SPPD') }}/{{ $sppd->id }}/angkutan" class="btn btn--primary">
                Selanjutnya <x-icon name="arrow-right" :size="16" />
            </a>
        @elseif (request()->is('SPPD/'.$sppd->id."/angkutan"))
            <a href="{{ route('SPPD') }}/{{ $sppd->id }}/bebanbiaya" class="btn btn--primary">
                Selanjutnya <x-icon name="arrow-right" :size="16" />
            </a>
        @elseif (request()->is('SPPD/'.$sppd->id."/bebanbiaya"))
            <a href="{{ route('SPPD') }}/{{ $sppd->id }}/keterangan" class="btn btn--primary">
                Selanjutnya <x-icon name="arrow-right" :size="16" />
            </a>
        @elseif (request()->is('SPPD/'.$sppd->id."/keterangan"))
            <a href="{{ route('SPPD') }}/{{ $sppd->id }}/selesai" class="btn btn--primary">
                Selesai <x-icon name="check" :size="16" />
            </a>
        @endif
    </div>
</div>