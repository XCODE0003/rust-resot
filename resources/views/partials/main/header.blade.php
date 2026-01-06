<nav>
    <div class="nav-icon"><a href="{{ route('index') }}"><img src="/images/icon.png"></a></div>

    <ul class="nav-list">
        <li><a class="nav-item @if(url()->current() == route('index')) active @endif" href="{{ route('index') }}">{{ __('Главная') }}</a></li>
        <li><a class="nav-item @if(url()->current() == route('servers')) active @endif" href="{{ route('servers') }}">{{ __('Сервера') }}</a></li>
        <li><a class="nav-item @if(url()->current() == route('shop')) active @endif" href="{{ route('shop') }}">{{ __('Магазин') }}</a></li>
        <li><a class="nav-item @if(url()->current() == route('faq')) active @endif" href="{{ route('faq') }}">{{ __('Помощь') }}</a></li>
        @if(isset(auth()->user()->role) && auth()->user()->role == 'admin')<li><a class="nav-item @if(url()->current() == route('stats')) active @endif" href="{{ route('stats') }}">{{ __('Статистика') }}</a></li>@endif
        {{--<li><a class="nav-item @if(url()->current() == route('cases')) active @endif" href="{{ route('cases') }}">{{ __('Кейсы') }}</a></li>--}}
        {{--
        @if(isset(auth()->user()->role) && auth()->user()->role == 'admin')<li><a class="nav-item @if(url()->current() == route('cases')) active @endif" href="{{ route('cases') }}">{{ __('Кейсы') }}</a></li>@endif
        --}}
    </ul>


    <div class="mobile-menu">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>

    <!--nav right-->
    @include('partials.main.account')
    <!--end nav right-->
</nav>