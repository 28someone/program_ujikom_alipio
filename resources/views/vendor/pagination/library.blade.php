@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination-shell">
        <div class="pagination-summary">
            Menampilkan {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}
            dari {{ $paginator->total() }} data
        </div>

        <div class="pagination-control">
            @if ($paginator->onFirstPage())
                <span class="pagination-button is-disabled" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <span class="pagination-arrow" aria-hidden="true">&lsaquo;</span>
                </span>
            @else
                <a class="pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">
                    <span class="pagination-arrow" aria-hidden="true">&lsaquo;</span>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-ellipsis" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-button is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pagination-button" href="{{ $url }}" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">
                    <span class="pagination-arrow" aria-hidden="true">&rsaquo;</span>
                </a>
            @else
                <span class="pagination-button is-disabled" aria-disabled="true" aria-label="Halaman berikutnya">
                    <span class="pagination-arrow" aria-hidden="true">&rsaquo;</span>
                </span>
            @endif
        </div>
    </nav>
@endif
