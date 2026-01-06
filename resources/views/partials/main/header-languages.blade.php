<div class="header__right">
    <div class="header__lang">
                    <span class="header__lang-active">
                        @if(app()->getLocale() == 'en')
                            <span class="lang-icon lang-text">En</span>
                        @elseif(app()->getLocale() == 'ru')
                            <span class="lang-icon lang-text">Ru</span>
                        @elseif(app()->getLocale() == 'de')
                            <span class="lang-icon lang-text">De</span>
                        @elseif(app()->getLocale() == 'fr')
                            <span class="lang-icon lang-text">Fr</span>
                        @elseif(app()->getLocale() == 'it')
                            <span class="lang-icon lang-text">It</span>
                        @elseif(app()->getLocale() == 'es')
                            <span class="lang-icon lang-text">Es</span>
                        @elseif(app()->getLocale() == 'uk')
                            <span class="lang-icon lang-text">Uk</span>
                        @endif
                    </span>

        @php
            $lang_count = 0;
              if(config('options.language1') === 'en') {
                  $lang_count++;
              }
              if(config('options.language2') === 'ru') {
                  $lang_count++;
              }
              if(config('options.language3') === 'de') {
                  $lang_count++;
              }
              if(config('options.language4') === 'fr') {
                  $lang_count++;
              }
              if(config('options.language5') === 'it') {
                  $lang_count++;
              }
              if(config('options.language6') === 'es') {
                  $lang_count++;
              }
              if(config('options.language7') === 'uk') {
                  $lang_count++;
              }
        @endphp

        <div class="header__lang-submenu @if($lang_count < 3) one-lang @endif">


             @if(app()->getLocale() != 'en' && (config('options.language1') !== NULL && config('options.language1') === 'en'))
                <a href="{{ route('setlocale', 'en') }}">
                    <span class="lang-text">En</span>
                </a>
            @endif

                @if(app()->getLocale() != 'ru' && (config('options.language2') !== NULL && config('options.language2') === 'ru'))
                    <a href="{{ route('setlocale', 'ru') }}">
                        <span class="lang-text">Ru</span>
                    </a>
                @endif
                @if(app()->getLocale() != 'de' && (config('options.language3') !== NULL && config('options.language3') === 'de'))
                    <a href="{{ route('setlocale', 'de') }}">
                        <span class="lang-text">De</span>
                    </a>
                @endif
                @if(app()->getLocale() != 'fr' && (config('options.language4') !== NULL && config('options.language4') === 'fr'))
                    <a href="{{ route('setlocale', 'fr') }}">
                        <span class="lang-text">Fr</span>
                    </a>
                @endif
                @if(app()->getLocale() != 'it' && (config('options.language5') !== NULL && config('options.language5') === 'it'))
                    <a href="{{ route('setlocale', 'it') }}">
                        <span class="lang-text">It</span>
                    </a>
                @endif
                @if(app()->getLocale() != 'es' && (config('options.language6') !== NULL && config('options.language6') === 'es'))
                    <a href="{{ route('setlocale', 'es') }}">
                        <span class="lang-text">Es</span>
                    </a>
                @endif
                @if(app()->getLocale() != 'uk' && (config('options.language7') !== NULL && config('options.language7') === 'uk'))
                    <a href="{{ route('setlocale', 'uk') }}">
                        <span class="lang-text">Uk</span>
                    </a>
                @endif

        </div>
        </div>
</div>


@push('scripts')
    <script>
        $(document).ready(function () {

            $(".lang-icon").on("click", function() {
                console.log('lang-icon');
                $('.header__lang-submenu').toggleClass('open');
                $(this).toggleClass('active');
            });

        });
    </script>
@endpush