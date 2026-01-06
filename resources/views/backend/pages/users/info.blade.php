@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Пользователи'))
@section('headerTitle', __('Пользователи'))
@section('headerDesc', $user->name)

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <span class="mr-2">{{ __('Информация о пользователе') }} {{ $user->name }}</span>
                            </h5>
                        </div>
                    </div>

                    <div class="card-inner p-0 border-top card-userinfo">

                        <div class="nk-reply-item">
                            <div class="nk-reply-header">
                                <div class="user-card flex-column align-items-start">
                                    <p><span class="bold">{{ __('Email') }}:</span> {{ $user->email }}</p>
                                    <p><span class="bold">{{ __('Steam ID') }}:</span> {{ $user->steam_id }}</p>
                                    <p><span class="bold">{{ __('Дата создания') }}:</span> {{ $user->created_at->format('d.m.Y') }}</p>
                                </div>
                            </div>
                        </div>

                        {{--
                        <div class="nk-reply-item nk-reply-server">
                            <div class="nk-reply-header-bc">
                                <div class="user-card flex-row align-items-start">
                                    @foreach($data as $server)
                                    <div class="server-block">
                                        <div class="server-header">{{ __('Сервер') }} : {{ $server['server']->name }}</div>
                                        @if (count($server['accounts']) > 0)
                                        @foreach($server['accounts'] as $account)
                                            <div class="account-block">

                                                <div class="account-info-block">
                                                <div class="account-header-block">
                                                <div class="account-header">{{ __('Аккаунт') }} : {{ $account }}</div>

                                                @if (count($server['characters']) > 0)
                                                    @php $char_isset = false; @endphp

                                                    @foreach($server['characters'] as $character)
                                                        @if (strtolower($character->account_name) == strtolower($account))
                                                            @php $char_isset = true; @endphp
                                                        @endif
                                                    @endforeach

                                                    @foreach($server['characters'] as $character)
                                                        @if (strtolower($character->account_name) == strtolower($account))
                                                        <div class="character-block">
                                                            <div class="character-header">{{ __('Персонаж') }} : {{ $character->char_name }}</div>
                                                        </div>
                                                        @endif
                                                        @if ($char_isset === false)
                                                                @php $char_isset = true; @endphp
                                                            <div class="character-block">
                                                                {{ __('Нет персонажей') }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <div class="character-block">
                                                        {{ __('Нет персонажей') }}
                                                    </div>
                                                @endif
                                                </div>
                                            </div>

                                            </div>
                                            @endforeach
                                                @else
                                                    <div class="account-block">
                                                        {{ __('Нет аккаунтов') }}
                                                    </div>
                                                @endif
                                    </div>
                                        @endforeach
                                </div>
                            </div>
                        </div>
                        --}}

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection