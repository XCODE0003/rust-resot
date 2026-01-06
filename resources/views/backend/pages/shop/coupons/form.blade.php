@extends('backend.layouts.backend')

@isset($shopcoupon)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать купон'))
    @section('headerDesc', __('Редактирование купона.'))

    @php
        if(isset($shopcoupon) && $shopcoupon->items !== NULL) {
            $items = json_decode($shopcoupon->items);
            $item = $items[0];
        }
    @endphp

@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить купон'))
    @section('headerDesc', __('Добавление купона.'))
@endisset

@section('headerTitle', __('Купоны'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($shopcoupon){{ route('shopcoupons.update', $shopcoupon) }}@else{{ route('shopcoupons.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($shopcoupon)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                                <input type="hidden" name="id" value="{{ $shopcoupon->id }}">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="title">{{ __('Заголовок') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="title" name="title"
                                                   @isset($shopcoupon) value="{{ $shopcoupon->title }}" @else value="{{ old('title') }}" @endisset required>
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
                                        <label class="form-label" for="code">{{ __('Код купона') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="code" name="code"
                                                   @isset($shopcoupon) value="{{ $shopcoupon->code }}" @else value="{{ old('code') }}" @endisset required>
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
                                                   @isset($shopcoupon) value="{{ str_replace(' ', 'T', date('Y-m-d H:i', strtotime($shopcoupon->date_start))) }}" @else value="{{ old('date_start') }}" @endisset required
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
                                                   @isset($shopcoupon) value="{{ str_replace(' ', 'T', date('Y-m-d H:i', strtotime($shopcoupon->date_end))) }}" @else value="{{ old('date_end') }}" @endisset required
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
                                        <label class="form-label" for="type">{{ __('Тип купона') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="type" name="type" class="form-select">
                                                <option value="0" @if(isset($shopcoupon) && $shopcoupon->type == 0) selected @endif>{{ __('Персональный') }}</option>
                                                <option value="1" @if(isset($shopcoupon) && $shopcoupon->type == 1) selected @endif>{{ __('Публичный') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="percent">{{ __('Скидка') }} (%)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="percent" name="percent"
                                                   @isset($shopcoupon) value="{{ $shopcoupon->percent }}" @else value="{{ old('percent') }}" @endisset required>
                                            @error('percent')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6" id="user_id-block" @if(isset($shopcoupon) && $shopcoupon->type == 1) style="display: none" @endif>
                                    <div class="form-group">
                                        <label class="form-label" for="user_id">{{ __('ID Пользователя') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="user_id" name="user_id"
                                                   @isset($shopcoupon) value="{{ $shopcoupon->user_id }}" @else value="{{ old('user_id') }}" @endisset>
                                            @error('user_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="margin-bottom-50"></div>
                            <div id="items">
                                <div class="payments-title">{{ __('Бонусный предмет') }}</div>
                                <div class="g-4 item" data-step="" id="item_1">
                                    <div class="row g-4">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="item_1_id">{{ __('Предмет') }} (ID)</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="1" class="form-control" id="item_1_id" name="item_1_id" @isset($item) value="{{ $item->id }}" @else value="" @endisset>
                                                </div>
                                                @if(session()->has('item_err_id'))
                                                    <div class="invalid-feedback">
                                                        {{ session()->get('item_err_id') }}
                                                    </div>
                                                    @php
                                                        session()->forget('item_err_id');
                                                    @endphp
                                                @endif

                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="item_1_name">{{ __('Название предмета') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="item_1_name" name="item_1_name"  @isset($item) value="{{ $item->name }}" @else value="" @endisset>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="item_1_amount">{{ __('Количество') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="1" max="999" class="form-control" id="item_1_amount" name="item_1_amount" @isset($item) value="{{ $item->amount }}" @else value="" @endisset>
                                                </div>
                                            </div>
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
                console.log($(this).find('option:selected').val());
                if ($(this).find('option:selected').val() == '1') {
                    $('#user_id-block').hide();
                } else {
                    $('#user_id-block').show();
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
    </script>
@endpush