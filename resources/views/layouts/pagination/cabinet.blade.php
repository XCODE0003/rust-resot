@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <a><i class="fa-solid fa-angles-left"></i></a>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"><i class="fa-solid fa-angles-left"></i></a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <a class="active" aria-disabled="true">{{ $element }}</a>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <a class="active">{{ $page }}</a>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a><i class="fa-solid fa-angles-right"></i></a>
    @else
        <a href="{{ $paginator->nextPageUrl() }}"><i class="fa-solid fa-angles-right"></i></a>
    @endif

@endif
