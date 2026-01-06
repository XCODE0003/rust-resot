<div class="languages" style="margin-left: 18px;">
    <a href="{{ route('setlocale', 'ru') }}"
       @class(['languages__item ru', 'active' => app()->getLocale() === 'ru'])
       title="Русский"></a>
    <a href="{{ route('setlocale', 'en') }}"
       @class(['languages__item en', 'active' => app()->getLocale() === 'en'])
       title="English"></a>
</div>
