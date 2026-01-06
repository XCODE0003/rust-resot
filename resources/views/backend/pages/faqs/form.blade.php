@extends('backend.layouts.backend')

@isset($faq)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать вопрос/ответ'))
    @section('headerDesc', __('Редактирование вопроса/ответа.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить вопрос/ответ'))
    @section('headerDesc', __('Добавление вопроса/ответа.'))
@endisset

@section('headerTitle', __('Частые вопросы'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($faq){{ route('faqs.update', $faq) }}@else{{ route('faqs.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($faq)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset


                        <!-- Tabs -->
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="tab">
                                        @foreach(getlangs() as $key => $value)
                                            @if($loop->index == 0)
                                                <a class="tablinks active" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                            @else
                                                <a class="tablinks" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Tab content -->
                            @foreach(getlangs() as $key => $value)
                                @if($loop->index == 0)
                                    <div id="{{ $key }}" class="tabcontent" style="display: block">
                                        @else
                                            <div id="{{ $key }}" class="tabcontent">
                                                @endif

                                                <div class="row g-4">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            @php
                                                                $question = "question_" . $key;
                                                                $answer = "answer_" . $key;
                                                            @endphp
                                                            <label class="form-label" for="{{ $question }}">{{ __('Вопрос') }} ({{ $key }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="{{ $question }}" name="{{ $question }}"
                                                                       @isset($faq) value="{{ $faq->$question }}" @else value="{{ old($question) }}" @endisset>
                                                                @error('{{ $question }}')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-4">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="{{ $answer }}">{{ __('Ответ') }} ({{ $key }})</label>
                                                            <div class="form-control-wrap">
                                                                <textarea type="text" class="form-control" id="{{ $answer }}" name="{{ $answer }}">@isset($faq) {{ $faq->$answer }} @else {{ old($answer) }} @endisset</textarea>
                                                            </div>
                                                            @error('{{ $answer }}')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            @endforeach


                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label" for="sort">{{ __('Порядок сортировки') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control @error('sort') is-invalid @enderror"
                                                       id="sort" name="sort"
                                                       @isset($faq) value="{{ $faq->sort }}" @else value="{{ old('sort') }}" @endisset required
                                                >
                                                @error('sort')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

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
    <!-- .nk-block -->
@endsection