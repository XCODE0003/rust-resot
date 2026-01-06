@extends('layouts.main')

@section('title', __('Сервера') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
    <meta name="description" content="All active RustResort servers.">
@endprepend

@section('content')

  <div class="inner-header">{{ __('Сервера') }}</div>

  <div class="inner">
    <div class="container">

        <div class="stats tabs">
      <div class="stats-content-nav tab-nav">
        <ul>
          @foreach(getservercategories() as $category)
            @php $title = "title_" .app()->getLocale(); @endphp
            <li class="@if($loop->iteration == 1){{ 'active' }}@endif"><span data-href="#category_{{ $category->id }}">{{ $category->$title }}</span></li>
          @endforeach
        </ul>

        <div class="stats-icon">
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>

            @foreach(getservercategories() as $category)
                @php $title = "title_" .app()->getLocale(); @endphp
                <div class="stats-content-rank servers-list-tab tab @if($loop->iteration == 1){{ 'active' }}@endif"
                     id="category_{{ $category->id }}">
                    <h1>{{ $category->$title }}</h1>

                    <div class="servers-list-block servers-page @if(count($servers[$category->id]) >= 12){{ 'many-servers' }}@endif">

                        <div class="servers" style="background: none; padding-top: 0;">

                            <div class="servers-list">

                                <script>
                                    function getTimeRemaining(endtime) {
                                        let t = Date.parse(endtime) - Date.parse(new Date());
                                        let seconds = Math.floor((t / 1000) % 60);
                                        let minutes = Math.floor((t / 1000 / 60) % 60);
                                        let hours = Math.floor((t / (1000 * 60 * 60)) % 24);
                                        let days = Math.floor(t / (1000 * 60 * 60 * 24));
                                        return {
                                            'total': t,
                                            'days': days,
                                            'hours': hours,
                                            'minutes': minutes,
                                            'seconds': seconds
                                        };
                                    }

                                    function initializeClock(id, endtime) {
                                        let clock = document.getElementById(id);
                                        let daysSpan = clock.querySelector('.days');
                                        let hoursSpan = clock.querySelector('.hours');
                                        let minutesSpan = clock.querySelector('.minutes');
                                        let secondsSpan = clock.querySelector('.seconds');

                                        function updateClock() {
                                            let t = getTimeRemaining(endtime);

                                            daysSpan.innerHTML = t.days;
                                            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
                                            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
                                            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

                                            if (t.total <= 0) {
                                                clearInterval(timeinterval);
                                            }
                                        }

                                        updateClock();
                                        let timeinterval = setInterval(updateClock, 1000);
                                    }
                                </script>

                            @foreach($servers[$category->id] as $server)
                                @php $options = json_decode($server->options); @endphp
                                <!--server content-->
                                    <div class="servers-list-content"
                                         @if(count($servers[$category->id]) >= 12)
                                            style="order: 0; width: 206px; margin-right: 10px; margin-bottom: 20px;"
                                         @else
                                            style="order: 0; width: 300px; margin-right: 25px; margin-bottom: 30px;"
                                         @endif
                                    >

                                        <div class="servers-list-content-name">
                                            <div class="servers-list-content-name-text">{{ $server->name }}</div>
                                            <div class="servers-list-content-name-wipe">
                                                @if(config('options.server_'.$server->id.'_plate', '0') == '0')
                                                    {!! get_wiped_data($server->id) !!}
                                                @else
                                                    <span style="display:flex;color:white;align-items:center;"><img
                                                                src="/images/smailik-s-krestikami.png">{{ __('NOT INFORMATION') }}</span>
                                                @endif
                                            </div>
                                            <div class="servers-list-content-name-img"><img
                                                        src="{{ $server->image_url }}"></div>
                                        </div>

                                        <div class="servers-list-content-info">
                                            @if(config('options.server_'.$server->id.'_plate', '0') == '1')
                                                <div class="servers-list-content-info-soon">
                                                    <p>{{ __('Opens in') }}: <span
                                                                id="wipe-server-9{{ $server->id }}"
                                                                class="online wipe-time">{!! opening_date($server->id) !!}</span>
                                                    </p></div>
                                            @else
                                                <div class="servers-list-content-info-ip"><p>IP: <span class="ip-copy"
                                                                                                       data-ip="{{ $options->ip }}">{{ $options->ip }}</span>
                                                    </p></div>

                                                {{--
                                                <a href="{{ route('account.stats', (isset(auth()->user()->steam_id) ? auth()->user()->steam_id : '0')) }}?server_id={{ $server->id }}"
                                                   class="servers-list-content-info-stats">
                                                    <div>{{ __('My stats') }}</div>
                                                </a>
                                                --}}

                                            @endif
                                        </div>

                                        <div class="servers-list-content-play">

                                            @if(config('options.server_'.$server->id.'_plate', '0') == '0')

                                                <div class="servers-list-content-play-online">
                                                    <p>{{ __('Next Wipe') }}: <span
                                                                id="wipe-server-{{ $server->id }}"
                                                                class="online wipe-time">{!! next_wipe($server->id) !!}</span>
                                                    </p></div>
                                                <div class="servers-list-content-play-online"><p>{{ __('Online') }}:
                                                        <span class="online">{{ online_count($server->id) }}</span>
                                                        <span>/ {{ online_max($server->id) }} @if(online_queued($server->id) > 0)
                                                                ({{ online_queued($server->id) }})@endif</span></p>
                                                </div>

                                            @else

                                                <div class="servers-list-content-info-plate">
                                                    <div class="plate-block">
                                                        @if(config('options.server_'.$server->id.'_plate', '0') == '1')
                                                            <span><img src="/images/coming_soon.png">{{ __('Opening soon') }}</span>
                                                        @elseif(config('options.server_'.$server->id.'_plate', '0') == '2')
                                                            <span><img src="/images/techn_work.png">{{ __('Technical work') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="servers-list-content-play-connect-shop">
                                                @if(config('options.server_'.$server->id.'_plate', '0') == '0')
                                                    <a href="steam://connect/{{ $options->ip }}" class="connect">
                                                        <div><span></span>{{ __('Connect') }}</div>
                                                    </a>
                                                    <a href="{{ route('shop.item.show', $server->id) }}" class="shop">
                                                        <div><span></span><i class="fa-solid fa-cart-shopping"></i>
                                                        </div>
                                                    </a>
                                                @else
                                                    <button class="connect disabled" disabled="disabled">
                                                        <div><span></span>{{ __('Connect') }}</div>
                                                    </button>
                                                    <a class="shop disabled">
                                                        <div><span></span><i class="fa-solid fa-cart-shopping"></i>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <script>

                                        @if(config('options.server_'.$server->id.'_plate', '0') == '1')
                                        let soon_deadline_{{ $category->id }}_{{ $server->id }} = new Date(Date.parse(new Date()) + {{ opening_date_second($server->id) }} * 1000);
                                        console.log({{ opening_date_second($server->id) }});
                                        initializeClock('wipe-server-9{{ $server->id }}', soon_deadline_{{ $category->id }}_{{ $server->id }});
                                        @else
                                        let deadline_{{ $category->id }}_{{ $server->id }} = new Date(Date.parse(new Date()) + {{ next_wipe_second($server->id) }} * 1000);
                                        console.log({{ next_wipe_second($server->id) }});
                                        initializeClock('wipe-server-{{ $server->id }}', deadline_{{ $category->id }}_{{ $server->id }});
                                        @endif

                                    </script>
                                    <!--end server content-->
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
        </div>
      </div>


@endsection
@push('scripts')
  <script>
    $('.ip-copy').on('click', function() {

      let temp = $("<input>");
      $("body").append(temp);
      let link = $(this).data('ip');
      console.log(link);
      temp.val(link).select();
      document.execCommand("copy");
      temp.remove();

      $('#alertip-msg').text('{{ __('IP сервера скопирован в буфер обмена!') }}');
      $('.alertip-modal').show();

      $(this).addClass('is-copied');
      setTimeout(function(){
        $('.ip-copy').removeClass('is-copied');
        $('.alertip-modal').hide();
      }, 2000);

    });
  </script>

@endpush