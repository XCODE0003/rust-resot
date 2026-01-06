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
@section('title', str_replace('<br>', ' ', $guide->$meta_title) . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
  <meta name="description" content="{{ $guide->$meta_description }}">
  <meta name="keywords" content="{{ $guide->$meta_keywords }}">
@endprepend

  @prepend('metah')
    <h1 style="display: none;">{{ $guide->$meta_h1 }}</h1>
    <h2 style="display: none;">{{ $guide->$meta_h2 }}</h2>
    <h3 style="display: none;">{{ $guide->$meta_h3 }}</h3>
  @endprepend

@section('content')

  <div class="inner-header">{{ __('Гайды') }}</div>

  <div class="inner">
    <div class="container">
      <div class="back-news">
        <a href="{{ route('guides') }}"><i class="fa-solid fa-arrow-left"></i> {{ __('Назад к гайдам') }}</a>
      </div>
      <div class="open-new text-page">
        <div class="open-new-title">
          <h1><span>-</span>{!! $guide->$title !!}</h1>
        </div>

        <div class="open-new-text">
          <p>{!! $guide->$description !!}</p>
        </div>
      </div>
    </div>
  </div>

@endsection