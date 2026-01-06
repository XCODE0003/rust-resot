@extends('layouts.main')
@php
  $title = "title_" . app()->getLocale();
  $description = "description_" . app()->getLocale();
  $meta_title = "meta_title_" . app()->getLocale();
  $meta_description = "meta_description_" . app()->getLocale();
  $meta_keywords = "meta_keywords_" . app()->getLocale();
  $meta_h1 = "meta_h1_" . app()->getLocale();
  $meta_h2 = "meta_h2_" . app()->getLocale();
  $meta_h3 = "meta_h3_" . app()->getLocale();
@endphp
@section('title', str_replace('<br>', ' ', $article->$meta_title) . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
  <meta name="description" content="{{ $article->$meta_description }}">
  <meta name="keywords" content="{{ $article->$meta_keywords }}">
@endprepend

@section('content')
  @prepend('metah')
    <h1 style="display: none;">{{ $article->$meta_h1 }}</h1>
    <h2 style="display: none;">{{ $article->$meta_h2 }}</h2>
    <h3 style="display: none;">{{ $article->$meta_h3 }}</h3>
  @endprepend

  <div class="inner-header">{{ __('Новости') }}</div>

  <div class="inner">
    <div class="container">
      <div class="back__text">
        <a href="{{ route('news') }}"><i class="fa-solid fa-arrow-left"></i> {{ __('Назад к новостям') }}</a>
      </div>
      <div class="open-new text-page" using-lightbox="">
        <!-- main-title -->
        <h1 class="main-h">
          <span>-</span>
          {!! $article->$title !!}
        </h1>

        <div class="open-new-text">
          <p>{!! $article->$description !!}</p>
        </div>
      </div>
    </div>
  </div>

@endsection