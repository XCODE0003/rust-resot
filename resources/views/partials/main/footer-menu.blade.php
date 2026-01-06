<div class="footer-copyright-links">
    <a class="@if(url()->current() == route('term')) active @endif" href="{{ route('term') }}">{{ __('ПОЛЬЗОВАТЕЛЬСКОЕ СОГЛАШЕНИЕ') }}</a>
    <a class="@if(url()->current() == route('policy')) active @endif" href="{{ route('policy') }}">{{ __('ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ') }}</a>
    <a class="@if(url()->current() == route('refund')) active @endif" href="{{ route('refund') }}">{{ __('ПОЛИТИКА ВОЗВРАТА') }}</a>
</div>
