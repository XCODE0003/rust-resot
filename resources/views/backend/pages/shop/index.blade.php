@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Магазин'))
@section('headerTitle', __('Магазин'))
@section('headerDesc', __('Редактирование вещей магазина.'))

@php
    $name = "name_" .app()->getLocale();
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
                                <a href="{{ route('shopcategories.index') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-folder-list mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Категории') }}</span>
                                </a>
                                {{--
                                <a href="{{ route('shopsets.index') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-folder-list mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Наборы') }}</span>
                                </a>
                                 --}}
                                <a href="{{ route('shopitems.create') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Добавить предмет') }}</span>
                                </a>
                                <a href="{{ route('shopitems.resetCache') }}" class="btn btn-sm btn-primary btn-delete">
                                    <em class="icon ni ni-reload mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Сбросить кэш') }}</span>
                                </a>
                            </h5>

                            <div class="card-tools d-none d-md-inline" style="width: 200px; margin-right: 14px;">
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="0" @if(request()->query('category_id') == 0) selected @endif>{{ __('Все') }}</option>
                                    @foreach($shopcategories as $category)
                                        <option value="{{ $category->id }}" @if(request()->query('category_id') == $category->id) selected @endif>{{ $category->$title }}</option>
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

                    @if(!empty($shopitems))
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($shopitems as $shopitem)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        <img src="/storage/{{ $shopitem->image }}" alt="{{ $shopitem->id }}" style="max-width: 32px;">
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">

                                        <a href="{{ route('shopitems.edit', $shopitem) }}" target="_blank">
                                                {{ $shopitem->$name }}
                                        </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $shopitem->sort }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $shopitem->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="dropdown">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-plain">
                                                <li><a href="{{ route('shopitems.edit', $shopitem) }}">{{ __('Редактировать') }}</a></li>
                                                <li><a href="{{ route('shopitems.duplicate', $shopitem) }}">{{ __('Дублировать') }}</a></li>
                                                <form action="{{ route('shopitems.destroy', $shopitem) }}" method="POST">
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
                        {{ $shopitems->links('backend.layouts.pagination.shopitems') }}
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
            $('#category_id').on('change', function() {
                document.location.replace('{{ route('shopitems.index') }}?search={{ request()->query('search') }}'+'&category_id='+$(this).val());
            });
        });
    </script>
@endpush