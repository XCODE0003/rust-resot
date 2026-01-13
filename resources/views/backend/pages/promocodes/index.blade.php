@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Промокоды'))
@section('headerTitle', __('Промокоды'))
@section('headerDesc', __('Редактирование Промокодов') . '.')

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <a href="{{ route('promocodes.create') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Добавить Промокод') }}</span>
                                </a>
                                <a href="{{ route('promocodes.generate') }}" class="btn btn-sm btn-primary">
                                    <em class="icon ni ni-plus-c mr-sm-1"></em>
                                    <span class="d-none d-sm-inline">{{ __('Сгенерировать Промокоды') }}</span>
                                </a>
                            </h5>
                            <div class="form-control-wrap" style="width: 206px; margin-left: 15px;">
                                <select class="form-select form-control" id="status" name="status">
                                    <option value="1" @if(request()->query('status') == '1') selected @endif>{{ __('Активные') }}</option>
                                    <option value="2" @if(request()->query('status') == '2') selected @endif>{{ __('Использованные') }}</option>
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

                    @if(!empty($promocodes))
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($promocodes as $promocode)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        <a href="{{ route('promocodes.edit', $promocode) }}" target="_blank">
                                            {{ $promocode->title }}
                                        </a>
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $promocode->code }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        @if($promocode->type_reward == 1) {{ __('VIP') }}
                                        @elseif($promocode->type_reward == 2) {{ __('Бонус пополнения') }}
                                        @elseif($promocode->type_reward == 3) {{ __('Открыть кейс') }}
                                        @elseif($promocode->type_reward == 4) {{ __('Кейс') }}
                                        @elseif($promocode->type_reward == 5) {{ __('Товар магазина') }}
                                        @elseif($promocode->type_reward == 6) {{ __('% к пополнению') }}
                                        @elseif($promocode->type_reward == 7) {{ __('Сумма на баланс') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        @if($promocode->type == 2)
                                            {{ __('Одноразовый') }}
                                        @elseif($promocode->type == 3)
                                            {{ __('Многоразовый') }}
                                        @else
                                            {{ __('Публичный') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $promocode->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="dropdown">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-opt">
                                                <li><a href="{{ route('promocodes.show', $promocode) }}">{{ __('Информация') }}</a></li>
                                                <li><a href="{{ route('promocodes.edit', $promocode) }}">{{ __('Редактировать') }}</a></li>
                                                <form action="{{ route('promocodes.destroy', $promocode) }}" method="POST">
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
                        {{ $promocodes->links('backend.layouts.pagination.promocodes') }}
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
        $(document).ready(function () {
            $(document).on('change', '#status', function() {
                document.location.replace('{{ route('promocodes.index') }}?status='+$('#status').val()+'&search={{request()->query('search')}}');
            });
        });
    </script>
@endpush