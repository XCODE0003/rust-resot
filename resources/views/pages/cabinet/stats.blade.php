@extends('layouts.main')

@section('title', __('Статистика') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@section('content')

  <div class="inner-header">{{ __('Персональная Статистика') }}</div>

  <div class="inner">

    <div class="container">
      <div class="stats tabs">

        <div class="stats-nav tab-nav">
          <h1>{{ __('Выберите сервер') }}:</h1>
          <ul>
            @foreach(getservers() as $server)
              <li @if(request()->has('server_id')) @if(request()->query('server_id') == $server->id) class="active" @endif @else @if($loop->iteration == 1) class="active" @endif @endif><span data-href="#main_{{ $server->id }}">{{ $server->name }}</span></li>
            @endforeach
          </ul>

          <div class="stats-icon">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </div>

        @foreach(getservers() as $server)
          <div class="tab @if(request()->has('server_id')) @if(request()->query('server_id') == $server->id) active @endif @else @if($loop->iteration == 1) active @endif @endif" id="main_{{ $server->id }}">

            <div class="stats-content tabs">

              <div class="personal-stats">

                <div class="personal-left">
                  <div class="personal-nick">
                    <span class="user-picture">
                      @if(isset($user->player_id))
                        @if(isset(getuser_by_steamid($user->player_id)->avatar))<img src="{{ getuser_by_steamid($user->player_id)->avatar }}" style="width: 85px;border-radius: 5px;">@endif
                      @else
                        <img src="@if(isset(auth()->user()->avatar)){{ auth()->user()->avatar }}@endif" style="width: 85px;border-radius: 5px;">
                      @endif
                    </span>
                    <h1>@if(isset($user->name)){{ $user->name }}@else @if(isset(auth()->user()->name)){{ auth()->user()->name }}@endif @endif</h1>
                  </div>
                  <div class="personal-info"><i class="fa-brands fa-steam"></i>Steam ID</div>

                  <div class="personal-info">@if(isset($user->player_id)){{ $user->player_id }}@else @if(isset(auth()->user()->steam_id)){{ auth()->user()->steam_id }}@endif @endif</div>
                  <div class="personal-characters desktop">
                    <div class="title"><span>-</span> {{ __('Другие игроки') }}:</div>
                    <div class="personal-characters-content">

                      @foreach($users_stat[$server->id] as $user_stat)

                        <a href="{{ route('account.stats', $user_stat->player_id) }}?server_id={{ $server->id }}" class="character">
                          <div class="character-img">@if(isset(getuser_by_steamid($user_stat->player_id)->avatar))<img src="{{ getuser_by_steamid($user_stat->player_id)->avatar }}">@endif</div>
                          <div class="character-info"><p>{{ $user_stat->name }}</p><span>{{ $user_stat->player_id }}</span></div>
                        </a>

                      @endforeach

                    </div>
                  </div>
                </div>

                <div class="personal-right">

                  @php $statistics = current($server_statistics[$server->id]['statistics']); @endphp

                  <div class="search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Введите Steam ID игрока, чтобы найти его статистику') }}">
                  </div>

                  <div class="stats-personal cover">
                    <div class="title">{{ __('PvP Cтатистика') }}</div>

                    <div class="stats-content">
                      <div>
                        <span>{{ __('Убийства / смерти') }}:</span>
                        <p>
                          @if(isset($statistics->deaths) && intval($statistics->deaths) > 0)
                            {{ number_format((intval($statistics->kills) / intval($statistics->deaths)), 2) }}
                          @else
                            {{ '0.00' }}
                          @endif
                        </p>
                      </div>
                      <div>
                        <span>{{ __('Убийств') }}:</span>
                        <p>{{ number_format(isset($statistics->kills) ? $statistics->kills : 0, 0, '.', ' ') }}</p>
                      </div>
                      <div>
                        <span>{{ __('Смертей') }}:</span>
                        <p>{{ number_format(isset($statistics->deaths) ? $statistics->deaths : 0, 0, '.', ' ') }}</p>
                      </div>
                      <div>
                        <span>{{ __('Смерти от игрока') }}:</span>
                        <p>{{ number_format(isset($statistics->deaths_player) ? $statistics->deaths_player : 0, 0, '.', ' ') }}</p>
                      </div>
                    </div>
                  </div>


                  <div class="stats-personal half">
                    <div class="title">{{ __('Ресурсы') }}</div>

                    <div class="stats-content">
                      @for($i=0; $i<count($server_statistics[$server->id]['resourses']); $i++)
                        @php $resourse = $server_statistics[$server->id]['resourses'][$i]; @endphp
                        <div>
                          <div class="stats-resources-title" style="flex-direction: row; margin-bottom: 0; margin-top: -10px;">
                            <span class="stats-resources-title-img" style="margin-top: 0px;"><img src="/images/rustitems/{{ $resourse }}.png"/></span>
                            <span>{{ getNameResource($resourse) }}</span>
                          </div>
                          <p>
                            @if(isset($statistics->resourse_list->$resourse))
                              {{ $statistics->resourse_list->$resourse }}
                            @else
                              {{ '0' }}
                            @endif
                          </p>
                        </div>
                      @endfor

                    </div>
                  </div>

                  <div class="stats-personal half">
                    <div class="title">{{ __('Сломанные постройки') }}</div>

                    <div class="stats-content">
                      @for($i=0; $i<count($server_statistics[$server->id]['raids']); $i++)
                        @php $raid = $server_statistics[$server->id]['raids'][$i]; @endphp
                        <div>
                            <span>{{ getNameRaid($raid) }}</span>
                          <p>
                            @if(isset($statistics->raid_list->$raid))
                              {{ $statistics->raid_list->$raid }}
                            @else
                              {{ '0' }}
                            @endif
                          </p>
                        </div>
                      @endfor

                    </div>
                  </div>


                  <div class="stats-personal cover">
                    <div class="title">{{ __('Попадания') }}</div>

                    <div class="stats-content">
                      <div>
                        <span>{{ __('Выстрелов / попаданий') }}:</span>
                        <p>
                          @if(isset($statistics->shoots) && intval($statistics->shoots) > 0)
                            {{ number_format((intval($statistics->hits) / intval($statistics->shoots)), 2) }}
                          @else
                            {{ '0.00' }}
                          @endif
                        </p>
                      </div>
                      <div>
                        <span>{{ __('Всего выстрелов') }}:</span>
                        <p>{{ number_format(isset($statistics->shoots) ? $statistics->shoots : 0, 0, '.', ' ') }}</p>
                      </div>
                      <div>
                        <span>{{ __('Всего попаданий') }}:</span>
                        <p>{{ number_format(isset($statistics->hits) ? $statistics->hits : 0, 0, '.', ' ') }}</p>
                      </div>
                      <div>
                        <span>{{ __('Попаданий в голову') }}:</span>
                        <p>{{ number_format(isset($statistics->head_shots) ? $statistics->head_shots : 0, 0, '.', ' ') }}</p>
                      </div>
                    </div>
                  </div>


                  <div class="personal-left mobile">
                    <div class="personal-characters">
                    <div class="title"><span>-</span> {{ __('Другие игроки') }}:</div>
                    <div class="personal-characters-content">

                      @foreach($users_stat[$server->id] as $user_stat)

                        <a href="{{ route('account.stats', $user_stat->player_id) }}?server_id={{ $server->id }}" class="character">
                          <div class="character-img">@if(isset(getuser_by_steamid($user_stat->player_id)->avatar))<img src="{{ getuser_by_steamid($user_stat->player_id)->avatar }}">@endif</div>
                          <div class="character-info"><p>{{ $user_stat->name }}</p><span>{{ $user_stat->player_id }}</span></div>
                        </a>

                      @endforeach

                    </div>
                  </div>
                  </div>

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
  <script src="/js/stats.js"></script>

  <script>
    $('.select-stats .option').on('click', function () {
      console.log($(this).data('type'));
      location.href = "{{ route('stats') }}?type=" + $(this).data('type') + "&search=" + $('#search').val();
    });
    $('input[name="search"]').on('change', function () {
      console.log($(this).val());

      @if(isset($user->player_id))
              location.href = "{{ route('account.stats', $user->player_id) }}?search=" + $(this).val();
      @elseif(isset(auth()->user()->steam_id))
              location.href = "{{ route('account.stats', auth()->user()->steam_id) }}?search=" + $(this).val();
      @endif
    });
  </script>
@endpush