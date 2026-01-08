<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    @hasSection('title')
        <title>@yield('title') • {{ config('options.title', 'Rust') }}</title>
    @else
        <title>Rust</title>
    @endif

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @stack('meta')
    <link rel="icon" type="image/x-icon" href="/images/icon.png">
    <link rel="preload" href="/css/font/Rust.woff2" as="font" crossorigin="anonymous" />
    <link rel="preload" href="/css/font/SalmaPro-MediumNarrow.woff2" as="font" crossorigin="anonymous" />
    <link rel="preload" href="/css/font/Stem-Bold.woff2" as="font" crossorigin="anonymous" />
    <link rel="preload" href="/css/font/Stem-Medium.woff2" as="font" crossorigin="anonymous" />
    <link rel="preload" href="/css/font/AntarcticanHeadline-Medium.woff2" as="font" crossorigin="anonymous" />
    <link rel="preload" href="/css/font/AntarcticanHeadline-Book.woff2" as="font" crossorigin="anonymous" />
    <link rel="preload" href="/css/font/AntarcticanHeadline-Bold.woff2" as="font" crossorigin="anonymous" />


    <link rel="stylesheet" href="/css/font.css">

    <link rel="stylesheet" href="/css/font/stylesheet.css">
    @if(app()->getLocale() == 'ru' || app()->getLocale() == 'uk')
        <link rel="stylesheet" href="/css/ru.css?ver=1.1263">

    @endif
    <link rel="stylesheet" href="/css/main.css?ver=1.{{ strtotime(date('d.m.Y H:i:s')) }}">
    <link rel="stylesheet" href="/css/swiper.css?ver=1.1263">
    <link rel="stylesheet" href="/css/nav.css?ver=1.1263">
    <link rel="stylesheet" href="/css/unsimple.css?ver=1.1263">
    <link rel="stylesheet" href="/css/loading.css?ver=1.1263">
    <link rel="stylesheet" href="/css/inner.css?ver=1.{{ strtotime(date('d.m.Y H:i:s')) }}">
    <link rel="stylesheet" href="/css/inner-additional.css?ver=1.{{ strtotime(date('d.m.Y H:i:s')) }}">
    <link rel="stylesheet" href="/css/additional-2.css?ver=1.{{ strtotime(date('d.m.Y H:i:s')) }}">
    <link rel="stylesheet" href="/css/additional-3.css?ver=1.{{ strtotime(date('d.m.Y H:i:s')) }}">
    <link rel="stylesheet" href="/css/addition.css?ver=1.{{ strtotime(date('d.m.Y H:i:s')) }}">


    <script src="/assets/js/icons.js" crossorigin="anonymous"></script>


    @stack('head')

    @if(app()->getLocale() == 'ru')
        <style>
            .nav-list li:not(:last-child) {
                padding-right: 40px !important;
            }
        </style>
    @endif

    <meta name="enot" content="41416718117200bzQchTgowUEHQrD9IN5y0rYUSVrh83F" />
    <meta name="verification" content="f12362585eed9635def7eec69a5aaa" />
    <meta name="yandex-verification" content="0935fc81cc2cbfdf" />

    @if(config('options.google_analitics'))
        {!! trim(config('options.google_analitics')) !!}
    @endif
    @if(config('options.yandex_metric'))
        {!! trim(config('options.yandex_metric')) !!}
    @endif

</head>
<body>

@stack('metah')

    @yield('body')

    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/swiper.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/nav.js"></script>
    <script src="/js/loading.js"></script>
    <script src="/js/shop.js"></script>
    <script src="/js/shop_add.js?ver=1.122"></script>
    <script src="/js/snowfall.js"></script>

    @stack('scripts')

    @if(config('options.snowfall_status', '0') === '1')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).snowfall({
                flakeCount: {{ config('options.snowfall_flakecount', '300') }},
                minSize: {{ config('options.snowfall_minsize', '2') }},
                maxSize: {{ config('options.snowfall_maxsize', '10') }},
                minSpeed: {{ config('options.snowfall_minspeed', '1') }},
                maxSpeed: {{ config('options.snowfall_maxspeed', '5') }},
                round: {{ config('options.snowfall_round', 'false') }},
                shadow: {{ config('options.snowfall_shadow', 'false') }},
            });
        });
    </script>
    @endif

</body>
</html>