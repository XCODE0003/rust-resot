@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Видео'))
@section('headerTitle', __('Видео'))
@section('headerDesc', __('Редактирование Видео.'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <a href="{{ route('videos.create') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Добавить видео') }}</span>
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
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($videos as $video)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        ({{ $video->language }}) <a href="https://www.youtube.com/watch?v={{ $video->url }}" target="_blank">{{ $video->title }}</a>
                                    </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $video->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    </div>
                                    <div class="nk-tb-col nk-tb-col-action">
                                        <div class="dropdown">
                                            <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-plain">
                                                    <li><a href="{{ route('videos.edit', $video) }}">{{ __('Редактировать') }}</a></li>
                                                    <form action="{{ route('videos.destroy', $video) }}" method="POST">
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
                        {{ $videos->links('layouts.pagination.cabinet') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
