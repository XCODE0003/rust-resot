@extends('layouts.main')

@section('title', __('Новости') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@php $title = "title_" .app()->getLocale(); @endphp

@prepend('meta')
  <meta name="description" content="All services that can be purchased on Rust Resort game servers.">
@endprepend

@section('content')

  <div class="inner-header">{{ __('Новости') }}</div>
  <div class="inner">
    <div class="container">
      <div class="news">

        <div class="news-list">
          @foreach($articles as $article)
            <a href="{{ route('news.show', $article->path) }}" class="news-content">
              <div class="news-content-img"><img src="{{ $article->image_url }}"></div>
              <div class="news-content-text">
                <span>{{ getmonthname($article->updated_at->format('m')) }} {{ $article->updated_at->format('d Y') }}</span>
                <h1>{!! $article->$title !!}</h1>
              </div>
            </a>
          @endforeach


        </div>

        <div class="news-pagination">
          {{ $articles->links('layouts.pagination.main') }}
        </div>

      </div>
    </div>
  </div>

@endsection