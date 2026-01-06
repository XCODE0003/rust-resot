<div class="nav-right">

    @include('partials.main.header-languages')

    @if(isset(auth()->user()->id))

        <button class="user-logged">
            <div class="user-logged-dropdown">
                <span>
                    <span class="user-picture"><img src="{{ auth()->user()->avatar }}"></span>
                    <span class="user-name-balance">
                        {{ auth()->user()->name }}
                        <a href="{{ route('account.profile', 'topup=1') }}">{{ __('Баланс') }}: <i class="account-balance">{{ getCurrentUserBalance() }}</i></a>
                    </span>
                </span>

                <span><i class="fa-solid fa-angle-down"></i></span>
                <ul class="user-logged-dropdown-list">

                    {{--
                    <li>SteamID: <span class="steamid steamid-copy" data-steamid="{{ auth()->user()->steam_id }}">{{ auth()->user()->steam_id }}</span></li>
                    --}}

                    <li><a href="{{ route('account.profile') }}"><i class="fa-solid fa-user"></i>{{ __('Мой Профиль') }}</a></li>
                    <li><a href="{{ route('promocode') }}"><i class="fa-solid fa-gifts"></i>{{ __('Промокод') }}</a></li>
                    {{--
                    <li><a href="{{ route('tickets') }}"><i class="fa-solid fa-envelope"></i>{{ __('Тикеты') }}</a></li>
                    --}}
                    {{--
                    <li><a href="{{ route('account.stats', auth()->user()->steam_id) }}"><i class="fa-solid fa-ranking-star"></i>{{ __('Статистика') }}</a></li>
                    --}}
                    <li><a href="{{ route('logout') }}"><i class="fa-solid fa-arrow-right-from-bracket"></i>{{ __('Выход') }}</a></li>
                </ul>
            </div>
        </button>

    @else
        <form action="{{ route('authenticateSteam') }}" method="POST">
            @csrf
            <button type="submit" class="login"><div><i class="fa-brands fa-steam"></i><span>{{ __('Войти') }}</span></div></button>
        </form>
    @endif
</div>

@push('scripts')
    <script>
        $('.steamid-copy').on('click', function() {

            let temp = $("<input>");
            $("body").append(temp);
            let link = $(this).data('steamid');
            console.log(link);
            temp.val(link).select();
            document.execCommand("copy");
            temp.remove();

            $('#alert-msg').text('{{ __('SteamID скопирован в буфер обмена!') }}');
            $('.alert-modal').show();

            $(this).addClass('is-copied');
            setTimeout(function(){
                $('.steamid-copy').removeClass('is-copied');
            }, 2000);

        });
    </script>
@endpush