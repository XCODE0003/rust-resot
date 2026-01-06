@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Кейсы'))
@section('headerTitle', __('Кейсы'))
@section('headerDesc', __('Редактирование кейсов.'))
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
                                <a href="{{ route('casesitems.index') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-folder-list mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Предметы') }}</span>
                                </a>
                                <a href="{{ route('cases.create') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Добавить кейс') }}</span>
                                </a>
                            </h5>
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

                    @if(!empty($cases))
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">

                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Кейс') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Название') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Сервер') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Дата создания') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Порядок сортировки') }}</span></div>
                                <div class="nk-tb-col tb-col-md" style="width: 350px;"><span class="sub-text"></span></div>
                            </div>

                            @foreach($cases as $case)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        <img src="{{ $case->image_url }}" alt="{{ $case->id }}" style="max-width: 32px;">
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        <a href="{{ route('cases.edit', $case) }}" target="_blank">
                                                {{ $case->$title }}
                                        </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ isset(getserver($case->server)->name) ? getserver($case->server)->name : '-' }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $case->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $case->sort }}
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="dropdown">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-plain">
                                                <li><a href="{{ route('cases.edit', $case) }}">{{ __('Редактировать') }}</a></li>
                                                <li><a href="{{ route('cases.duplicate', $case) }}">{{ __('Дублировать') }}</a></li>
                                                <form action="{{ route('cases.destroy', $case) }}" method="POST">
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
                        {{ $cases->links('layouts.pagination.cabinet') }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
