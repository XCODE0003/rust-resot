@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Bonus Gift for Monday settings.'))

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="bonusm_status">{{ __('Статус') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="bonusm_status" name="setting_bonusm_status" class="form-select">
                                                <option value="0" @if(config('options.bonusm_status', '0') == '0') selected @endif>{{ __('Отключено') }}</option>
                                                <option value="1" @if(config('options.bonusm_status', '0') == '1') selected @endif>{{ __('Включено') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="bonusm_online_amount">{{ __('Количество часов онлайна') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="bonusm_online_amount" name="setting_bonusm_online_amount" value="{{ config('options.bonusm_online_amount', '100') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            <!-- Tabs -->
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="tab">
                                        @foreach(getlangs() as $key => $value)
                                            <a class="tablinks @if($loop->index == 0) active @endif" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Tab content -->
                                @foreach(getlangs() as $key => $value)
                                    <div id="{{ $key }}" class="tabcontent"
                                         @if($loop->index == 0) style="display: block" @endif>
                                        <div class="row g-4">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="bonusm_description_{{ $key }}">{{ __('Информация') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                            <textarea type="text" class="form-control" id="bonusm_description_{{ $key }}" name="setting_bonusm_description_{{ $key }}">{{ config('options.bonusm_description_'.$key) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            @endforeach



                            <div class="col-items section-title" style="margin-top: 20px; padding-top: 10px;">
                                <hr>
                                <span>{{ __('Предметы') }}</span>
                            </div>

                            <!-- peculiarities -->
                            <div class="col-items">
                                <div class="margin-bottom-50"></div>
                                <div id="peculs">
                                    <div class="g-4 pecul" data-pecul="" id="pecul_" style="display: none;">
                                        <div class="row g-4">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="bonusm_pecul__title">{{ __('Skin ID') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="bonusm_pecul__title"
                                                               name="setting_bonusm_pecul__title" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="bonusm_pecul__description">{{ __('Тип качества') }}</label>
                                                    <div class="form-control-wrap">
                                                        <select id="bonusm_pecul__description" name="setting_bonusm_pecul__description">
                                                            <option value="1">{{ __('Серый') }}</option>
                                                            <option value="2">{{ __('Синий') }}</option>
                                                            <option value="3">{{ __('Зелёный') }}</option>
                                                            <option value="4">{{ __('Красный') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="bonusm_pecul__chance">{{ __('Шанс выигрыша') }}, %</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" min="0" max="100" step="0.01" class="form-control" id="bonusm_pecul__chance"
                                                               name="setting_bonusm_pecul__chance" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="bonusm_pecul__available">{{ __('Доступно') }}, {{ __('шт.') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" min="0" class="form-control" id="bonusm_pecul__available"
                                                               name="setting_bonusm_pecul__available" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="bonusm_pecul__image">{{ __('Изображение') }}</label>
                                                    <div class="form-control-wrap form-input-file form-control">
                                                        <input type="file" class="custom-file-input" id="bonusm_pecul__image"
                                                               name="setting_bonusm_pecul__image" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group delete-bonus">
                                                    <a class="btn delete" data-donat="pecul_" onClick="deletepecul('pecul_')">{{ __('Удалить предмет') }}</a>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    @for($i=0;$i<200;$i++)
                                        @if (config('options.bonusm_pecul_'.$i.'_title', '') != '')

                                            <input type="hidden" name="setting_bonusm_pecul_{{ $i }}_title" value="" />
                                            <input type="hidden" name="setting_bonusm_pecul_{{ $i }}_description" value="" />
                                            <input type="hidden" name="setting_bonusm_pecul_{{ $i }}_image" value="" />

                                            <div class="g-4 pecul" data-pecul="{{ $i }}" id="pecul_{{ $i }}">
                                                <div class="row g-4">
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-label" for="bonusm_pecul_{{ $i }}_title">{{ __('Skin ID') }} ({{ $i }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="bonusm_pecul_{{ $i }}_title"
                                                                       name="setting_bonusm_pecul_{{ $i }}_title" value="{{ config('options.bonusm_pecul_'.$i.'_title', '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-label" for="bonusm_pecul_{{ $i }}_description">{{ __('Тип качества') }}</label>
                                                            <div class="form-control-wrap">
                                                                <select id="bonusm_pecul_{{ $i }}_description" name="setting_bonusm_pecul_{{ $i }}_description">
                                                                    <option value="1" @if(config('options.bonusm_pecul_'.$i.'_description', 1) == 1) selected @endif>{{ __('Серый') }}</option>
                                                                    <option value="2" @if(config('options.bonusm_pecul_'.$i.'_description', 1) == 2) selected @endif>{{ __('Синий') }}</option>
                                                                    <option value="3" @if(config('options.bonusm_pecul_'.$i.'_description', 1) == 3) selected @endif>{{ __('Зелёный') }}</option>
                                                                    <option value="4" @if(config('options.bonusm_pecul_'.$i.'_description', 1) == 4) selected @endif>{{ __('Красный') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-label" for="bonusm_pecul_{{ $i }}_chance">{{ __('Шанс выигрыша') }}, %</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="bonusm_pecul_{{ $i }}_chance"
                                                                       name="setting_bonusm_pecul_{{ $i }}_chance" value="{{ config('options.bonusm_pecul_'.$i.'_chance', '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-label" for="bonusm_pecul_{{ $i }}_available">{{ __('Доступно') }}, {{ __('шт.') }}</label>
                                                            <div class="form-control-wrap">
                                                                <input type="number" class="form-control" id="bonusm_pecul_{{ $i }}_available"
                                                                       name="setting_bonusm_pecul_{{ $i }}_available" value="{{ config('options.bonusm_pecul_'.$i.'_available', '') }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-label" for="bonusm_pecul_{{ $i }}_image">@if(config('options.bonusm_pecul_'.$i.'_image', '') != ''){{ __('Заменить изображение') }} <img style="width: 30px;" src="/storage/{{ config('options.bonusm_pecul_'.$i.'_image', '') }}"/>@else{{ __('Изображение') }}@endif</label>
                                                            <div class="form-control-wrap form-input-file form-control">
                                                                @if(config('options.bonusm_pecul_'.$i.'_image', '') != '')
                                                                    <input type="hidden" name="setting_bonusm_pecul_{{ $i }}_image_old" value="{{ config('options.bonusm_pecul_'.$i.'_image', '') }}" />
                                                                @endif
                                                                <input type="file" class="custom-file-input" id="bonusm_pecul_{{ $i }}_image"
                                                                       name="setting_bonusm_pecul_{{ $i }}_image" value="{{ config('options.bonusm_pecul_'.$i.'_image', '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group delete-bonus">
                                                            <a class="btn delete" data-donat="pecul_{{ $i }}" onClick="deletepecul('pecul_{{ $i }}')">{{ __('Удалить предмет') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endfor
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="form-group add-bonus">
                                            <a class="btn add addpecul">{{ __('Добавить предмет') }}</a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- end peculiarities --}}

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

            let pecul_id = 1;
            let pecul_id_next = 1;
            let pecul_html = '';
            let pecul_sear = '';
            let pecul_repl = '';
            $('.addpecul').on('click', function(){
                pecul_id = $('.pecul:last').data('pecul');
                pecul_id_next = pecul_id + 1;
                pecul_id = '';
                pecul_sear = new RegExp('pecul_' + pecul_id, 'g');
                pecul_repl = 'pecul_' + pecul_id_next;
                pecul_html = $('#pecul_'+pecul_id).html().replace(pecul_sear,pecul_repl);
                pecul_sear = new RegExp('{{ __("ID предмета") }} ' + pecul_id, 'g');
                pecul_html = pecul_html.replace(pecul_sear,'{{ __("ID предмета") }} ' + pecul_id_next);

                $('#peculs').append('<div class="g-4 pecul" data-pecul="'+pecul_id_next+'" id="pecul_' + pecul_id_next + '">' + pecul_html + '</div>');
                $('#pecul_'+pecul_id_next+'_sort').val(pecul_id_next);
            });

        });

        function deletepecul(pecul){
            $('#'+pecul).remove();
        }
    </script>
@endpush