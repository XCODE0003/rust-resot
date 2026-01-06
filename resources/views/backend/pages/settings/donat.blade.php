@extends('backend.layouts.backend')

@section('title', 'Панель управления - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки Курса Валют') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">

                    <div class="tab-pane" id="donate">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="exchange_rate_usd">{{ __('Курс за 1') }} USD ({{ __('RUB') }})</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" step="0.01" class="form-control" id="exchange_rate_usd"
                                                           name="setting_exchange_rate_usd" value="{{ config('options.exchange_rate_usd', 100) }}">
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
    </div>
    <!-- .nk-block -->


    <script>
        $( document ).ready(function() {

            //donat Cols
            let donat_id = 1;
            let donat_id_next = 1;
            let donat_html = '';
            let sear = '';
            let repl = '';
            $('.adddonat').on('click', function(){
                donat_id = $('.donat:last').data('donat');
                donat_id_next = donat_id + 1;
                donat_id = '';
                sear = new RegExp('donat_' + donat_id, 'g');
                repl = 'donat_' + donat_id_next;
                donat_html = $('#donat_'+donat_id).html().replace(sear,repl);
                sear = new RegExp('bitems_' + donat_id, 'g');
                repl = 'bitems_' + donat_id_next;
                donat_html = donat_html.replace(sear,repl);
                sear = new RegExp('ditem_' + donat_id, 'g');
                repl = 'ditem_' + donat_id_next;
                donat_html = donat_html.replace(sear,repl);
                sear = new RegExp('bitem_' + donat_id, 'g');
                repl = 'bitem_' + donat_id_next + '_';
                donat_html = donat_html.replace(sear,repl);
                sear = "addBitem('"+donat_id+"')";
                repl = "addBitem('"+donat_id_next+"')";
                donat_html = donat_html.replace(sear,repl);
                sear = new RegExp('{{ __("Донат") }} ' + donat_id, 'g');
                donat_html = donat_html.replace(sear,'{{ __("Донат") }} ' + donat_id_next);

                $('#donates').append('<div class="g-4 donat" data-donat="'+donat_id_next+'" id="donat_' + donat_id_next + '">' + donat_html + '</div>');
            });


            $('#server').on('change', function () {
                console.log($('#server').val());
                location.href = "{{ route('settings.donat', '') }}?server=" + $('#server').val();
            })

        });

        //donat Cols
        function deletedonat(donat){
            $('#'+donat).remove();
        }

        //donat Items
        function deleteBitem(bitem){
            $('#'+bitem).remove();
        }

        //donat Items
        function addBitem(donat){
            let bitem_id = 1;
            let bitem_id_next = 1;
            let bitem_html = '';
            let sear2 = '';
            let repl2 = '';
            console.log(donat);
            bitem_id = $('.ditem_'+donat+':last').data('bitem');
            bitem_id_next = bitem_id + 1;
            console.log(bitem_id_next);
            sear2 = new RegExp('bitem_' + donat + '_' + bitem_id, 'g');
            console.log(sear2);
            repl2 = 'bitem_' + donat + '_' + bitem_id_next;
            bitem_html = $('#bitem_'+donat+'_'+bitem_id).html().replace(sear2,repl2);
            sear2 = new RegExp('{{ __("Предмет") }} ' + bitem_id, 'g');
            bitem_html = bitem_html.replace(sear2,'{{ __("Предмет") }} ' + bitem_id_next);
            console.log('#bitems_'+donat);
            $('#bitems_'+donat).append('<div class="row g-4 ditem_'+donat+'" data-bitem="'+bitem_id_next+'" id="bitem_'+donat+'_' + bitem_id_next + '">' + bitem_html + '</div>');
        }
    </script>

@endsection
