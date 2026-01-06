@php
    $title = "title_" . app()->getLocale();
    $subtitle = "subtitle_" . app()->getLocale();
    $description = "description_" . app()->getLocale();
@endphp

    <div>
        <div class="container">

                <section class="open_cases-area">
                    <div class="content-area">
                        <div class="title-big title-big-type-1 flex-sc">
                            <div class="line-left"></div>
                            <h2 id="win_item-title"><span>{{ $case->$title }}</span></h2>
                            <div class="line-right"></div>
                        </div>

                        <div id="roll-{{ $case->id }}" class="roll-main open_cases-all-case-scroll open_cases-all-case-scroll-{{ $case->id }}">
                            <div class="open_cases-all-case open_cases-all-case-{{ $case->id }}" data-case-items>

                            </div>
                            <div class="open_cases-item-case main_case main_case-{{ $case->id }} active flex-cs">
                                <img src="{{ $case->image_url }}" alt="{{ $case->$title }}">
                            </div>
                            <div class="open_cases-item-case win_case win_case-{{ $case->id }} flex-cs">
                                <div class="win_case_img win_case_img-{{ $case->id }} flex-cs"><img src="" alt=""></div>
                                <div class="win_case_name win_case_name-{{ $case->id }}"></div>
                                <span class="win_case_amount win_case_amount-{{ $case->id }} item-amount"></span>
                            </div>
                        </div>

                        <div class="open_cases-link-block flex-sc @if($case->kind == 1) bonus-case-btns @endif @if(in_array(app()->getLocale(), ['ru', 'ua'])) bonus-case-ru @endif">
                            <div class="line-left">
                                <div class="quotes"></div>
                            </div>
                            <div class="flex-sbc open_cases-links open_cases-links-{{ $case->id }}">

                                @if($case->kind === 2)

                                    @if($case->is_free === 1)

                                        @if($free_open === TRUE)

                                            <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10 || 1==1) id="opencase-shopfree-{{ $case->id }}" @else id="set-tradeurl" @endif
                                            class="opencase-items flex-sbc open_cases-link active">
                                                <div class="open_cases-link-image"><img src="/images/icons/open-case_icon.png" alt=""></div>
                                                <div class="flex-cs">
                                                    {{ __('Открыть даром') }}
                                                </div>
                                                <div class="open_cases-link-shadow"></div>
                                            </div>

                                        @else

                                            <div class="opencase-items flex-sbc open_cases-link active">
                                                <div class="open_cases-link-image"><img src="/images/icons/open-case_icon.png" alt=""></div>
                                                <div class="flex-cs">
                                                    {!! format_seconds_from_day(free_case_left_time($case->id)) !!}
                                                </div>
                                                <div class="open_cases-link-shadow"></div>
                                            </div>
                                        @endif

                                    @else

                                        <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10 || 1==1) @if(isset(auth()->user()->balance) && auth()->user()->balance >= $case->price) id="opencase-{{ $case->id }}" @endif @else id="set-tradeurl" @endif
                                        class="opencase-items flex-sbc open_cases-link open_cases-link-{{ $case->id }} active">
                                            <div class="open_cases-link-image"><img src="/images/icons/open-case_icon.png" alt=""></div>
                                            <div class="flex-cs">
                                                @if(isset(auth()->user()->balance) && auth()->user()->balance >= $case->price) {{ __('Открыть кейс') }} @else <a href="{{ route('account.profile', ['topup' => 1]) }}">{{ __('Пополнить баланс') }}</a> @endif
                                            </div>
                                            <span>
                                                @if(app()->getLocale() == 'ru'){{ number_format($case->price, 0, '.', '') }} {{ __('₽') }}@else${{ number_format($case->price_usd, 0, '.', '') }}@endif
                                            </span>
                                            <div class="open_cases-link-shadow"></div>
                                        </div>

                                    @endif

                                @endif

                                <div class="open_cases__rolling open_cases__rolling-{{ $case->id }} flex-sbc">
                                    <div class="flex-cs">{{ __('ОТКРЫТИЕ') }}...</div>
                                </div>
                                <div class="open_cases-link-bar open_cases-link-bar-{{ $case->id }} flex-cc">
                                    <div class="flex-sbc open_cases-link-take">
                                        <div @if(isset(auth()->user()->balance) && auth()->user()->balance >= $case->price) id="take-bonus-{{ $case->id }}" @else id="disable" @endif class="flex-cs">{{ __('ЗАБРАТЬ ПРЕДМЕТ') }}</div>
                                        <div class="open_cases-link-shadow"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="line-right">
                                <div class="quotes"></div>
                            </div>
                        </div>

                    </div>
                    <form>
                        @csrf
                    </form>
                </section>

            <section id="trade-url-block-{{ $case->id }}" class="page-bonus-block" style="margin-bottom: 85px;display: none;">
                <div class="page-bonus-title">
                    <span>{{ __('STEAM TRADE OFFER URL') }}</span>
                </div>
                <div class="page-bonus-info">
                    <p>{{ __('Для получения подарка Вы должны указать ссылку для обмена STEAM') }}</p>
                </div>

                    <form action="{{ route('account.profile.setTradeUrl') }}" method="POST">
                        @csrf
                        <div class="page-bonus-tradeurl">
                            <div class="page-bonus-tradeurl-href">
                                <a href="https://steamcommunity.com/id/gbgi/tradeoffers/privacy#trade_offer_access_url" target="_blank">{{ __('Где взять ссылку для обмена?') }}</a>
                            </div>
                            <input type="text" placeholder="Steam tarde offer url" name="steam_trade_url" value="" class="search" autocomplete="off" required/>
                            <button class="modal-bonus-btn red modal-accept active" type="submit">{{ __('Сохранить') }}</button>
                        </div>
                    </form>

            </section>


            <section class="page-bonus-block">

                <div class="page-bonus-description" style="margin-bottom: 50px;">
                    <span>{!! $case->$description !!}</span>
                </div>

                    <div class="page-bonus-title">
                        <span>{{ __('Предметы в кейсе') }}</span>
                    </div>

                    <div class="page-bonus-prizes">

                        @foreach($items as $item)
                            <div class="page-bonus-prize type-{{ $item->context->quality_type }}">
                                <div class="quality-type"></div>
                                <img src="{{ $item->context->icon }}" alt="{{ $item->context->name }}">
                                <span class="item-amount">x{{ str_replace('-', ' - ', $item->context->amount) }}</span>
                                <span>{{ $item->context->name }}</span>

                                <span class="wipe-block tooltip" style="right: 15px;top: 38px;">
                                    <span class="tooltiptext">{{ __('Блокировка после вайпа:') }} {{ $item->context->wipe_block }} {{ plural_form($item->context->wipe_block, [__('час'), __('часа'), __('часов')]) }} </span>
                                    <img src="/images/info_white.svg" class="icon ni ni-info">
                                </span>
                            </div>
                        @endforeach

                    </div>

                </section>


        </div>
    </div>


    <script>



        $(document).ready(function() {

            controller{{ $case->id }} = new CaseController('roll-{{ $case->id }}');

            //Открытие кейса
            let widthLink = $('.open_cases-link-{{ $case->id }}').width();
            $('.open_cases-links-{{ $case->id }}').width(widthLink + 100);

            // Добавление шаблона итемов
            controller{{ $case->id }}.setItemsTemplate(`
                        <div class="open_cases-item-case flex-cs">
                            <img src="{icon}" alt="">
                        </div>
                    `);

            $.ajax({
                type: "POST",
                url: "{{ route('cases.getCaseItemsForRoll') }}",
                dataType: "json",
                data: { case_id: '{{ $case->id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.status);
                if (data.status == 'success') {
                    console.log(data.result);
                    // Добавление итемов
                    controller{{ $case->id }}.setItems(data.result);
                }
            });

        });

        $(document).on('click', '#opencase-{{ $case->id }}, #opencase-shopfree-{{ $case->id }}', function(e){

            // Событие на старте анимации вращения
            controller{{ $case->id }}.onAnimationStart(function (event) {
                $('.open_cases-link-{{ $case->id }}').removeClass('active');
                $('#opencase-shopfree-{{ $case->id }}').removeClass('active');
                $('.main_case-{{ $case->id }}').removeClass('active');
                $('.open_cases__rolling-{{ $case->id }}').addClass('active');
                let widthLink = $('.open_cases__rolling-{{ $case->id }}').width();
                $('.open_cases-links-{{ $case->id }}').width(widthLink + 100);
                $('.open_cases-all-case-scroll-{{ $case->id }}').addClass('rolling');

                setTimeout(function () {
                    $('.open_cases-all-case-{{ $case->id }}').addClass('roll');
                }, 500);
            });

            // Событие в конце анимации вращения
            controller{{ $case->id }}.onAnimationEnd(function (event) {
                $('.open_cases-all-case-{{ $case->id }}').addClass('disable');
                $('.open_cases__rolling-{{ $case->id }}').removeClass('active');
                let widthLink = $('.open_cases-link-bar-{{ $case->id }}').width();
                $('.open_cases-links-{{ $case->id }}').width(widthLink + 100);
                $('.open_cases-all-case-scroll-{{ $case->id }}').removeClass('rolling');
                $('.win_case_img-{{ $case->id }}').html('<img src="' + event.item.context.icon + '" alt="">');
                $('.win_case_name-{{ $case->id }}').html(event.item.context.name);
                $('.win_case-{{ $case->id }}, .open_cases-link-bar-{{ $case->id }} > div').addClass('active');
            });

            $.ajax({
                type: "POST",
                url: "{{ route('cases.open') }}",
                dataType: "json",
                data: { case_id: '{{ $case->id }}', server_id: '{{ $server_id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.win_index);
                console.log(data.win_item);
                if (data.status == 'success') {
                    controller{{ $case->id }}.run(data.win_index);

                    let rnd_amount = data.win_item.context.shop_var;
                    if (rnd_amount.indexOf('rnd=') > -1) {
                        rnd_amount = rnd_amount.replace("rnd=", "");
                        $('.win_case_amount-{{ $case->id }}').text('x' + rnd_amount);
                    }
                } else {
                    //
                }
            });

        });

        $(document).on('click', '#take-bonus-{{ $case->id }}', function(e){
            location.reload();
        });
        $(document).on('click', '#to-donate-{{ $case->id }}', function(e){
            location.href="{{ route('account.profile', 'topup=1') }}";
        });

        $(document).on('click', '#set-tradeurl-{{ $case->id }}, #set-tradeurl-free-{{ $case->id }}, #set-tradeurl-pay-{{ $case->id }}', function(e){
            $('#trade-url-block-{{ $case->id }}').show();
        });
    </script>