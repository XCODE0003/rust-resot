@extends('layouts.main')

@section('title', __('Помощь') . ' - ' . config('options.main_title_'.app()->getLocale(), ''))

@section('content')

    <div class="inner-header">{{ __('Помощь') }}</div>
    <div class="inner">

        @include('partials.main.help-menu')

        <div class="container">
            <div class="ticket my-ticket">
                <h1>{{ __('Мои тикеты') }}</h1>

                <ul class="my-ticket-list">

                    @foreach($tickets as $ticket)
                        <li>
                            <div class="ticket-info">
                                <div class="ticket-info-subject"><p>{{ __('Тема') }}: <span>{{ $ticket->title }}</span></p></div>
                                <div class="ticket-info-date"><p>{{ __('Дата') }}: <span>{{ $ticket->created_at->format('d.m.Y H:i') }}</span></p></div>
                            </div>
                                <div class="ticket-status">
                                <div>
                                    <p>{{ __('Статус') }}:
                                        @if ($ticket->trashed())
                                            <span class="gray">{{ __('Удалён') }}</span>
                                        @elseif ($ticket->status == 1)
                                            <span class="green">{{ __('Открыт') }}</span>
                                        @elseif ($ticket->status == 0)
                                            <span class="red">{{ __('Закрыт') }}</span>
                                        @endif
                                    </p>
                                </div>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="@if($ticket->user_is_read == 1){{ 'read' }}@else{{ 'unread' }}@endif"><i class="fa-solid @if($ticket->answer === NULL){{ 'fa-eye' }}@else{{ 'fa-eye' }}@endif"></i></a>
                            </div>
                        </li>
                    @endforeach

                <div class="pagination">
                    {{ $tickets->links('layouts.pagination.main') }}
                </div>
                </ul>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="/js/ticket.js"></script>
    <script src="/js/ticket_add.js?ver=1.1"></script>
@endpush