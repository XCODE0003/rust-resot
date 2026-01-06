@extends('layouts.main')

@section('title', __('Статистика') . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
  <meta name="description" content="View the statistics of your RustResort survival.">
@endprepend

@section('content')

  <div class="inner-header">{{ __('Статистика') }}</div>

  <div class="inner">

    <div class="container">
      <div class="stats tabs">

        <div class="stats-nav tab-nav">
          <h1>{{ __('Выберите сервер') }}:</h1>
          <ul>
            @foreach(getservers() as $server)
              <li class="@if($loop->first) active @endif stats-nav_{{ $server->id }}" data-server_id="{{ $server->id }}"><span data-href="#main_{{ $server->id }}">{{ $server->name }}</span></li>
            @endforeach
          </ul>

          <div class="stats-icon">
            <i class="fa-solid fa-chevron-down"></i>
          </div>
        </div>

        @foreach(getservers() as $server)
          <div class="tab stats-tab @if($loop->first) active @endif" id="main_{{ $server->id }}">

            <div class="stats-content tabs">

              <div class="stats-content-nav tab-nav stat-tab_{{ $server->id }}">
                <ul>
                  <li class="active" id="pvp-tab_{{ $server->id }}" data-sort="pvp_sort"><span data-href="#pvp_{{ $server->id }}">{{ __('PvP Cтатистика') }}</span></li>
                  <li id="resources-tab_{{ $server->id }}" data-sort="res_sort"><span data-href="#resources_{{ $server->id }}">{{ __('Ресурсы') }}</span></li>
                  <li id="raids_doors-tab_{{ $server->id }}" data-sort="raids_doors_sort"><span data-href="#raids_doors_{{ $server->id }}">{{ __('Сломанные двери') }}</span></li>
                  <li id="raids-tab_{{ $server->id }}" data-sort="raids_sort"><span data-href="#raids_{{ $server->id }}">{{ __('Сломанные постройки') }}</span></li>
                  <li id="hits-tab_{{ $server->id }}" data-sort="hits_sort"><span data-href="#hits_{{ $server->id }}">{{ __('Попадания') }}</span></li>
                </ul>

                <div class="stats-icon">
                  <i class="fa-solid fa-chevron-down"></i>
                </div>
              </div>

              <div class="stats-content-rank tab stat-tab_{{ $server->id }} active" id="pvp_{{ $server->id }}">
                <h1>{{ __('PvP Cтатистика') }}</h1>

                <div class="total-stats-search">
                  <div class="total-stats"><p>{{ __('Общая статистика PvP') }}: <span>{{ $server_statistics[$server->id]['total_pvp_stat'] }}</span></p> <div class="check-stats">{{ __('Проверить статистику') }}<i class="fa-solid fa-angle-down"></i></div></div>

                  <div class="search-stats"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Имя профиля') }}" data-sort="pvp_sort" data-server_id="{{ $server->id }}"></div>

                  <div class="select-stats">
                    <i class="fa-solid fa-chevron-down"></i>
                    <div class="select">
                        @if(request()->has('type') && request()->query('type') != '')
                          <div class="select-type" data-type="{{ request()->query('type') }}">
                            @if(request()->query('type') == 'day')
                              {{ __('За день') }}
                            @elseif(request()->query('type') == 'week')
                              {{ __('За неделю') }}
                            @elseif(request()->query('type') == 'month')
                              {{ __('За месяц') }}
                            @elseif(request()->query('type') == 'all')
                              {{ __('За все время') }}
                            @endif
                          </div>
                        @else
                          <div class="select-type" data-type="all">{{ __('За все время') }}</div>
                        @endif

                      <div class="select-dropdown">
                        <div class="option" data-type="all" data-server_id="{{ $server->id }}">{{ __('За все время') }}</div>
                        <div class="option" data-type="day" data-server_id="{{ $server->id }}">{{ __('За день') }}</div>
                        <div class="option" data-type="week" data-server_id="{{ $server->id }}">{{ __('За неделю') }}</div>
                        <div class="option" data-type="month" data-server_id="{{ $server->id }}">{{ __('За месяц') }}</div>
                      </div>
                    </div>
                  </div>


                </div>

                <div class="stats-info">
                  <div><span>{{ __('Убийства / смерти') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kdr'] }}</span></span></div>
                  <div><span>{{ __('Убийств') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kills'] }}</span></span></div>
                  <div><span>{{ __('Смертей') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths'] }}</span></span></div>
                  <div><span>{{ __('Смерти от игрока') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths_player'] }}</span></span></div>
                </div>

                <div class="stats-rank-list">
                  <div class="stats-type">
                    <div class="name">{{ __('Имя') }}</div>
                    <div class="pvp-stats pvp-sort" data-pvp_sort="kdr" data-server_id="{{ $server->id }}">{{ __('Убийства / смерти') }}</div>
                    <div class="pvp-stats pvp-sort" data-pvp_sort="kills" data-server_id="{{ $server->id }}">{{ __('Убийств') }}</div>
                    <div class="pvp-stats pvp-sort" data-pvp_sort="deaths" data-server_id="{{ $server->id }}">{{ __('Смертей') }}</div>
                    <div class="pvp-stats pvp-sort" data-pvp_sort="deaths_player" data-server_id="{{ $server->id }}">{{ __('Смерти от игрока') }}</div>
                    <div class="pvp-stats"></div>
                  </div>

                  <ul class="rank-list">
                    @foreach($server_statistics[$server->id]['statistics'] as $statistic)
                      <li>
                        <div class="player">{{ $statistic->name }}</div>
                        <div class="pvp-stats"><span>{{ __('Убийства / смерти') }}:</span>
                          @if(intval($statistic->deaths) > 0)
                            {{ number_format((intval($statistic->kills) / intval($statistic->deaths)), 2) }}
                          @else
                            {{ '0.00' }}
                          @endif
                        </div>
                        <div class="pvp-stats"><span>{{ __('Убийств') }}:</span>{{ number_format($statistic->kills, 0, '.', ' ') }}</div>
                        <div class="pvp-stats"><span>{{ __('Смертей') }}:</span>{{ number_format($statistic->deaths, 0, '.', ' ') }}</div>
                        <div class="pvp-stats"><span>{{ __('Смерти от игрока') }}:</span>{{ number_format($statistic->deaths_player, 0, '.', ' ') }}</div>
                        <div class="pvp-stats">
                          <button><a href="{{ route('account.stats', $statistic->player_id) }}?server_id={{ $server->id }}">{{ __('Смотреть Профиль') }}</a></button>
                        </div>
                      </li>
                    @endforeach

                  </ul>

                  <div class="pagination">
                      {{ $server_statistics[$server->id]['statistics']->links('layouts.pagination.stat-sort') }}
                  </div>
                </div>
              </div>

              <div class="stats-content-rank tab stat-tab_{{ $server->id }}" id="resources_{{ $server->id }}">
                <h1>{{ __('Ресурсы') }}</h1>

                <div class="total-stats-search">
                  <div class="total-stats"><p>{{ __('Общая статистика PvP') }}: <span>{{ $server_statistics[$server->id]['total_pvp_stat'] }}</span></p> <div class="check-stats">{{ __('Проверить статистику') }}<i class="fa-solid fa-angle-down"></i></div></div>

                  <div class="search-stats"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Имя профиля') }}" data-sort="res_sort" data-server_id="{{ $server->id }}"></div>

                  <div class="select-stats">
                    <i class="fa-solid fa-chevron-down"></i>
                    <div class="select">
                      @if(request()->has('type') && request()->query('type') != '')
                        <div class="select-type" data-type="{{ request()->query('type') }}" data-server_id="{{ $server->id }}">
                          @if(request()->query('type') == 'day')
                            {{ __('За день') }}
                          @elseif(request()->query('type') == 'week')
                            {{ __('За неделю') }}
                          @elseif(request()->query('type') == 'month')
                            {{ __('За месяц') }}
                          @elseif(request()->query('type') == 'all')
                            {{ __('За все время') }}
                          @endif
                        </div>
                      @else
                        <div class="select-type" data-type="all">{{ __('За все время') }}</div>
                      @endif

                      <div class="select-dropdown">
                        <div class="option" data-type="all" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За все время') }}</div>
                        <div class="option" data-type="day" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За день') }}</div>
                        <div class="option" data-type="week" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За неделю') }}</div>
                        <div class="option" data-type="month" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За месяц') }}</div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="stats-info">
                  <div><span>{{ __('Убийства / смерти') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kdr'] }}</span></span></div>
                  <div><span>{{ __('Убийств') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kills'] }}</span></span></div>
                  <div><span>{{ __('Смертей') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths'] }}</span></span></div>
                  <div><span>{{ __('Смерти от игрока') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths_player'] }}</span></span></div>
                </div>

                <div class="stats-rank-list">
                  <div class="stats-type">
                    <div class="name">{{ __('Имя') }}</div>
                    @foreach($server_statistics[$server->id]['resourses'] as $resourse)
                      <div class="stats-resources-title">
                        <span>{{ getNameResource($resourse) }}</span>
                        <span class="stats-resources-title-img res-sort" data-res_sort="{{ $resourse }}" data-server_id="{{ $server->id }}"><img src="/images/rustitems/{{ $resourse }}.png"/></span>
                      </div>
                    @endforeach
                  </div>

                  <ul class="rank-list">
                    @foreach($server_statistics[$server->id]['statistics'] as $statistic)
                      @php $resourse_list = $statistic->resourse_list; @endphp
                      <li>
                        <div class="player">{{ $statistic->name }}</div>
                        @foreach($server_statistics[$server->id]['resourses'] as $resourse)
                          @php $resourse_find = FALSE; @endphp
                          @foreach($resourse_list as $resourse_key => $resourse_value)
                            @if($resourse_key == $resourse)
                              @php
                                $resourse_find = TRUE;
                                $resourse_find_value = $resourse_value;
                              @endphp
                            @endif
                          @endforeach

                            <div class="stats-resources-title">
                              <span>{{ getNameResource($resourse) }}</span>
                              <span class="stats-resources-title-img"><img src="/images/rustitems/{{ $resourse }}.png"/></span>

                            @if($resourse_find === TRUE)
                              {{ number_format($resourse_find_value, 0, '.', ' ') }}
                            @else
                              {{ '0' }}
                            @endif

                          </div>

                        @endforeach

                        <button><a href="{{ route('account.stats', $statistic->player_id) }}?server_id={{ $server->id }}">{{ __('Смотреть Профиль') }}</a></button>
                      </li>
                    @endforeach

                  </ul>

                  <div class="pagination">
                      {{ $server_statistics[$server->id]['statistics']->links('layouts.pagination.stat-sort') }}
                  </div>
                </div>
              </div>

              <div class="stats-content-rank tab stat-tab_{{ $server->id }}" id="raids_doors_{{ $server->id }}">
                <h1>{{ __('Сломанные двери') }}</h1>


                <div class="total-stats-search">
                  <div class="total-stats"><p>{{ __('Общая статистика PvP') }}: <span>{{ $server_statistics[$server->id]['total_pvp_stat'] }}</span></p> <div class="check-stats">{{ __('Проверить статистику') }}<i class="fa-solid fa-angle-down"></i></div></div>

                  <div class="search-stats"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Имя профиля') }}" data-sort="raids_doors_sort" data-server_id="{{ $server->id }}"></div>

                  <div class="select-stats">
                    <i class="fa-solid fa-chevron-down"></i>
                    <div class="select">
                      @if(request()->has('type') && request()->query('type') != '')
                        <div class="select-type" data-type="{{ request()->query('type') }}" data-server_id="{{ $server->id }}">
                          @if(request()->query('type') == 'day')
                            {{ __('За день') }}
                          @elseif(request()->query('type') == 'week')
                            {{ __('За неделю') }}
                          @elseif(request()->query('type') == 'month')
                            {{ __('За месяц') }}
                          @elseif(request()->query('type') == 'all')
                            {{ __('За все время') }}
                          @endif
                        </div>
                      @else
                        <div class="select-type" data-type="all">{{ __('За все время') }}</div>
                      @endif

                      <div class="select-dropdown">
                        <div class="option" data-type="all" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За все время') }}</div>
                        <div class="option" data-type="day" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За день') }}</div>
                        <div class="option" data-type="week" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За неделю') }}</div>
                        <div class="option" data-type="month" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За месяц') }}</div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="stats-info">
                  <div><span>{{ __('Убийства / смерти') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kdr'] }}</span></span></div>
                  <div><span>{{ __('Убийств') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kills'] }}</span></span></div>
                  <div><span>{{ __('Смертей') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths'] }}</span></span></div>
                  <div><span>{{ __('Смерти от игрока') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths_player'] }}</span></span></div>
                </div>

                <div class="stats-rank-list">
                  <div class="stats-type">
                    <div class="name">{{ __('Имя') }}</div>
                    @foreach($server_statistics[$server->id]['raids_doors'] as $raid)
                      <div class="raids_doors-sort" data-raids_doors_sort="{{ $raid }}" data-server_id="{{ $server->id }}">{{ getNameRaid($raid) }}</div>
                    @endforeach
                  </div>

                  <ul class="rank-list raids-list raids_doors-list">
                    @foreach($server_statistics[$server->id]['statistics'] as $statistic)
                      @php $raid_list = $statistic->raid_list; @endphp
                      <li>
                        <div class="player">{{ $statistic->name }}</div>
                        @foreach($server_statistics[$server->id]['raids_doors'] as $raid)
                          @php $raid_find = FALSE; @endphp
                          @foreach($raid_list as $raid_key => $raid_value)
                            @if($raid_key == $raid)
                              @php
                                $raid_find = TRUE;
                                $raid_find_value = $raid_value;
                              @endphp
                            @endif
                          @endforeach



                          <div class="stats-resources-title">
                            <span>{{ $raid }}</span>

                            @if($raid_find === TRUE)
                              {{ number_format($raid_find_value, 0, '.', ' ') }}
                            @else
                              {{ '0' }}
                            @endif

                          </div>

                        @endforeach

                        <button><a href="{{ route('account.stats', $statistic->player_id) }}?server_id={{ $server->id }}">{{ __('Смотреть Профиль') }}</a></button>
                      </li>
                    @endforeach

                  </ul>

                  <div class="pagination">
                    {{ $server_statistics[$server->id]['statistics']->links('layouts.pagination.stat-sort') }}
                  </div>
                </div>
              </div>

              <div class="stats-content-rank tab stat-tab_{{ $server->id }}" id="raids_{{ $server->id }}">
                <h1>{{ __('Сломанные постройки') }}</h1>

                <div class="total-stats-search">
                  <div class="total-stats"><p>{{ __('Общая статистика PvP') }}: <span>{{ $server_statistics[$server->id]['total_pvp_stat'] }}</span></p> <div class="check-stats">{{ __('Проверить статистику') }}<i class="fa-solid fa-angle-down"></i></div></div>

                  <div class="search-stats"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Имя профиля') }}" data-sort="raids_sort" data-server_id="{{ $server->id }}"></div>

                  <div class="select-stats">
                    <i class="fa-solid fa-chevron-down"></i>
                    <div class="select">
                      @if(request()->has('type') && request()->query('type') != '')
                        <div class="select-type" data-type="{{ request()->query('type') }}" data-server_id="{{ $server->id }}">
                          @if(request()->query('type') == 'day')
                            {{ __('За день') }}
                          @elseif(request()->query('type') == 'week')
                            {{ __('За неделю') }}
                          @elseif(request()->query('type') == 'month')
                            {{ __('За месяц') }}
                          @elseif(request()->query('type') == 'all')
                            {{ __('За все время') }}
                          @endif
                        </div>
                      @else
                        <div class="select-type" data-type="all">{{ __('За все время') }}</div>
                      @endif

                      <div class="select-dropdown">
                        <div class="option" data-type="all" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За все время') }}</div>
                        <div class="option" data-type="day" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За день') }}</div>
                        <div class="option" data-type="week" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За неделю') }}</div>
                        <div class="option" data-type="month" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За месяц') }}</div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="stats-info">
                  <div><span>{{ __('Убийства / смерти') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kdr'] }}</span></span></div>
                  <div><span>{{ __('Убийств') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kills'] }}</span></span></div>
                  <div><span>{{ __('Смертей') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths'] }}</span></span></div>
                  <div><span>{{ __('Смерти от игрока') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths_player'] }}</span></span></div>
                </div>

                <div class="stats-rank-list">
                  <div class="stats-type">
                    <div class="name" style="width: 13%">{{ __('Имя') }}</div>
                    @foreach($server_statistics[$server->id]['raids'] as $raid)
                      <div class="raids-sort" data-raids_sort="{{ $raid }}" data-server_id="{{ $server->id }}">{{ getNameRaid($raid) }}</div>
                    @endforeach
                  </div>

                  <ul class="rank-list raids-list">
                    @foreach($server_statistics[$server->id]['statistics'] as $statistic)
                      @php $raid_list = $statistic->raid_list; @endphp
                      <li>
                        <div class="player" style="width: 13% !important">{{ $statistic->name }}</div>
                        @foreach($server_statistics[$server->id]['raids'] as $raid)
                          @php $raid_find = FALSE; @endphp
                          @foreach($raid_list as $raid_key => $raid_value)
                            @if($raid_key == $raid)
                              @php
                                $raid_find = TRUE;
                                $raid_find_value = $raid_value;
                              @endphp
                            @endif
                          @endforeach



                          <div class="stats-resources-title">
                            <span>{{ $raid }}</span>

                            @if($raid_find === TRUE)
                              {{ number_format($raid_find_value, 0, '.', ' ') }}
                            @else
                              {{ '0' }}
                            @endif

                          </div>

                        @endforeach

                        <button><a href="{{ route('account.stats', $statistic->player_id) }}?server_id={{ $server->id }}">{{ __('Смотреть Профиль') }}</a></button>
                      </li>
                    @endforeach

                  </ul>

                  <div class="pagination">
                      {{ $server_statistics[$server->id]['statistics']->links('layouts.pagination.stat-sort') }}
                  </div>
                </div>
              </div>

              <div class="stats-content-rank tab stat-tab_{{ $server->id }}" id="hits_{{ $server->id }}">
                <h1>{{ __('Попадания') }}</h1>

                <div class="total-stats-search">
                  <div class="total-stats"><p>{{ __('Общая статистика PvP') }}: <span>{{ $server_statistics[$server->id]['total_pvp_stat'] }}</span></p> <div class="check-stats">{{ __('Проверить статистику') }}<i class="fa-solid fa-angle-down"></i></div></div>

                  <div class="search-stats"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Имя профиля') }}" data-sort="hits_sort" data-server_id="{{ $server->id }}"></div>

                  <div class="select-stats">
                    <i class="fa-solid fa-chevron-down"></i>
                    <div class="select">
                      @if(request()->has('type') && request()->query('type') != '')
                        <div class="select-type" data-type="{{ request()->query('type') }}">
                          @if(request()->query('type') == 'day')
                            {{ __('За день') }}
                          @elseif(request()->query('type') == 'week')
                            {{ __('За неделю') }}
                          @elseif(request()->query('type') == 'month')
                            {{ __('За месяц') }}
                          @elseif(request()->query('type') == 'all')
                            {{ __('За все время') }}
                          @endif
                        </div>
                      @else
                        <div class="select-type" data-type="all">{{ __('За все время') }}</div>
                      @endif

                      <div class="select-dropdown">
                        <div class="option" data-type="all" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За все время') }}</div>
                        <div class="option" data-type="day" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За день') }}</div>
                        <div class="option" data-type="week" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За неделю') }}</div>
                        <div class="option" data-type="month" data-sort="hits_sort" data-server_id="{{ $server->id }}">{{ __('За месяц') }}</div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="stats-info">
                  <div><span>{{ __('Убийства / смерти') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kdr'] }}</span></span></div>
                  <div><span>{{ __('Убийств') }}: <span>{{ $server_statistics[$server->id]['total_pvp_kills'] }}</span></span></div>
                  <div><span>{{ __('Смертей') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths'] }}</span></span></div>
                  <div><span>{{ __('Смерти от игрока') }}: <span>{{ $server_statistics[$server->id]['total_pvp_deaths_player'] }}</span></span></div>
                </div>

                <div class="stats-rank-list">
                  <div class="stats-type">
                    <div class="name">{{ __('Имя') }}</div>
                    <div class="pvp-stats hits-sort" data-hits_sort="hits_kdr" data-server_id="{{ $server->id }}">{{ __('Выстрелов / попаданий') }}</div>
                    <div class="pvp-stats hits-sort" data-hits_sort="shoots" data-server_id="{{ $server->id }}">{{ __('Всего выстрелов') }}</div>
                    <div class="pvp-stats hits-sort" data-hits_sort="hits" data-server_id="{{ $server->id }}">{{ __('Всего попаданий') }}</div>
                    <div class="pvp-stats hits-sort" data-hits_sort="head_shots" data-server_id="{{ $server->id }}">{{ __('Попаданий в голову') }}</div>
                    <div class="pvp-stats"></div>
                  </div>

                  <ul class="rank-list">
                    @foreach($server_statistics[$server->id]['statistics'] as $statistic)
                      <li>
                        <div class="player">{{ $statistic->name }}</div>
                        <div class="pvp-stats"><span>{{ __('Выстрелов / попаданий') }}:</span>
                          @if(intval($statistic->shoots) > 0)
                          {{ number_format((intval($statistic->hits) / intval($statistic->shoots)), 2) }}
                          @else
                          {{ '0.00' }}
                          @endif
                        </div>
                        <div class="pvp-stats"><span>{{ __('Всего выстрелов') }}:</span>{{ number_format($statistic->shoots, 0, '.', ' ') }}</div>
                        <div class="pvp-stats"><span>{{ __('Всего попаданий') }}:</span>{{ number_format($statistic->hits, 0, '.', ' ') }}</div>
                        <div class="pvp-stats"><span>{{ __('Попаданий в голову') }}:</span>{{ number_format($statistic->head_shots, 0, '.', ' ') }}</div>
                        <div class="pvp-stats">
                          <button><a href="{{ route('account.stats', $statistic->player_id) }}?server_id={{ $server->id }}">{{ __('Смотреть Профиль') }}</a></button>
                        </div>
                      </li>
                    @endforeach

                  </ul>

                  <div class="pagination">
                      {{ $server_statistics[$server->id]['statistics']->links('layouts.pagination.stat-sort') }}
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
        let sort_query = "&" + $(this).data('sort') + "=undefined";

        location.href = "{{ route('stats') }}?type=" + $(this).data('type') + "&search=" + '{{ request()->query('search') }}' + sort_query + "&server_id=" + $(this).data('server_id');
      });
      $('input[name="search"]').on('change', function () {
        console.log($(this).val());
        let sort_query = "&" + $(this).data('sort') + "=undefined";

        location.href = "{{ route('stats') }}?type=" + '{{ request()->query('type') }}' + "&search=" + $(this).val() + sort_query + "&server_id=" + $(this).data('server_id');
      });
      $('.res-sort').on('click', function () {
        console.log($(this).data('res_sort'));
        location.href = "{{ route('stats') }}?type=" + '{{ request()->query('type') }}' + "&search=" + '{{ request()->query('search') }}' + "&res_sort=" + $(this).data('res_sort') + "&server_id=" + $(this).data('server_id');
      });
      $('.pvp-sort').on('click', function () {
        console.log($(this).data('pvp_sort'));
        location.href = "{{ route('stats') }}?type=" + '{{ request()->query('type') }}' + "&search=" + '{{ request()->query('search') }}' + "&pvp_sort=" + $(this).data('pvp_sort') + "&server_id=" + $(this).data('server_id');
      });
      $('.raids-sort').on('click', function () {
        console.log($(this).data('raids_sort'));
        location.href = "{{ route('stats') }}?type=" + '{{ request()->query('type') }}' + "&search=" + '{{ request()->query('search') }}' + "&raids_sort=" + $(this).data('raids_sort') + "&server_id=" + $(this).data('server_id');
      });
      $('.raids_doors-sort').on('click', function () {
        console.log($(this).data('raids_doors_sort'));
        location.href = "{{ route('stats') }}?type=" + '{{ request()->query('type') }}' + "&search=" + '{{ request()->query('search') }}' + "&raids_doors_sort=" + $(this).data('raids_doors_sort') + "&server_id=" + $(this).data('server_id');
      });
      $('.hits-sort').on('click', function () {
        console.log($(this).data('hits_sort'));
        location.href = "{{ route('stats') }}?type=" + '{{ request()->query('type') }}' + "&search=" + '{{ request()->query('search') }}' + "&hits_sort=" + $(this).data('hits_sort') + "&server_id=" + $(this).data('server_id');
      });

      $('.stats-nav ul li').on('click', function () {
        console.log($(this).data('server_id'));
          let server_id = $(this).data('server_id');
          $('.pagination a').each(function (index) {
            let link = this.href.replace(/(server_id=)([^&]+)/, '$01' + server_id);
            this.href = link;
          });
      });

      $('.stats-content-nav ul li').on('click', function () {
        let sort_page = $(this).data('sort');
        $('.pagination a').each(function (index) {
          let link = this.href;
          if(link.indexOf('res_sort') < 0 && link.indexOf('pvp_sort') < 0 && link.indexOf('raids_sort') < 0 && link.indexOf('hits_sort') < 0) {
            link = link + '&' + sort_page + '=';
          }
          console.log(sort_page);
          link = link.replace(/(res_sort)[^&]+/ig, sort_page + '=');
          link = link.replace(/(pvp_sort)([^&]+)/, sort_page + '=');
          link = link.replace(/(raids_sort)([^&]+)/, sort_page + '=');
          link = link.replace(/(hits_sort)([^&]+)/, sort_page + '=');
          this.href = link;
        });
      });

      $( document ).ready(function() {
        if('{{ request()->has('res_sort') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#resources-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#resources_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        } else if('{{ request()->has('pvp_sort') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#pvp-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#pvp_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        } else if('{{ request()->has('raids_sort') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#raids-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#raids_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        } else if('{{ request()->has('raids_doors_sort') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#raids_doors-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#raids_doors_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        } else if('{{ request()->has('hits_sort') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#hits-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#hits_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        } else if('{{ request()->has('type') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#pvp-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#pvp_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        } else if('{{ request()->has('search') }}' != '') {
          $('.stat-tab_{{ request()->query('server_id') }} ul li').removeClass('active');
          $('#pvp-tab_{{ request()->query('server_id') }}').addClass('active');
          $('.stat-tab_{{ request()->query('server_id') }}').removeClass('active');
          $('#pvp_{{ request()->query('server_id') }}').addClass('active');

          $('.stats-nav ul li').removeClass('active');
          $('.stats-nav_{{ request()->query('server_id') }}').addClass('active');
          $('.stats-tab').removeClass('active');
          $('#main_{{ request()->query('server_id') }}').addClass('active');

          $('html, body').animate({
            scrollTop: $(".tab").offset().top+200
          }, 1000);
        }

      });

  </script>
@endpush