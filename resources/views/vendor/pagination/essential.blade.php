@if ($paginator->hasPages())
<nav class="pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="page-btn disabled"><x-icon name="chevron-left" :size="16" /></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" rel="prev"><x-icon name="chevron-left" :size="16" /></a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-btn disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" rel="next"><x-icon name="chevron-right" :size="16" /></a>
    @else
        <span class="page-btn disabled"><x-icon name="chevron-right" :size="16" /></span>
    @endif

    <span class="page-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
</nav>
@endif
