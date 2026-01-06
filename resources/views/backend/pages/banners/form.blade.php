@extends('backend.layouts.backend')

@isset($banner)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать баннер'))
    @section('headerDesc', __('Редактирование баннера.'))

@php
    $banners = json_decode($banner->banners);
@endphp
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить баннер'))
    @section('headerDesc', __('Добавление баннера.'))
@endisset

@section('headerTitle', __('Баннеры'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($banner){{ route('banners.update', $banner) }}@else{{ route('banners.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($banner)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="path">{{ __('Путь страницы') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="path" name="path" @isset($banner->path) value="{{ $banner->path }}" @else value="{{ old('path') }}" @endisset required>
                                            @error('path')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-items section-title" style="margin-top: 20px; padding-top: 10px;">
                                <hr>
                                <span>{{ __('Баннеры') }}</span>
                            </div>

                            <!-- baneriarities -->
                            <div class="col-items">
                                <div class="margin-bottom-50"></div>
                                <div id="baners">
                                    <div class="g-4 baner" data-baner="" id="baner_" style="display: none;">
                                        <div class="row g-4">
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label class="form-label" for="baner__image">{{ __('Изображение') }}</label>
                                                    <div class="custom-file">
                                                        <input class="custom-file-input" id="baner__image" name="baner__image" type="file" >
                                                        <label class="custom-file-label" for="baner__image">{{ __('Изображение') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label class="form-label" for="baner__sort">{{ __('Порядок сортировки') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="baner__sort"
                                                               name="baner__sort" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group delete-bonus">
                                                    <a class="btn delete" data-donat="baner_" onClick="deletebaner('baner_')">{{ __('Удалить баннер') }}</a>
                                                </div>
                                            </div>

                                        </div>

                                    </div>


                                    @isset($banners)
                                        @foreach($banners as $banner_item)

                                            <div class="g-4 baner" data-baner="{{ $loop->iteration }}" id="baner_{{ $loop->iteration }}">
                                                <div class="row g-4">
                                                    <div class="col-lg-5">
                                                        <div class="form-group">
                                                            <input type="hidden" name="baner_{{ $loop->iteration }}_image_old" value="{{ $banner_item->image }}">
                                                            <label class="form-label" for="baner_{{ $loop->iteration }}_image">{{ __('Изображение') }} ({{ $loop->iteration }}) <img style="width: 30px;" src="/storage/{{ $banner_item->image }}"/></label>
                                                            <div class="custom-file">
                                                                <input class="custom-file-input" id="baner_{{ $loop->iteration }}_image" name="baner_{{ $loop->iteration }}_image" type="file" value="{{ $banner_item->image }}">
                                                                <label class="custom-file-label" for="baner_{{ $loop->iteration }}_image">{{ __('Изображение') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5">
                                                        <div class="form-group">
                                                            <label class="form-label" for="baner_{{ $loop->iteration }}_sort">{{ __('Порядок сортировки') }} ({{ $loop->iteration }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="baner_{{ $loop->iteration }}_sort"
                                                                       name="baner_{{ $loop->iteration }}_sort" value="{{ $banner_item->sort }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group delete-bonus">
                                                            <a class="btn delete" data-donat="baner_{{ $loop->iteration }}" onClick="deletebaner('baner_{{ $loop->iteration }}')">{{ __('Удалить баннер') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    @endforeach
                                    @endisset
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="form-group add-bonus">
                                            <a class="btn add addbaner">{{ __('Добавить баннер') }}</a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- end baneriarities --}}


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

            let baner_id = 1;
            let baner_id_next = 1;
            let baner_html = '';
            let baner_sear = '';
            let baner_repl = '';
            $('.addbaner').on('click', function(){
                baner_id = $('.baner:last').data('baner');
                baner_id_next = baner_id + 1;
                baner_id = '';
                baner_sear = new RegExp('baner_' + baner_id, 'g');
                baner_repl = 'baner_' + baner_id_next;
                baner_html = $('#baner_'+baner_id).html().replace(baner_sear,baner_repl);
                baner_sear = new RegExp('{{ __("Изображение") }} ' + baner_id, 'g');
                baner_html = baner_html.replace(baner_sear,'{{ __("Изображение") }} ' + baner_id_next);

                $('#baners').append('<div class="g-4 baner" data-baner="'+baner_id_next+'" id="baner_' + baner_id_next + '">' + baner_html + '</div>');
                $('#baner_'+baner_id_next+'_sort').val(baner_id_next);
            });

        });

        function deletebaner(baner){
            $('#'+baner).remove();
        }
    </script>
@endpush