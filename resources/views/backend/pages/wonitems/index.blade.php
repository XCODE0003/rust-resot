@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Выигранные предметы из Кейса'))
@section('headerTitle', __('Выигранные предметы из Кейса'))
@section('headerDesc', __('Список предметов.'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group" style="justify-content: flex-end;">
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

                    @if(!empty($wonitems))
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($wonitems as $wonitem)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        {{ $wonitem->user->name }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md" style="display: flex !important;align-items: center;">
                                    <span class="tb-lead">
                                        <img src="{{ $wonitem->item_icon }}" alt="" style="width: 50px;margin-right: 25px;">
                                    </span>
                                    <span class="tb-lead">
                                        {{ $wonitem->item }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $wonitem->server }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        @if($wonitem->issued == 1){{ __('Выдано') }}@else{{ __('Не выдано') }}@endif
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        {{ $wonitem->user->steam_trade_url }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $wonitem->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="dropdown">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-plain">
                                                <li><a href="{{ route('bonuses.issued', $wonitem) }}">{{ __('Отметить выданным') }}</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-inner">
                        {{ $wonitems->links('backend.layouts.pagination.main') }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
