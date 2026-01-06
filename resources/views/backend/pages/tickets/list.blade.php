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
                                                <div class="card-tools" style="width: 150px;">
                                                        <div class="form-control-wrap">
                                                            <div class="form-icon form-icon-left">
                                                                <em class="icon ni ni-search"></em>
                                                            </div>

                                                            <select class="form-select form-control" id="tickets_status" name="tickets_status">
                                                                <option value="0" @if($tickets_status == '0') selected @endif>{{ __('Все') }}</option>
                                                                <option value="1" @if($tickets_status == '1') selected @endif>{{ __('Открытые') }}</option>
                                                                <option value="2" @if($tickets_status == '2') selected @endif>{{ __('Закрытые') }}</option>
                                                                <option value="3" @if($tickets_status == '3') selected @endif>{{ __('Удалённые') }}</option>
                                                                <option value="4" @if($tickets_status == '4') selected @endif>{{ __('Не прочитанные') }}</option>
                                                            </select>

                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="simplebar-mask" style="position: relative;">
                                            <div class="simplebar-offset" style="position: relative;">
                                                <div class="simplebar-content-wrapper"
                                                     style="max-height: max-content;height: auto;">
                                                    <div class="simplebar-content" style="padding: 0px;">
                                                        @foreach($tickets as $ticket)
                                                            <div class="nk-ibx-item is-unread">
                                                                <a href="{{ route('backend.tickets.show', $ticket) }}"
                                                                   class="nk-ibx-link"></a>
                                                                <div class="nk-ibx-item-elem nk-ibx-item-user">
                                                                    <div class="user-card">
                                                                        <div class="user-name">
                                                                            <div class="lead-text">{{ $ticket->user->name }}</div>
                                                                            <span class="text-gray">{{ $ticket->user->email }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="nk-ibx-item-elem nk-ibx-item-fluid">
                                                                    <div class="nk-ibx-context-group">
                                                                        <div class="nk-ibx-context-badges">
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
                                                                        </div>
                                                                        <div class="nk-ibx-context">
                                                        <span class="nk-ibx-context-text">
                                                            <span class="heading">{{ $ticket->title }}</span>

                                                            @php
                                                                $histories = NULL;
                                                                if(isset($ticket) && $ticket->history !== NULL) {
                                                                    $histories = json_decode($ticket->history);
                                                                }
                                                            @endphp
                                                            @if (isset($histories) && $histories !== NULL)
                                                                @foreach($histories as $history)
                                                                    @if($loop->last)
                                                                        {{ Str::limit(str_replace("<br>", "\r\n", $history->text), 64) }}
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                {{ Str::limit($ticket->question, 64) }}
                                                            @endif
                                                        </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($ticket->attachment)
                                                                    <div class="nk-ibx-item-elem nk-ibx-item-attach">
                                                                        <a class="link link-light"> <em
                                                                                    class="icon ni ni-clip-h"></em> </a>
                                                                    </div>
                                                                @endif
                                                                <div class="nk-ibx-item-elem nk-ibx-item-time">
                                                                    <div class="sub-text">{{ $ticket->updated_at->format('d/m/y') }}</div>
                                                                    <div class="sub-text">{{ $ticket->updated_at->format('H:i') }}</div>
                                                                </div>
                                                                <div class="nk-ibx-item-elem nk-ibx-item-tools">
                                                                    <div class="ibx-actions">
                                                                        <ul class="ibx-actions-hidden gx-1">
                                                                            <li>
                                                                                <a href="{{ route('tickets.delete', $ticket) }}"
                                                                                   class="btn btn-sm btn-icon btn-trigger"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="top" title=""
                                                                                   data-original-title="{{ __('Удалить') }}">
                                                                                    <em class="icon ni ni-trash"></em>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                        <ul class="ibx-actions-visible gx-2">
                                                                            <li>
                                                                                <div class="dropdown"><a href="#"
                                                                                                         class="dropdown-toggle btn btn-sm btn-icon btn-trigger"
                                                                                                         data-toggle="dropdown"><em
                                                                                                class="icon ni ni-more-h"></em></a>
                                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                                        <ul class="link-list-opt no-bdr">
                                                                                            <li><a class="dropdown-item"
                                                                                                   href="{{ route('tickets.delete', $ticket) }}"><em
                                                                                                            class="icon ni ni-trash"></em><span>{{ __('Удалить') }}</span></a>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner">
                                        {{ $tickets->links('layouts.pagination.cabinet') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .nk-block -->

@endsection

@prepend('scripts')
    <div class="modal fade zoom" tabindex="-1" id="createTicket">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Создать запрос') }}</h5>
                </div>
                @if ($errors->any())
                    <script>
                        $(document).ready(function () {
                            $('#createTicket').modal('show');
                        });
                    </script>
                @endif
                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="title">{{ __('Тема запроса') }}</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control d-inline @error('title') is-invalid @enderror" id="title" name="title">
                                @error('title')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="question">{{ __('Вопрос') }}</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control d-inline @error('title') is-invalid @enderror" id="question" name="question">{{ old('question') }}</textarea>
                                @error('question')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="attachment">{{ __('Скриншот') }}</label>
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
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-lg btn-primary">{{ __('Создать') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('#tickets_status').on('change', function() {
            document.location.replace('{{ route('tickets.all') }}?status='+this.value);
        });
    </script>
@endprepend