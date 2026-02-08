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
                                    @if($promocode->max_activations !== NULL && $promocode->max_activations > 0)
                                        @php
                                            $remaining = $promocode->max_activations - $used_count;
                                        @endphp
                                        <p><span class="bold">{{ __('Максимум активаций') }}:</span> {{ $promocode->max_activations }}</p>
                                        <p><span class="bold">{{ __('Осталось активаций') }}:</span>
                                            <span class="badge badge-{{ $remaining > 0 ? 'success' : 'danger' }}">
                                                {{ $remaining }}
                                            </span>
                                        </p>
                                    @else
                                        <p><span class="bold">{{ __('Максимум активаций') }}:</span> <span class="text-muted">{{ __('Без ограничений') }}</span></p>
                                    @endif
                                    <p><span class="bold">{{ __('Дата создания') }}:</span> {{ $promocode->created_at->format('d.m.Y') }}</p>

                                    @if($promocode->public_uuid)
                                    <div class="mt-3 pt-3 border-top">
                                        <p><span class="bold">{{ __('Публичная статистика') }}:</span></p>
                                        <div class="input-group" style="max-width: 500px;">
                                            <input type="text" class="form-control" id="public-url" value="{{ route('promo.public', ['uuid' => $promocode->public_uuid]) }}" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" onclick="copyPublicUrl()">
                                                    <em class="icon ni ni-copy"></em> {{ __('Копировать') }}
                                                </button>
                                                <a href="{{ route('promo.public', ['uuid' => $promocode->public_uuid]) }}" target="_blank" class="btn btn-outline-secondary">
                                                    <em class="icon ni ni-external"></em> {{ __('Открыть') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        function copyPublicUrl() {
                                            var copyText = document.getElementById("public-url");
                                            copyText.select();
                                            copyText.setSelectionRange(0, 99999);
                                            navigator.clipboard.writeText(copyText.value);
                                            alert('{{ __("Ссылка скопирована!") }}');
                                        }
                                    </script>
                                    @endif
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
