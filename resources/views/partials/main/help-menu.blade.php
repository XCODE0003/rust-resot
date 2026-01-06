<div class="help-nav">
    <a class="@if(url()->current() == route('tickets.create')) active @endif" href="{{ route('tickets.create') }}"><i class="fa-solid fa-plus"></i><span>{{ __('Создать тикет') }}</span></a>
    <a class="@if(url()->current() == route('tickets') || (strpos(url()->current(), 'account/tickets') != FALSE && strpos(url()->current(), 'account/tickets/create') == FALSE)) active @endif" href="{{ route('tickets') }}"><i class="fa-solid fa-envelope"></i><span>{{ __('Мои заявки') }}</span></a>
    <a class="@if(url()->current() == route('faq')) active @endif" href="{{ route('faq') }}"><i class="fa-solid fa-question"></i><span>{{ __('Частые вопросы') }}</span></a>
</div>