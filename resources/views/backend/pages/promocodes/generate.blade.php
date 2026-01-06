@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Сгенерировать Промокоды'))
@section('headerDesc', __('Сгенерировать Промокоды') . '.')

@section('headerTitle', __('Промокоды'))

@php
    $title = "title_" .app()->getLocale();
    $name = "name_" . app()->getLocale();
@endphp

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="{{ route('promocodes.generate_store') }}"
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
                                        <label class="form-label" for="title">{{ __('Общий Заголовок') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="title" name="title"
                                                   value="{{ old('title') }}" required>
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="amount">{{ __('Количество Промокодов') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="1" max="1000" class="form-control" id="amount" name="amount"
                                                   value="{{ old('amount') }}" required>
                                            @error('amount')
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
                                        <label class="form-label" for="date_start">{{ __('Дата начала') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="datetime-local" class="form-control @error('date_start') is-invalid @enderror"
                                                   id="date_start" name="date_start"
                                                   value="{{ old('date_start') }}" required
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
                                                   value="{{ old('date_end') }}" required
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
                                        <label class="form-label" for="type">{{ __('Тип Промокода') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="type" name="type" class="form-select">
                                                <option value="2" selected>{{ __('Одноразовый') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="type_reward">{{ __('Тип награды') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="type_reward" name="type_reward" class="form-select">
                                                <option value="1">{{ __('VIP') }}</option>
                                                <option value="2">{{ __('Бонус пополнения') }}</option>
                                                <option value="4">{{ __('Кейс') }}</option>
                                                <option value="5">{{ __('Товары магазина') }}</option>
                                                <option value="6">{{ __('% к пополнению') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6" id="premium_period-form">
                                    <div class="form-group">
                                        <label class="form-label" for="premium_period">{{ __('Период VIP') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="premium_period" name="premium_period" class="form-select">
                                                <option value="3">{{ __('3 days') }}</option>
                                                <option value="14">{{ __('14 days') }}</option>
                                                <option value="30">{{ __('30 days') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="bonus_amount-form" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="bonus_amount">{{ __('Сумма Бонуса') }}, $</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="0" step="0.1" class="form-control" id="bonus_amount" name="bonus_amount" value="0" required>
                                            @error('bonus_amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="case_id-form" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="case_id">{{ __('Кейс') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="case_id" name="case_id" class="form-select">
                                                @foreach($cases as $case)
                                                    <option value="{{ $case->id }}">{{ $case->$title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="shop_item-form" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="shop_item_id">{{ __('Товар магазина') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="shop_item_id" name="shop_item_id" class="form-select">
                                                <option value="0">{{ __('Не выбрано') }}</option>
                                                @foreach($shopitems as $shopitem)
                                                    <option value="{{ $shopitem->id }}">{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" id="shop_item-form2" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="variation_id">{{ __('Вариация товара') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="variation_id" name="variation_id" class="form-select">
                                                <option value="0">{{ __('Не выбрано') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" id="bonus_amount_percent-form" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="bonus_amount">{{ __('Процент бонуса к сумме пополнения') }}, %</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="0" max="100" step="0.1" class="form-control" id="bonus_amount" name="bonus_amount" value="0" required>
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
                if ($(this).find('option:selected').val() == '1') {
                    $('#case_id-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#premium_period-form').show();
                } else if ($(this).find('option:selected').val() == '2') {
                    $('#case_id-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#premium_period-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_amount-form').show();
                } else if ($(this).find('option:selected').val() == '3') {
                    $('#premium_period-form').hide();
                    $('#case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#bonus_case_id-form').show();
                } else if ($(this).find('option:selected').val() == '4') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#case_id-form').show();
                } else if ($(this).find('option:selected').val() == '5') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#case_id-form').hide();
                    $('#bonus_amount_percent-form').hide();
                    $('#shop_item-form').show();
                    $('#shop_item-form2').show();
                } else if ($(this).find('option:selected').val() == '6') {
                    $('#premium_period-form').hide();
                    $('#bonus_case_id-form').hide();
                    $('#bonus_amount-form').hide();
                    $('#case_id-form').hide();
                    $('#shop_item-form').hide();
                    $('#shop_item-form2').hide();
                    $('#bonus_amount_percent-form').show();
                }
            });

            $('#generate-code').on('click', function () {
                let code = '';
                let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                for (var i = 0; i < 20; i++)
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