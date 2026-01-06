@extends('layouts.main')

@section('title', __('Помощь') . config('options.main_title_'.app()->getLocale(), ''))

@section('content')

    <div class="inner-header">{{ __('Помощь') }}</div>

    <div class="inner">

        @include('partials.main.help-menu')

        <div class="container">

            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="ticket-form">
                @csrf
                <input type="hidden" id="type" name="type" value="1">
                <input type="hidden" id="title" name="title" value="Ban Appeal">
                <input type="hidden" id="server_id" name="server_id" value="1">
                <input type="hidden" id="char_id" name="char_id" value="1">

            <div class="ticket tabs">

                <div class="ticket-nav-tab tab-nav">
                    <h1>{{ __('Выберите тип тикета') }}:</h1>
                    <ul id="select-type">
                        <li><span data-href="#appeal" data-title="Cheat" data-type="1"><i class="fa-solid fa-ghost"></i>{{ __('Читер') }}</span></li>
                        <li><span data-href="#appeal" data-title="Intruder" data-type="2"><i class="fa-solid fa-user-secret"></i>{{ __('Интрудер') }}</span></li>
                        <li><span data-href="#appeal" data-title="Appeal" data-type="3"><i class="fa-solid fa-ban"></i></i>{{ __('Апеляция') }}</span></li>
                        <li><span data-href="#appeal" data-title="Store" data-type="4"><i class="fa-solid fa-shop-slash"></i></i>{{ __('Магазин') }}</span></li>
                        <li><span data-href="#appeal" data-title="Other" data-type="5"><i class="fa-solid fa-gears"></i>{{ __('Другое') }}</span></li>
                        <li><span data-href="#appeal" data-title="Offers" data-type="6"><i class="fa-solid fa-bug-slash"></i>{{ __('Предложения') }}</span></li>
                    </ul>
                </div>


                <div class="ticket-content tab" id="appeal">

                    <div class="ticket-content-server tabs">

                        <div class="server-nav-tab tab-nav">
                            <h1>{{ __('Выберите сервер') }}:</h1>
                            <ul id="select-server">
                                @foreach(getservers() as $server)
                                    <li><span data-href="#main_{{ $server->id }}" data-server="{{ $server->id }}">{{ $server->name }}</span></li>
                                @endforeach
                            </ul>
                        </div>

                        @foreach(getservers() as $server)

                        <div class="server-content tab" id="main_{{ $server->id }}">

                            <div class="server-content-players" id="players">

                                <h1>{{ __('Выберите игрока') }}:</h1>

                                <div class="checkbox-player-not" style="display: none;">
                                    <div style="display: flex;">
                                        <input type="checkbox" class="player_not search" name="player_not" autocomplete="off"><label>{{ __('Выбор игрока не требуется') }}</label>
                                    </div>
                                </div>

                                <div class="server-content-players-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" placeholder="Search for Name or ID" class="search" data-server="{{ $server->id }}" autocomplete="off"/>
                                </div>


                                <div class="search-block">
                                    <!--not found message-->
                                    <div class="not-found not-found_{{ $server->id }}" style="display: none;"><i class="fa-solid fa-circle-xmark"></i>{{ __('Игроки не найдены') }}</div>
                                    <!--end not found message-->

                                    <ul class="list select-char select-char_{{ $server->id }}">

                                    </ul>
                                </div>

                            </div>

                        </div>

                        @endforeach

                        <div class="ticket-content-server">
                            <div class="server-content">

                                <div class="server-content-ticket">
                                    <h1>{{ __('Ваши комментарии') }}:</h1>
                                    <textarea name="question" placeholder="{{ __('Напишите здесь') }}"></textarea>

                                    <h1>{{ __('Вложения') }}:</h1>
                                    <div class="attachment">
                                        <input type="file" accept=".png, .jpg, .jpeg, .webp" id="attachment" name="attachment">
                                    </div>

                                    <div class="ticket-submit">
                                        <button type="submit">{{ __('Отправить Тикет') }}</button>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>

            </div>

            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="/js/ticket.js"></script>
    <script src="/js/ticket_add.js?ver=1.114"></script>

    <script>
        $(document).on('keyup', '.search', function(event){
            $.ajax({
                type: "POST",
                url: "{{ route('tickets.searchPlayer') }}",
                data: { search: $(this).val(), server: $(this).data('server'), _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                let server = $('#server_id').val();
                let html = '';
                let players = data.result;
                $('.select-char_'+server).html('');
                players.forEach(function(player) {
                    if (1==1) {
                        html = '<li data-char="'+player.player_id+'"><div class="player-picture"><img src="'+player.avatar+'"></div><div class="player-info">'+player.name+'<div><div class="player"></div><div>ID: <span class="id">'+player.player_id+'</span></div></div><div><div class="offline">{{ __('Оффлайн') }}</div></div></div></li>';
                    } else {
                        html = '<li data-char="'+player.player_id+'"><div class="player-picture"><img src="'+player.avatar+'"></div><div class="player-info"><div><div class="player">'+player.name+'</div><div>ID: <span class="id">'+player.player_id+'</span></div></div><div><div class="online">{{ __('Онлайн') }}</div></div></div></li>';
                    }
                    $('.select-char_'+server).append(html);
                });
                $('.not-found_'+server).hide();
            });
            event.preventDefault();
        });

        $(document).ready(function() {
            $('.ticket-form').keydown(function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endpush