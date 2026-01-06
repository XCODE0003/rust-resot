@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Пользователи'))
@section('headerTitle', __('Пользователи'))
@section('headerDesc', __('Список пользователей.'))

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <div class="card-tools" style="display: flex; flex-direction: row;">
                                <div class="form-control-wrap" style="width: 150px;">
                                    <select class="form-select form-control" id="mute_status" name="mute_status">
                                        <option value="0" @if(request()->query('mute_status') == 0) selected @endif>{{ __('Все') }}</option>
                                        <option value="1" @if(request()->query('mute_status') == 1) selected @endif>{{ __('Замутенные') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-tools" style="display: flex; flex-direction: row;">
                                <div class="form-control-wrap" style="width: 150px;">
                                    <select class="form-select form-control" id="role" name="role">
                                        <option value="all" @if(request()->query('role') == 'all') selected @endif>{{ __('Все') }}</option>
                                        <option value="admin" @if(request()->query('role') == 'admin') selected @endif>{{ __('Admin') }}</option>
                                        <option value="support" @if(request()->query('role') == 'support') selected @endif>{{ __('Support') }}</option>
                                        <option value="analyst" @if(request()->query('role') == 'analyst') selected @endif>{{ __('Analyst') }}</option>
                                        <option value="user" @if(request()->query('role') == 'user') selected @endif>{{ __('User') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-tools" style="display: flex; flex-direction: row;">
                                <form method="GET" style="margin-right: 15px;">

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
                        <div class="nk-tb-list nk-tb-ulist is-compact">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Пользователь') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Роль') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Мут') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Баланс') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Дата регистрации') }}</span></div>
                                <div class="nk-tb-col tb-col-md" style="width: 350px;"><span class="sub-text"></span></div>
                            </div>
                            <!-- .nk-tb-item -->
                            @foreach($users as $user)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-avatar xs bg-primary">
                                                <span class="text-uppercase"> {{ substr(trim($user->name), 0, 2) }} </span>
                                            </div>
                                            <div class="user-name">
                                                <span class="tb-lead">{{ $user->name }}</span>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col"> <span>{{ ($user->role == 'investor') ? 'analyst' : $user->role }}</span> </div>
                                    <div class="nk-tb-col"> <span>{{ ($user->mute == 1) ? date('d-m-Y H:i:s', strtotime($user->mute_date)) : '-' }}</span> </div>
                                    <div class="nk-tb-col"> <span>{{ $user->balance }} {{ __('руб.') }}</span> </div>
                                    <div class="nk-tb-col tb-col-md"> <span>{{ $user->created_at->format('d.m.Y H:i') }}</span> </div>

                                    <div class="nk-tb-col nk-tb-col-tools users-set" style="float: right;justify-content: flex-end;width: 100%;max-width: 350px;">

                                        <div class="coin-set">
                                            <a href="{{ route('backend.user.details', $user) }}" class="btn btn-sm btn-icon btn-trigger getinfo" title="{{ __('Информация') }}" data-username="{{ $user->name }}" data-userid="{{ $user->id }}">
                                                <em class="icon ni ni-info ml-1"></em>
                                            </a>
                                        </div>
                                        <div class="coin-set">
                                            <a class="btn btn-sm btn-icon btn-trigger setcoin" title="{{ __('Начислить баланс') }}" data-username="{{ $user->name }}" data-userid="{{ $user->id }}">
                                                <em class="icon ni ni-coins ml-1"></em>
                                            </a>
                                        </div>

                                        <div class="drodown" style="margin-right: 20px;">
                                            <a href="#"
                                               class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown" title="{{ __('Мут') }}">
                                                <em class="icon ni ni-account-setting"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a class="setmute" data-username="{{ $user->name }}" data-userid="{{ $user->id }}">
                                                            <em class="icon ni ni-user-cross"></em>
                                                            <span>{{ __('Мут') }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('user.unmute', $user) }}">
                                                            <em class="icon ni ni-user-check"></em>
                                                            <span>{{ __('Размут') }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="drodown">
                                            <a href="#"
                                               class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown" title="{{ __('Назначить роль') }}">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a href="{{ route('user.role.admin', $user) }}">
                                                            <em class="icon ni ni-inbox"></em>
                                                            <span>{{ __('Администратор') }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('user.role.investor', $user) }}">
                                                            <em class="icon ni ni-inbox"></em>
                                                            <span>{{ __('Аналитик') }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('user.role.support', $user) }}">
                                                            <em class="icon ni ni-inbox"></em>
                                                            <span>{{ __('Поддержка') }}</span>
                                                        </a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a href="{{ route('user.role.user', $user) }}">
                                                            <em class="icon ni ni-inbox"></em>
                                                            <span>{{ __('Пользователь') }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal fade zoom" tabindex="-1" id="PopupBalance">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </a>
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('Изменить баланс пользователю ') }} - <span id="u_name"></span></h5>
                                </div>
                                <form method="POST" action="{{ route('user.balance.set') }}">
                                    @csrf
                                    <input id="balance_user_id" name="user_id" type="hidden" value="">

                                    <div class="modal-body">

                                        <div class="form-group">
                                            <label class="form-label" for="amount">{{ __('Введите количество') }} {{ __('руб.') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" min="0" step="0.01" class="form-control" id="balance" name="balance"
                                                       value="{{ old('balance') ? old('balance') : '0' }}">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="submit" class="btn btn-lg btn-primary">{{ __('Начислить') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="popup-mute" class="popup-balance-block">

                        <div class="nk-block">
                            <div class="row g-gs">
                                <div class="col-12">
                                    <div class="card card-bordered">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <h5 class="card-title">
                                                    <span class="mr-2">{{ __('Выдать мут пользователю') }} <span id="u_name"></span></span>
                                                </h5>
                                                <span class="popup-close" onClick="$('#popup-mute').hide();">x</span>
                                            </div>
                                        </div>
                                        <div class="card-inner border-top">
                                            <form action="{{ route('user.mute') }}" method="POST">
                                                @csrf
                                                <input id="mute_user_id" name="user_id" type="hidden" value="">

                                                <div class="row g-4">
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <label class="form-label" for="mute_date">{{ __('Выберите период мута') }}</label>
                                                                <div class="form-control-wrap">
                                                                    <select id="mute_date" name="mute_date" class="form-select">
                                                                        <option value="0">{{ __('День') }}</option>
                                                                        <option value="1">{{ __('Неделя') }}</option>
                                                                        <option value="2">{{ __('Месяц') }}</option>
                                                                        <option value="3">{{ __('Навсегда') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="mute_reason">{{ __('Укажите причину') }}</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="mute_reason" name="mute_reason"
                                                                       value="{{ old('mute_reason') }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    </div>

                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-lg btn-primary">{{ __('Выдать') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-inner">
                        {{ $users->links('layouts.pagination.cabinet') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            $('.setcoin').on('click', function () {
                $('#balance_user_id').val($(this).data('userid'));
                $('#u_name').text($(this).data('username'));
                $('#PopupBalance').modal('show');
                return false;
            });

            $('.setmute').on('click', function () {
                $('#popup-mute').show();
                $('#mute_user_id').val($(this).data('userid'));
                $('#u_name').text($(this).data('username'));

                console.log($(this).data('username'));
            });
            $('#mute_status').on('change', function() {
                document.location.replace('{{ route('users') }}?mute_status='+this.value+'&role='+$('#role').val());
            });
            $('#role').on('change', function() {
                document.location.replace('{{ route('users') }}?mute_status='+$('#mute_status').val()+'&role='+this.value);
            });
        });
    </script>
    <!-- .nk-block -->
@endsection
