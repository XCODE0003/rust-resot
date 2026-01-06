<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="js">
<head>
    <meta charset="utf-8">
    @hasSection('title')
        <title>@yield('title') • {{ config('options.title', 'L2') }}</title>
    @else
        <title>Rust</title>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href={{ '/assets/images/favicon.png' }}>
    <link rel="stylesheet" href="{{ '/assets/css/backend.css?ver=1.1' }}">
    <link rel="stylesheet" href="{{ '/assets/css/dashlite.css' }}">
    <link rel="stylesheet" href="{{ '/assets/css/theme.css' }}">

    @if(session()->has('theme') && session()->get('theme') == 'dark')
        <link rel="stylesheet" href="{{ '/assets/css/backend-dark.css?ver=2' }}">
    @else
        <link rel="stylesheet" href="{{ '/assets/css/theme.css' }}">
    @endif

    <script src="/assets/js/jquery-2.1.1.min.js"></script>
    <script src="/assets/js/ckeditor/ckeditor.js"></script>
  
    @stack('head')
</head>

@yield('body')


<!-- JavaScript -->
<script src="{{ '/assets/js/bundle.js?ver=1.0.0' }}"></script>
<script src="{{'/assets/js/scripts.js?ver=1.0.0' }}"></script>
<script src="{{ '/assets/js/charts/gd-general.js?ver=1.0.0' }}"></script>

@stack('scripts')

</html>
