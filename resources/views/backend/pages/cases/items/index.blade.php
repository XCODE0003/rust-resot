@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Предметы кейсов'))
@section('headerTitle', __('Предметы кейсов'))
@section('headerDesc', __('Редактирование предметов') . '.')

@php
    $title = "title_" .app()->getLocale();
    $description = "description_" .app()->getLocale();
@endphp

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <a href="{{ route('cases.index') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-folder-list mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Кейсы') }}</span>
                                </a>
                                <a href="{{ route('casesitems.create') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Добавить предмет') }}</span>
                                </a>
                            </h5>
                            <div class="card-tools d-none d-md-inline" style="width: 200px; margin-right: 14px;">
                                <select id="sort_price" name="sort_price" class="form-select">
                                    <option value="0" @if(request()->query('sort_price') == '0') selected @endif>{{ __('None') }}</option>
                                    <option value="asc" @if(request()->query('sort_price') == 'asc') selected @endif>{{ __('Price ASC') }}</option>
                                    <option value="desc" @if(request()->query('sort_price') == 'desc') selected @endif>{{ __('Price DESC') }}</option>
                                </select>
                            </div>

                            <div class="card-tools d-none d-md-inline" style="width: 200px; margin-right: 14px;">
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="0" @if(request()->query('category_id') == 0) selected @endif>{{ __('Все') }}</option>
                                    @foreach(get_caseitem_categories() as $category_id => $category_name)
                                        <option value="{{ $category_id }}" @if(request()->query('category_id') == $category_id) selected @endif>{{ $category_name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="card-tools d-none d-md-inline">
                                <form method="GET">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-search"></em>
                                        </div>
                                        <input type="text" class="form-control" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Поиск') }}...">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if(!empty($casesitems))
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($casesitems as $casesitem)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        @if($casesitem->image_url !== NULL)<img src="{{ $casesitem->image_url }}" alt="{{ $casesitem->id }}" style="max-width: 32px;">@endif
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        <a href="{{ route('casesitems.edit', $casesitem) }}" target="_blank">
                                                {{ $casesitem->title }}
                                        </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $casesitem->item_id }}
                                            <a class="btn btn-sm btn-icon btn-trigger getcopy" data-item_id="{{ $casesitem->item_id }}" title="{{ __('Скопировать ID') }}">
                                                <em class="icon ni ni-copy ml-1"></em>
                                            </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $casesitem->price }} {{ __('руб.') }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $casesitem->source }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        @if($casesitem->status == 1)
                                            <span style="color: green;">{{ __('Активный') }}</span>
                                        @else
                                            <span style="color: red;">{{ __('Не активный') }}</span>
                                        @endif

                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $casesitem->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $casesitem->sort }}
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="dropdown">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-plain">
                                                <li><a href="{{ route('casesitems.edit', $casesitem) }}">{{ __('Редактировать') }}</a></li>
                                                <form action="{{ route('casesitems.destroy', $casesitem) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <li><a href="#" class="text-danger" onclick="this.closest('form').submit();return false;">{{ __('Удалить') }}</a></li>
                                                </form>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-inner">
                        {{ $casesitems->links('backend.layouts.pagination.caseitems') }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            $('#sort_price').on('change', function() {
                document.location.replace('{{ route('casesitems.index') }}?sort_price='+$('#sort_price').val()+'&search={{ request()->query('search') }}'+'&category_id={{ request()->query('category_id') }}');
            });
            $('#category_id').on('change', function() {
                document.location.replace('{{ route('casesitems.index') }}?sort_price='+$('#sort_price').val()+'&search={{ request()->query('search') }}'+'&category_id='+$(this).val());
            });

            $('.getcopy').on('click', function () {
                let temp = $("<input>");
                $("body").append(temp);
                let item_id = $(this).data('item_id');
                temp.val(item_id).select();
                document.execCommand("copy");
                temp.remove();

                $(this).addClass('is-copied');
                setTimeout(function () {
                    $('.getcopy').removeClass('is-copied');
                }, 1000);
            });
        });
    </script>
@endpush