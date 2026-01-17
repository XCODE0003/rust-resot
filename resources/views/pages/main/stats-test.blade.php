@extends('layouts.main')

@section('title', __('Статистика') . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
  <meta name="description" content="View the statistics of your RustResort survival.">
@endprepend

@section('content')

  <div class="inner-header">{{ __('Статистика') }}</div>

  <div id="rankeval-widget"></div>
@endsection
@push('scripts')

<script src="https://cdn.rankeval.gg/integration/latest/rankeval-widget.js"></script>
@endpush