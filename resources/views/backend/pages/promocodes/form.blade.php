@extends('backend.layouts.backend')

@isset($promocode)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать Промокод'))
    @section('headerDesc', __('Редактирование Промокода') . '.')

    @php
        if(isset($promocode) && $promocode->items !== NULL) {
            $items = json_decode($promocode->items);
            if(isset($items[0])) {
                $item = $items[0];
            }
        }
    @endphp

@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить Промокод'))
    @section('headerDesc', __('Добавление Промокода') . '.')
@endisset
@php
    $title = "title_" .app()->getLocale();
    $name = "name_" . app()->getLocale();
@endphp

@section('headerTitle', __('Промокоды'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($promocode){{ route('promocodes.update', $promocode) }}@else{{ route('promocodes.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($promocode)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                                <input type="hidden" name="id" value="{{ $promocode->id }}">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="title">{{ __('Заголовок') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="title" name="title"
                                                   @isset($promocode) value="{{ $promocode->title }}" @else value="{{ old('title') }}" @endisset required>
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="display: flex;">
                                    <div class="form-group" style="width: 80%;">
                                        <label class="form-label" for="code">{{ __('Код') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="code" name="code"
                                                   @isset($promocode) value="{{ $promocode->code }}" @else value="{{ old('code') }}" @endisset required>
                                            @error('code')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <span id="generate-code">{{ __('Сгенерировать') }}</span>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="date_start">{{ __('Дата начала') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="datetime-local" class="form-control @error('date_start') is-invalid @enderror"
                                                   id="date_start" name="date_start"
                                                   @isset($promocode) value="{{ str_replace(' ', 'T', date('Y-m-d H:i', strtotime($promocode->date_start))) }}" @else value="{{ old('date_start') }}" @endisset required
                                            >
                                            @error('date_start')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="date_end">{{ __('Дата окончания') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="datetime-local" class="form-control @error('date_end') is-invalid @enderror"
                                                   id="date_end" name="date_end"
                                                   @isset($promocode) value="{{ str_replace(' ', 'T', date('Y-m-d H:i', strtotime($promocode->date_end))) }}" @else value="{{ old('date_end') }}" @endisset required
                                            >
                                            @error('date_end')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="max_activations">{{ __('Максимальное количество активаций') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="0" class="form-control" id="max_activations" name="max_activations"
                                                   @isset($promocode) value="{{ $promocode->max_activations }}" @else value="{{ old('max_activations', '') }}" @endisset
                                                   placeholder="{{ __('0 = без ограничений') }}">
                                            @error('max_activations')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="type">{{ __('Тип Промокода') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="type" name="type" class="form-select">
                                                <option value="1" @if(isset($promocode) && $promocode->type == 1) selected @endif>{{ __('Публичный') }}</option>
                                                <option value="2" @if(isset($promocode) && $promocode->type == 2) selected @endif>{{ __('Одноразовый') }}</option>
                                                <option value="3" @if(isset($promocode) && $promocode->type == 3) selected @endif>{{ __('Многоразовый') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="type_reward">{{ __('Тип награды') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="type_reward" name="type_reward" class="form-select">
                                                <option value="1" @if(isset($promocode) && $promocode->type_reward == 1) selected @endif>{{ __('VIP') }}</option>
                                                <option value="2" @if(isset($promocode) && $promocode->type_reward == 2) selected @endif>{{ __('Бонус пополнения') }}</option>
                                                <option value="4" @if(isset($promocode) && $promocode->type_reward == 4) selected @endif>{{ __('Кейс') }}</option>
                                                <option value="5" @if(isset($promocode) && $promocode->type_reward == 5) selected @endif>{{ __('Товары магазина') }}</option>
                                                <option value="6" @if(isset($promocode) && $promocode->type_reward == 6) selected @endif>{{ __('% к пополнению') }}</option>
                                                <option value="7" @if(isset($promocode) && $promocode->type_reward == 7) selected @endif>{{ __('Сумма на баланс') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6" id="premium_period-form" @if(isset($promocode)) @if($promocode->type_reward == 1) @else style="display: none" @endif @else @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="premium_period">{{ __('Период VIP') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="premium_period" name="premium_period" class="form-select">
                                                <option value="3" @if(isset($promocode) && $promocode->premium_period == 3) selected @endif>{{ __('3 days') }}</option>
                                                <option value="14" @if(isset($promocode) && $promocode->premium_period == 14) selected @endif>{{ __('14 days') }}</option>
                                                <option value="30" @if(isset($promocode) && $promocode->premium_period == 30) selected @endif>{{ __('30 days') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="bonus_amount-form" @if(isset($promocode) && $promocode->type_reward == 2) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="bonus_amount">{{ __('Сумма Бонуса') }}, $</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="0" step="0.1" class="form-control" id="bonus_amount" name="bonus_amount"
                                                   @isset($promocode) value="{{ $promocode->bonus_amount }}" @else value="0" @endisset>
                                            @error('bonus_amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="bonus_case_id-form" @if(isset($promocode) && $promocode->type_reward == 3) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="bonus_case_id">{{ __('Кейс') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="bonus_case_id" name="bonus_case_id" class="form-select">
                                                    <option value="1" @if(isset($promocode) && $promocode->bonus_case_id == 1) selected @endif>{{ __('Happy New Year') }}</option>
                                                <option value="2" @if(isset($promocode) && $promocode->bonus_case_id == 2) selected @endif>{{ __('Monday') }}</option>
                                                <option value="3" @if(isset($promocode) && $promocode->bonus_case_id == 3) selected @endif>{{ __('Thursday') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="case_id-form" @if(isset($promocode) && $promocode->type_reward == 4) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="case_id">{{ __('Кейс') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="case_id" name="case_id" class="form-select">
                                                @foreach($cases as $case)
                                                    <option value="{{ $case->id }}" @if(isset($promocode) && $promocode->case_id == $case->id) selected @endif>{{ $case->$title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="shop_item-form" @if(isset($promocode) && $promocode->type_reward == 5) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="shop_item_id">{{ __('Товар магазина') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="shop_item_id" name="shop_item_id" class="form-select">
                                                <option value="0">{{ __('Не выбрано') }}</option>
                                                @foreach($shopitems as $shopitem)
                                                    <option value="{{ $shopitem->id }}" @if(isset($promocode) && $promocode->shop_item_id == $shopitem->id) selected @endif>{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" id="shop_item-form2" @if(isset($promocode) && $promocode->type_reward == 5) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="variation_id">{{ __('Вариация товара') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="variation_id" name="variation_id" class="form-select">
                                                    <option value="0">{{ __('Не выбрано') }}</option>
                                                @if(isset($promocode) && $promocode->type_reward == 5)
                                                    @foreach(getshopitemvariation($promocode->shop_item_id) as $variation)
                                                        <option value="{{ $variation->variation_id }}" @if(isset($promocode) && $promocode->variation_id == $variation->variation_id) selected @endif>{{ $variation->variation_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="bonus_amount_percent-form" @if(isset($promocode) && $promocode->type_reward == 6) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="bonus_amount_percent">{{ __('Процент бонуса к сумме пополнения') }}, %</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="0" max="100" step="0.1" class="form-control" id="bonus_amount_percent" name="bonus_amount"
                                                   @isset($promocode) value="{{ $promocode->bonus_amount }}" @else value="0" @endisset>
                                            @error('bonus_amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" id="bonus_amount_balance-form" @if(isset($promocode) && $promocode->type_reward == 7) @else style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="bonus_amount_balance">{{ __('Сумма на баланс') }}, $</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="0" step="0.1" class="form-control" id="bonus_amount_balance" name="bonus_amount"
                                                   @isset($promocode) value="{{ $promocode->bonus_amount }}" @else value="0" @endisset>
                                            @error('bonus_amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary ml-auto">{{ __('Отправить') }}</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#type').on('change', function () {
                if ($(this).find('option:selected').val() == '1') {
                    $('#user_id-block').hide();
                    $('#type_restriction-block').show();
                } else if ($(this).find('option:selected').val() == '2') {
                    $('#user_id-block').hide();
                    $('#type_restriction-block').hide();
                } else {
                    $('#user_id-block').show();
                    $('#type_restriction-block').show();
                }
            });

            $('#type_reward').on('change', function () {
                // Включаем все поля обратно (на случай, если они были отключены)
                $('#bonus_amount, #bonus_amount_percent, #bonus_amount_balance').prop('disabled', false);
                // Убираем required со всех полей bonus_amount
                $('#bonus_amount, #bonus_amount_percent, #bonus_amount_balance').removeAttr('required');

                if ($(this).find('option:selected').val() == '1') {
                    $('#case_id-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount_balance-form').hide();
                    $('#premium_period-form').show();
                } else if ($(this).find('option:selected').val() == '2') {
                    $('#case_id-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#premium_period-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount_balance-form').hide();
                    $('#bonus_amount-form').show();
                    $('#bonus_amount_percent, #bonus_amount_balance').prop('disabled', true);
                    $('#bonus_amount').prop('disabled', false).attr('required', 'required');
                } else if ($(this).find('option:selected').val() == '3') {
                    $('#premium_period-form').hide();
                    $('#case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount_balance-form').hide();
                    $('#bonus_case_id-form').show();
                } else if ($(this).find('option:selected').val() == '4') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount_balance-form').hide();
                    $('#case_id-form').show();
                } else if ($(this).find('option:selected').val() == '5') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#case_id-form').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount_balance-form').hide();
                    $('#shop_item-form').show();
                    $('#shop_item-form2').show();
                } else if ($(this).find('option:selected').val() == '6') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#case_id-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_balance-form').hide();
                    $('#bonus_amount_percent-form').show();
                    $('#bonus_amount, #bonus_amount_balance').prop('disabled', true);
                    $('#bonus_amount_percent').prop('disabled', false).attr('required', 'required');
                } else if ($(this).find('option:selected').val() == '7') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#case_id-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount_balance-form').show();
                    $('#bonus_amount, #bonus_amount_percent').prop('disabled', true);
                    $('#bonus_amount_balance').prop('disabled', false).attr('required', 'required');
                }
            });

            // Инициализация при загрузке страницы: отключаем скрытые поля и устанавливаем required только для видимого
            var currentTypeReward = $('#type_reward').val();
            $('#bonus_amount, #bonus_amount_percent, #bonus_amount_balance').removeAttr('required');

            // Отключаем скрытые поля (disabled поля не валидируются)
            if (!$('#bonus_amount-form').is(':visible')) {
                $('#bonus_amount').prop('disabled', true);
            }
            if (!$('#bonus_amount_percent-form').is(':visible')) {
                $('#bonus_amount_percent').prop('disabled', true);
            }
            if (!$('#bonus_amount_balance-form').is(':visible')) {
                $('#bonus_amount_balance').prop('disabled', true);
            }

            // Устанавливаем required только для видимого поля
            if (currentTypeReward == '2' && $('#bonus_amount-form').is(':visible')) {
                $('#bonus_amount').prop('disabled', false).attr('required', 'required');
            } else if (currentTypeReward == '6' && $('#bonus_amount_percent-form').is(':visible')) {
                $('#bonus_amount_percent').prop('disabled', false).attr('required', 'required');
            } else if (currentTypeReward == '7' && $('#bonus_amount_balance-form').is(':visible')) {
                $('#bonus_amount_balance').prop('disabled', false).attr('required', 'required');
            }

            // Обработчик отправки формы: отключаем скрытые поля перед валидацией
            $('form').on('submit', function(e) {
                // Отключаем все скрытые поля bonus_amount (disabled поля не валидируются)
                $('#bonus_amount-form:hidden input[name="bonus_amount"]').prop('disabled', true);
                $('#bonus_amount_percent-form:hidden input[name="bonus_amount"]').prop('disabled', true);
                $('#bonus_amount_balance-form:hidden input[name="bonus_amount"]').prop('disabled', true);

                // Убираем required со всех скрытых полей на всякий случай
                $('#bonus_amount-form:hidden input[name="bonus_amount"]').removeAttr('required');
                $('#bonus_amount_percent-form:hidden input[name="bonus_amount"]').removeAttr('required');
                $('#bonus_amount_balance-form:hidden input[name="bonus_amount"]').removeAttr('required');
            });

            $('#generate-code').on('click', function () {
                let code = '';
                let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (var i = 0; i < 6; i++)
                    code += possible.charAt(Math.floor(Math.random() * possible.length));
                console.log(code);
                $('#code').val(code);
            });

        });

        $('#shop_item_id').on('change', function () {
            let sel = $(this);
            console.log(sel.val());
            $.ajax({
                type: "POST",
                url: "{{ route('shopitems.getVariations') }}",
                dataType: "json",
                data: { shopitem_id: sel.val(), _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                if (data.status == 'success') {
                    $('#variation_id').html('');
                    $.each(data.result , function() {
                        $('#variation_id').append(new Option($(this)[0].variation_name, $(this)[0].variation_id));
                    });
                }
            });
        });
    </script>

@endpush