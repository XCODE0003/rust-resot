@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Новости'))
@section('headerTitle', __('Новости'))
@section('headerDesc', __('Редактирование новостей.'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner" style="display: flex; justify-content: space-between;">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <a href="{{ route('articles.create') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Добавить новость') }}</span>
                                </a>
                            </h5>
                        </div>
                        <div class="card-title-group">
                            <h5 class="card-title" style="margin-right: 30px">
                                <a href="{{ route('articles.index') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Сбросить фильтр') }}</span>
                                </a>
                            </h5>
                            <div style="width: 200px; margin-right: 14px;">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control"
                                               id="date" name="date"
                                               @if(request()->has('date') && request()->get('date') != '')
                                                value="{{ str_replace(' ', 'T', date('Y-m-d', strtotime(request()->query('date')))) }}"
                                               @else
                                                value=""
                                               @endif
                                        >
                                    </div>
                                </div>
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
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($articles as $article)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        @php
                                            $title = "title_" .app()->getLocale();
                                            $description = "description_" .app()->getLocale();
                                        @endphp
                                        <a href="{{ route('articles.edit', $article) }}" target="_blank">
                                            @if ($article->$title)
                                                {{ $article->$title }}
                                            @else
                                                {{ $article->$description }}
                                            @endif
                                        </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $article->sort }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $article->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="dropdown">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-plain">
                                                <li><a href="{{ route('articles.edit', $article) }}">{{ __('Редактировать') }}</a></li>
                                                <form action="{{ route('articles.destroy', $article) }}" method="POST">
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
                        {{ $articles->links('backend.layouts.pagination.cabinet') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            $('#date').on('change', function() {
                document.location.replace('{{ route('articles.index') }}?date='+$('#date').val());
            });
        });
    </script>
@endpush