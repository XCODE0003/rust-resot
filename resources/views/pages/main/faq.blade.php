@extends('layouts.main')

@section('title', __('Частые вопросы') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
  <meta name="description" content="Creating tickets and answering the most popular questions about the project.">
@endprepend

@section('content')

  <div class="inner-header">{{ __('Частые вопросы') }}</div>
  
  <div class="inner">

    <div class="container">
      <div class="ticket faq">
        <h1>{{ __('Частые вопросы') }}</h1>

        <ul class="faq-list">

          @foreach($faqs as $faq)
            @php
              $question = "question_" .app()->getLocale();
              $answer = "answer_" .app()->getLocale();
            @endphp

            <li class="faq-list-li">
              <div class="faq-list-title"><span>{{ $faq->$question }}</span> <i class="fa-solid fa-angle-down"></i></div>
              <div class="faq-list-text">{{ $faq->$answer }}</div>
            </li>

          @endforeach

        </ul>
      </div>
    </div>
  </div>

@endsection
@push('scripts')
  <script src="/js/ticket.js"></script>
  <script src="/js/ticket_add.js?ver=1.1"></script>
@endpush