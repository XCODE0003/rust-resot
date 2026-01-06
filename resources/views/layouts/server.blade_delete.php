<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="image/favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <base href="{{ config('app.url', '') }}">
    @hasSection('title')
        <title>@yield('title') • {{ config('options.title', 'WarsTown') }}</title>
    @else
        <title>{{ config('options.title', 'WarsTown') }}</title>
    @endif
    <link rel="stylesheet" href="fonts/stylesheet.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link rel="stylesheet" href="css/addition.css">
    <link rel="stylesheet" href="css/video-headers.css">

    <script src="{{ asset('assets/js/jquery-2.1.1.min.js') }}"></script>


</head>
<body class="@yield('serverName')">

<!-- Header -->
@include('partials.main.server-header')

<!-- Main -->
@yield('main')

<!-- Discord -->
@include('partials.main.server-discord')

<!-- Vote -->
@include('partials.main.server-vote')


<!-- Footer -->
@include('partials.main.server-footer')


<!-- Modal -->
@include('partials.main.modal')

<!-- Scripts -->
<div class="md-overlay"></div>

<script src="js/vendor.min.js"></script>

@if ($server_id == '1')
<script src="js/main_home.min_solid.js"></script>
@elseif ($server_id == '2')
<script src="js/main_home.min_improved.js"></script>
@else
<script src="js/main_home.min.js"></script>
@endif


<script src="{{ asset('assets/js/ion.rangeSlider.min.js') }}"></script>

@stack('scripts')

</body>
</html>