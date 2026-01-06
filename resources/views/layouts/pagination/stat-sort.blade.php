@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <a><i class="fa-solid fa-angles-left"></i></a>
    @else
        <a href="{{ $paginator->previousPageUrl() }}&type={{ request()->query('type') }}&search={{ request()->query('search') }}@if(request()->has('res_sort')){{ '&res_sort=' }}{{ request()->query('res_sort') }}@elseif(request()->has('pvp_sort')){{ '&pvp_sort=' }}{{ request()->query('pvp_sort') }}@elseif(request()->has('raids_sort')){{ '&raids_sort=' }}{{ request()->query('raids_sort') }}@elseif(request()->has('raids_doors_sort')){{ '&raids_doors_sort=' }}{{ request()->query('raids_doors_sort') }}@elseif(request()->has('hits_sort')){{ '&hits_sort=' }}{{ request()->query('hits_sort') }}@endif{{ '&server_id=' }}@if(request()->has('server_id')){{ request()->query('server_id') }}@else{{ getservers()[0]->id }}@endif"><i class="fa-solid fa-angles-left"></i></a>
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
                    <a href="{{ $url }}&type={{ request()->query('type') }}&search={{ request()->query('search') }}@if(request()->has('res_sort')){{ '&res_sort=' }}{{ request()->query('res_sort') }}@elseif(request()->has('pvp_sort')){{ '&pvp_sort=' }}{{ request()->query('pvp_sort') }}@elseif(request()->has('raids_sort')){{ '&raids_sort=' }}{{ request()->query('raids_sort') }}@elseif(request()->has('raids_doors_sort')){{ '&raids_doors_sort=' }}{{ request()->query('raids_doors_sort') }}@elseif(request()->has('hits_sort')){{ '&hits_sort=' }}{{ request()->query('hits_sort') }}@endif{{ '&server_id=' }}@if(request()->has('server_id')){{ request()->query('server_id') }}@else{{ getservers()[0]->id }}@endif">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}&type={{ request()->query('type') }}&search={{ request()->query('search') }}@if(request()->has('res_sort')){{ '&res_sort=' }}{{ request()->query('res_sort') }}@elseif(request()->has('pvp_sort')){{ '&pvp_sort=' }}{{ request()->query('pvp_sort') }}@elseif(request()->has('raids_sort')){{ '&raids_sort=' }}{{ request()->query('raids_sort') }}@elseif(request()->has('raids_doors_sort')){{ '&raids_doors_sort=' }}{{ request()->query('raids_doors_sort') }}@elseif(request()->has('hits_sort')){{ '&hits_sort=' }}{{ request()->query('hits_sort') }}@endif{{ '&server_id=' }}@if(request()->has('server_id')){{ request()->query('server_id') }}@else{{ getservers()[0]->id }}@endif"><i class="fa-solid fa-angles-right"></i></a>
    @else
        <a><i class="fa-solid fa-angles-right"></i></a>

    @endif

@endif
