@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Промокоды'))
@section('headerTitle', __('Промокоды'))
@section('headerDesc', $promocode->title)

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <span class="mr-2">{{ __('Информация о Промокоде') }} {{ $promocode->title }}</span>
                            </h5>
                        </div>
                    </div>

                    <div class="card-inner p-0 border-top card-userinfo">

                        <div class="nk-reply-item">
                            <div class="nk-reply-header">
                                <div class="user-card flex-column align-items-start">
                                    <p><span class="bold">{{ __('Код') }}:</span> {{ $promocode->code }}</p>
                                    <p><span class="bold">{{ __('Тип Промокода') }}:</span>
                                        @if($promocode->type == 2)
                                            {{ __('Одноразовый') }}
                                        @elseif($promocode->type == 3)
                                            {{ __('Многоразовый') }}
                                        @else
                                            {{ __('Публичный') }}
                                        @endif
                                    </p>
                                    <p><span class="bold">{{ __('Тип Награды') }}:</span>
                                        @if($promocode->type_reward == 1)
                                            {{ __('VIP') }}
                                        @elseif($promocode->type_reward == 2)
                                            {{ __('Бонус пополнения') }}
                                        @elseif($promocode->type_reward == 3)
                                            {{ __('Открытие кейса') }}
                                        @else
                                            {{ '-' }}
                                        @endif
                                    </p>
                                    <p><span class="bold">{{ __('Время действия') }}:</span> {{ $promocode->date_start }} - {{ $promocode->date_end }}</p>
                                    <p><span class="bold">{{ __('Количество использований') }}:</span> {{ $used_count }}</p>
                                    <p><span class="bold">{{ __('Дата создания') }}:</span> {{ $promocode->created_at->format('d.m.Y') }}</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-ulist is-compact">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Пользователь') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Дата использования') }}</span></div>
                            </div>
                            <!-- .nk-tb-item -->
                            @if($used_users !== NULL && is_array($used_users))
                                @foreach($used_users as $user)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-name">
                                                    <span class="tb-lead"><a href="{{ route('backend.user.details', $user->user_id) }}">{{ getuser($user->user_id)->name }} ({{ getuser($user->user_id)->email }})</a></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="nk-tb-col tb-col-md" style="font-size: 12px;"> <span>{{ $user->date }}</span> </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
