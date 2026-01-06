@extends('backend.layouts.backend')

@isset($shopset)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать сет'))
    @section('headerDesc', __('Редактирование сета.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить сет'))
    @section('headerDesc', __('Добавление сета.'))
@endisset

@section('headerTitle', __('Сеты магазина'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($shopset){{ route('shopsets.update', $shopset) }}@else{{ route('shopsets.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($shopset)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="category_id">{{ __('Категория') }}</label>
                                        <div class="form-control-wrap">
                                            @php $title = "title_" .app()->getLocale(); @endphp
                                            <select id="category_id" name="category_id" class="form-select">
                                                @foreach(getshopcategories() as $category)
                                                    <option value="{{ $category->id }}"
                                                            @if(isset($shopset) && $shopset->category_id == $category->id) selected @endif>{{ $category->$title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="amount">{{ __('Количество') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="1" class="form-control" id="amount" name="amount"
                                                   @isset($shopset) value="{{ $shopset->amount }}" @else value="{{ old('amount') }}" @endisset required>
                                            @error('amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="server">{{ __('Сервер') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="server" name="server" class="form-select">
                                                <option value="-1" @if(isset($shopset) && $shopset->server == '-1') selected @endif>{{ __('Нет') }}</option>
                                                <option value="0" @if(isset($shopset) && $shopset->server == '0') selected @endif>{{ __('Все сервера') }}</option>
                                                @foreach(getservers() as $server)
                                                    <option value="{{ $server->id }}" @if(isset($shopset) && $shopset->server == $server->id) selected @endif>{{ $server->name }}</option>
                                                @endforeach
                                                @if(isset($shopset) && $shopset->server == 8)
                                                    <option value="8" selected>{{ __('Сервер') }} 8</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="servers">{{ __('Cервера') }}</label>
                                        <div class="form-control-wrap">
                                            @foreach(getservers() as $server)
                                                <input type="hidden" name="server_{{ $loop->iteration }}_id" value="" />
                                                <input type="checkbox" class="lang-checkbox" id="server_{{ $loop->iteration }}_id" name="server_{{ $loop->iteration }}_id" value="{{ $server->id }}" @if(isset($servers) && in_array($server->id, $servers)) checked @endif>{{ $server->name }}<br>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="price">{{ __('Цена') }} (RUB)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price" name="price"
                                                   @isset($shopset) value="{{ $shopset->price }}" @else value="{{ old('price') }}" @endisset required>
                                            @error('price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="price_usd">{{ __('Цена') }} (USD)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price_usd" name="price_usd"
                                                   @isset($shopset) value="{{ $shopset->price_usd }}" @else value="{{ old('price_usd') }}" @endisset required>
                                            @error('price_usd')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="status">{{ __('Состояние') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="status" name="status" class="form-select">
                                                <option value="0" @if(isset($shopset) && $shopset->status == '0') selected @endif>{{ __('Выключить') }}</option>
                                                <option value="1" @if(isset($shopset) && $shopset->status == '1') selected @endif>{{ __('Включить') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="can_gift">{{ __('Можно дарить') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="can_gift" name="can_gift" class="form-select">
                                                <option value="0" @if(isset($shopset) && $shopset->can_gift == '0') selected @endif>{{ __('Нет') }}</option>
                                                <option value="1" @if(isset($shopset) && $shopset->can_gift == '1') selected @endif>{{ __('Да') }}</option>
                                            </select>
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
                                @php
                                    $name = "name_" . $key;
                                    $short_description = "short_description_" . $key;
                                    $description = "description_" . $key;
                                @endphp
                                <div id="{{ $key }}" class="tabcontent" @if($loop->index == 0) style="display: block" @endif>

                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label" for="{{ $name }}">{{ __('Название') }} ({{ $key }})</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="{{ $name }}"
                                                           name="{{ $name }}"
                                                           @isset($shopset) value="{{ $shopset->$name }}"
                                                           @else value="{{ old($name) }}" @endisset>
                                                    @error($name)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="sort">{{ __('Порядок сортировки') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control @error('sort') is-invalid @enderror"
                                                   id="sort" name="sort"
                                                   @isset($shopset) value="{{ $shopset->sort }}" @else value="{{ old('sort') }}" @endisset required>
                                            @error('sort')
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
                                        <label for="image" class="form-label">@isset($shopset) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
                                        <div class="form-control-wrap">
                                            @isset($shopset)
                                                <span><img src="{{ $shopset->image_url }}" alt="{{ $shopset->id }}" width="200"></span>
                                            @endisset
                                            <div class="custom-file">
                                                <input class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" type="file" >
                                                <label class="custom-file-label" for="image">{{ __('Изображение') }}</label>
                                                @error('image')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- itemitems -->
                            <div class="col-items">
                                <div class="margin-bottom-50"></div>
                                <h6>{{ __('Предметы') }}</h6>
                                <div id="items">
                                    <div class="g-4 item" data-item="" id="item_" style="display: none;">
                                        <div class="row g-4">

                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label class="form-label" for="shop_item__id">{{ __('Предмет') }}</label>
                                                    <div class="form-control-wrap">
                                                        <select id="shop_item__id" name="shop_item__id" class="form-select case_shop" data-search="on">
                                                            <option value="0">{{ __('Не выбрано') }}</option>
                                                            @foreach($shopitems as $shopitem)
                                                                <option value="{{ $shopitem->id }}">{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label class="form-label" for="shop_item__amount">{{ __('Количество') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="number" class="form-control" id="shop_item__amount"
                                                               name="shop_item__amount" value="1">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2">
                                                <div class="form-group delete-bonus">
                                                    <a class="btn delete" data-donat="item_" onClick="deleteitem('item_')">{{ __('Удалить') }}</a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    @foreach($items as $item)
                                        <div class="g-4 item" data-item="{{ $loop->iteration }}" id="item_{{ $loop->iteration }}">
                                            <div class="row g-4">

                                                <div class="col-lg-5">
                                                    <div class="form-group">
                                                        <label class="form-label" for="shop_item_{{ $loop->iteration }}_id">{{ __('Предмет') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="shop_item_{{ $loop->iteration }}_id" name="shop_item_{{ $loop->iteration }}_id" class="form-select" data-search="on">
                                                                <option value="0">{{ __('Не выбрано') }}</option>
                                                                @foreach($shopitems as $shopitem)
                                                                    <option value="{{ $shopitem->id }}" @if(isset($item->id) && $item->id == $shopitem->id) selected @endif>{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-5">
                                                    <div class="form-group">
                                                        <label class="form-label" for="shop_item_{{ $loop->iteration }}_amount">{{ __('Количество') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" class="form-control"
                                                                   id="shop_item_{{ $loop->iteration }}_amount"
                                                                   name="shop_item_{{ $loop->iteration }}_amount" value="{{ $item->amount ?? '1' }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="form-group delete-bonus">
                                                        <a class="btn delete" data-donat="item_{{ $loop->iteration }}"
                                                           onClick="deleteitem('item_{{ $loop->iteration }}')">{{ __('Удалить') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="form-group add-bonus">
                                            <a class="btn add additem">{{ __('Добавить') }}</a>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            {{-- end items --}}

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

            let item_id = 1;
            let item_id_next = 1;
            let item_html = '';
            let sear = '';
            let repl = '';
            $('.additem').on('click', function(){
                item_id = $('.item:last').data('item');
                item_id_next = item_id + 1;
                item_id = '';
                sear = new RegExp('item_' + item_id, 'g');
                repl = 'item_' + item_id_next;
                item_html = $('#item_'+item_id).html().replace(sear,repl);
                sear = new RegExp('{{ __('Предмет') }} ' + item_id, 'g');
                item_html = item_html.replace(sear,'{{ __('Предмет') }} ' + item_id_next);

                $temp = $('<div>').html(item_html);
                $temp.find('.select2').remove();
                item_html = $temp.html();
                console.log(item_html);
                $('#items').append('<div class="g-4 item" data-item="'+item_id_next+'" id="item_' + item_id_next + '">' + item_html + '</div>');

                $('#shop_item_' + item_id_next+'_id').select2();
            });
        });

        //item
        function deleteitem(item){
            $('#'+item).remove();
        }
    </script>
    <script>
        @foreach(getlangs() as $key => $value)
            CKEDITOR.config.allowedContent=true;
            CKEDITOR.replace( 'description_{{ $key }}', {
                language: '{{ app()->getLocale() }}',
                filebrowserBrowseUrl : '/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                filebrowserUploadUrl : '/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                filebrowserImageBrowseUrl : '/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
            });

        @if(session()->has('theme') && session()->get('theme') == 'dark')
            CKEDITOR.addCss('.cke_editable { background-color: #0e1014; color: #942f06 }');
        @endif

        @endforeach
    </script>
@endpush