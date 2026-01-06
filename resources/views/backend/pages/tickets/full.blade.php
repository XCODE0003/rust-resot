@php
    if(isset($ticket) && $ticket->history !== NULL) {
        $histories = json_decode($ticket->history);
    }
@endphp

@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Обращения'))
@section('headerTitle', __('Обращения'))
@section('headerDesc', __('Обращения пользователей.'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <span class="mr-2">{{ $ticket->title }}</span>
                                @if ($ticket->trashed())
                                    <span class="badge badge-gray">{{ __('Удалён') }}</span>
                                @elseif ($ticket->status === 1)
                                    <span class="badge badge-success">{{ __('Открыт') }}</span>
                                @elseif ($ticket->status === 0)
                                    <span class="badge badge-danger">{{ __('Закрыт') }}</span>
                                @endif

                                @if ($ticket->is_read == 1)
                                    <span class="badge badge-green">{{ __('Прочитано') }}</span>
                                @else
                                    <span class="badge badge-orange">{{ __('Новое сообщение') }}</span>
                                @endif
                            </h5>
                        </div>
                        <div class="card-title-group" style="flex-direction: column;align-items: flex-start;">
                            <span>{{ __('SteamID Игрока') }}: <span>{{ $ticket->char_id }}</span></span>
                            <span>{{ __('Сервер') }}: <span>{{ getserver($ticket->server_id)->name }}</span></span>
                        </div>
                    </div>

                    <div class="card-inner p-0 border-top">

                        <div class="nk-reply-item" style="min-height: 230px;">

                            <div class="nk-reply-header">
                                <div class="user-card flex-column align-items-start">
                                    <div class="user-name text-left">{{ $ticket->user->name }}</div>
                                    <div class="ml-3 text-gray">{{ $ticket->user->email }}</div>
                                </div>
                                <div class="date-time">{{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            <div class="nk-reply-body">
                                <div class="nk-reply-entry entry">
                                    <form action="{{ route('backend.tickets.question.update', $ticket) }}" method="post">
                                        @csrf
                                        <textarea type="text" class="form-control" id="question" name="question" style="min-height: auto;">{{ $ticket->question }}</textarea>
                                        <ul class="nk-reply-form-actions g-1" style="display: flex;float: right;padding-top: 40px;">
                                            <li class="mr-2">
                                                <button class="btn btn-primary" type="submit">{{ __('Обновить') }}</button>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                @if ($ticket->attachment)
                                    <div class="attach-files">
                                        <ul class="attach-list">
                                            <li class="attach-item">
                                                <a class="download" href="{{ $ticket->image_url }}" target="_blank">
                                                    <em class="icon ni ni-img"></em>
                                                    <span>{{ __('Изображение') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if (isset($histories))

                            @foreach($histories as $history)

                                @if ($history->type == 'question')


                                    <div class="nk-reply-item">
                                        <div class="nk-reply-header">
                                            <div class="user-card flex-column align-items-start">
                                                <div class="user-name text-left">{{ $history->user_name}}</div>
                                            </div>
                                            <div class="date-time">{{ $history->updated_at }}</div>
                                        </div>
                                        <div class="nk-reply-body">
                                            <div class="nk-reply-entry entry">
                                                <form action="{{ route('backend.tickets.reply.update', $ticket) }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="reply_index" value="{{ $loop->iteration }}">
                                                    <textarea type="text" class="form-control" id="reply-{{ $loop->iteration }}" name="reply" style="min-height: auto;">{!! str_replace("<br>", "\r\n", $history->text) !!}</textarea>
                                                    <ul class="nk-reply-form-actions g-1" style="display: flex;float: right;padding-top: 15px;">
                                                        <li class="mr-2">
                                                            <button class="btn btn-primary" type="submit">{{ __('Обновить') }}</button>
                                                        </li>
                                                    </ul>
                                                </form>
                                            </div>
                                            @if (isset($history->attachment) && $history->attachment != '')
                                                <div class="attach-files">
                                                    <ul class="attach-list">
                                                        <li class="attach-item">
                                                            <a class="download" href="/storage/{{ $history->attachment }}" target="_blank">
                                                                <em class="icon ni ni-img"></em>
                                                                <span>{{ __('Вложение') }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                @else

                                    <div class="nk-reply-item">
                                        <div class="nk-reply-header">
                                            <div class="user-card flex-column align-items-start">
                                                <div class="user-name text-left">
                                                    {{ __('Агент Поддержки') }} #{{ $history->user_id }}
                                                    @can('support')
                                                        ({{ $history->user_name }})
                                                    @endcan
                                                </div>
                                            </div>
                                            <div class="date-time">{{ $history->updated_at }}</div>
                                        </div>
                                        <div class="nk-reply-body">
                                            <div class="nk-reply-entry entry">
                                                <form action="{{ route('backend.tickets.reply.update', $ticket) }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="reply_index" value="{{ $loop->iteration }}">

                                                    <textarea type="text" class="form-control" id="reply-{{ $loop->iteration }}" name="reply" style="min-height: auto;">{!! str_replace("<br>", "\r\n", $history->text) !!}</textarea>
                                                    <ul class="nk-reply-form-actions g-1" style="display: flex;float: right;padding-top: 15px;">
                                                        <li class="mr-2">
                                                            <button class="btn btn-primary" type="submit">{{ __('Обновить') }}</button>
                                                        </li>
                                                    </ul>
                                                </form>
                                            </div>
                                            @if (isset($history->attachment) && $history->attachment != '')
                                                <div class="attach-files">
                                                    <ul class="attach-list">
                                                        <li class="attach-item">
                                                            <a class="download" href="/storage/{{ $history->attachment }}" target="_blank">
                                                                <em class="icon ni ni-img"></em>
                                                                <span>{{ __('Вложение') }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                @endif

                            @endforeach


                        @else




                            @if ($ticket->answer)
                                <div class="nk-reply-item">
                                    <div class="nk-reply-header">
                                        <div class="user-card flex-column align-items-start">
                                            <div class="user-name text-left">
                                                {{ __('Агент Поддержки') }} #{{ $ticket->answerer_id }}
                                                @can('support')
                                                    ({{ $ticket->answerer_id }})
                                                @endcan
                                            </div>
                                        </div>
                                        <div class="date-time">{{ $ticket->updated_at->format('d.m.Y H:i') }}</div>
                                    </div>
                                    <div class="nk-reply-body">
                                        <div class="nk-reply-entry entry">
                                            <p>{{ $ticket->answer }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endif

                        @if (auth()->user()->can('support') && !$ticket->trashed() && $ticket->status != 0)
                            <div class="nk-reply-form">
                                <div class="nk-reply-form-header">
                                    <ul class="nav nav-tabs-s2 nav-tabs nav-tabs-sm">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#reply-form">{{ __('Ответить') }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="reply-form">
                                        <form action="{{ route('backend.tickets.update', $ticket) }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="nk-reply-form-editor">
                                                <div class="nk-reply-form-field">
                                                    <textarea class="form-control form-control-simple no-resize p-3" name="answer" placeholder=""></textarea>
                                                </div>
                                                <div class="nk-reply-form-field">
                                                    <div class="form-group">
                                                        <label class="form-label" for="attachment">{{ __('Вложение') }}</label>
                                                        <div class="form-control-wrap">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="attachment" name="attachment">
                                                                <label class="custom-file-label" for="attachment">{{ __('Выбрать файл') }}</label>
                                                            </div>
                                                            @error('attachment')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-reply-form-tools">
                                                    <ul class="nk-reply-form-actions g-1">
                                                        <li class="mr-2"><button class="btn btn-primary" type="submit">{{ __('Отправить') }}</button></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="nk-reply-form-editor">
                                        <div class="nk-reply-form-tools">
                                            <form action="{{ route('backend.tickets.isread', $ticket) }}" method="post">
                                                @csrf
                                                <ul class="nk-reply-form-actions g-1">
                                                    <li class="mr-2"><button class="btn btn-primary btn-read" type="submit">{{ __('Прочитано') }}</button></li>
                                                </ul>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="nk-reply-form-editor form-close">
                                        <div class="nk-reply-form-tools">
                                            <form action="{{ route('backend.tickets.close', $ticket) }}" method="post">
                                                @csrf
                                                <ul class="nk-reply-form-actions g-1">
                                                    <li class="mr-2"><button class="btn btn-primary btn-close" type="submit">{{ __('Закрыть') }}</button></li>
                                                </ul>
                                            </form>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection