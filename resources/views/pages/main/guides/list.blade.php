@extends('layouts.main')

@section('title', __('Гайды') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@php
  $title = "title_" .app()->getLocale();
  $meta_keywords = "meta_keywords_" .app()->getLocale();
 @endphp

@prepend('meta')
  <meta name="description" content="You can see the guides about the game Rust.">
@endprepend

@section('content')

  <div class="inner-header">{{ __('Гайды') }}</div>
  <div class="inner">
    <div class="container" style="display: flex;">
      <div class="news" style="width: 80%;">

        <div class="news-list">
          @foreach($guides as $guide)
            @php $category = getguidecategory($guide->category_id); @endphp
            <a href="{{ route('guides.show', [(isset($category->path)) ? $category->path : 'all', (isset($guide->path) && $guide->path !== NULL && $guide->path !== '') ? $guide->path : 'not-found']) }}" class="news-content">
              <div class="news-content-img"><img src="{{ $guide->image_url }}"></div>
              <div class="news-content-text">
                <span>{{ getmonthname($guide->updated_at->format('m')) }} {{ $guide->updated_at->format('d Y') }}</span>
                <span class="tags">
                  @foreach(getGuideTags($guide->$meta_keywords) as $tag)
                    <span>{{ $tag }}</span>
                  @endforeach
                </span>
                <h1>{!! $guide->$title !!}</h1>
              </div>
            </a>
          @endforeach

        </div>

        <div class="news-pagination">
          {{ $guides->links('layouts.pagination.main') }}
        </div>

      </div>

      @include('pages.main.guides.partials.categories')

    </div>
  </div>

@endsection