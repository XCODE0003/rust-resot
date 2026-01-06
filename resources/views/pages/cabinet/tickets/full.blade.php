@extends('layouts.main')

@section('title', __('Помощь') . ' - ' . config('options.main_title_'.app()->getLocale(), ''))

@php
    if(isset($ticket) && $ticket->history !== NULL) {
        $histories = json_decode($ticket->history);
    }
@endphp

@section('content')

    <div class="inner-header">{{ __('Помощь') }}</div>

    <div class="inner">

        @include('partials.main.help-menu')

        <div class="container">
            <div class="ticket answer-ticket">

                <div class="inner-nav">
                    <a href="{{ route('tickets') }}"><i class="fa-solid fa-ticket"></i> {{ __('Мои тикеты') }}</a>
                    <a class="active">{{ __('Прочитать тикет') }}</a>
                </div>

                <div class="answer-ticket-subject">
                    <p>{{ __('Тема') }}: <span>{{ $ticket->title }}</span></p>
                    <p>{{ __('SteamID Игрока') }}: <span>{{ $ticket->char_id }}</span></p>
                </div>

                <div class="answer-ticket-content player">
                    <div class="answer-ticket-content-userplace">
                        <div>
                            <div class="answer-ticket-content-userplace-picture"><img src="{{ auth()->user()->avatar }}"></div>
                            <div class="answer-ticket-content-userplace-info">
                                <div class="player">{{ $ticket->user->name }}</div>
                                <div class="id"><p>ID: <span>{{ $ticket->user->steam_id }}</span></p></div>
                            </div>
                        </div>

                        <div>
                            <div class="answer-ticket-content-userplace-date">
                                <p>{{ __('Дата') }}: <span>{{ $ticket->created_at->format('d.m.Y H:i') }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="answer-ticket-content-textplace">
                        <p>{{ $ticket->question }}</p>
                    </div>

                    <div class="answer-ticket-content-attachments">
                        @if ($ticket->attachment)
                            <a class="download" href="{{ $ticket->image_url }}" target="_blank">
                                <i class="fa-solid fa-file"></i>
                                <span>{{ __('Вложение') }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if (isset($histories))

                    @foreach($histories as $history)

                        @if ($history->type == 'question')

                            <div class="answer-ticket-content player">
                                <div class="answer-ticket-content-userplace">
                                    <div>
                                        <div class="answer-ticket-content-userplace-picture"><img src="{{ auth()->user()->avatar }}"></div>
                                        <div class="answer-ticket-content-userplace-info">
                                            <div class="staff-helper">{{ $history->user_name}}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="answer-ticket-content-userplace-date">
                                            <p>{{ __('Дата') }}: <span>{{ $history->updated_at }}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="answer-ticket-content-textplace">
                                    <p>{{ $history->text }}</p>
                                </div>
                                <div class="answer-ticket-content-attachments">
                                    @if (isset($history->attachment) && $history->attachment != '')
                                        <a class="download" href="/storage/{{ $history->attachment }}" target="_blank">
                                            <i class="fa-solid fa-file"></i>
                                            <span>{{ __('Вложение') }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        @else

                            <div class="answer-ticket-content staff">
                                <div class="answer-ticket-content-userplace">
                                    <div>
                                        <div class="answer-ticket-content-userplace-picture"><img src="/images/bg/1-2.jpg"></div>
                                        <div class="answer-ticket-content-userplace-info">
                                            <div class="staff-helper">{{ __('Агент Поддержки') }} #{{ $history->user_id }}
                                                @can('support')
                                                    ({{ $history->user_name }})
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="answer-ticket-content-userplace-date">
                                            <p>{{ __('Дата') }}: <span>{{ $history->updated_at }}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="answer-ticket-content-textplace">
                                    <p>{{ $history->text }}</p>
                                </div>
                                <div class="answer-ticket-content-attachments">
                                    @if (isset($history->attachment) && $history->attachment != '')
                                        <a class="download" href="/storage/{{ $history->attachment }}" target="_blank">
                                            <i class="fa-solid fa-file"></i>
                                            <span>{{ __('Вложение') }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        @endif

                    @endforeach

                @endif

                        @if (!$ticket->trashed() && $ticket->status != 0)
                            <div class="answer-ticket-textarea">
                                <h1>{{ __('Ваш ответ') }}:</h1>
                                <form action="{{ route('tickets.update', $ticket) }}" method="post">
                                    @csrf
                                    <textarea name="answer" placeholder="{{ __('Ваш ответ') }}"></textarea>
                                    <button type="submit">{{ __('Подтвердить') }}</button>
                                </form>
                            </div>
                        @endif
            </div>
        </div>
    </div>
    
@endsection
@push('scripts')
    <script src="/js/ticket.js"></script>
    <script src="/js/ticket_add.js?ver=1.1"></script>
@endpush