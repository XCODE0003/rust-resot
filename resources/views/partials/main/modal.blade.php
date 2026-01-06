<div class="login-modal modal">
    <div class="modal-close"></div>

    <div class="modal-content buying-item">
        <div class="buying-item-close login-close"><i class="fa-solid fa-xmark"></i></div>

        <div class="buying-item-name">
            <p>{{ __('Пожалуйста авторизируйтесь') }}</p>
        </div>
            <ul class="buying-item-payment-option">
                <form action="{{ route('authenticateSteam') }}" method="POST">
                    @csrf
                    <button type="submit" class="login"><div><i class="fa-brands fa-steam"></i><span>{{ __('Войти через Steam') }}</span></div></button>
                </form>
            </ul>
    </div>
</div>

<div class="alert-modal modal">
    <div class="modal-close"></div>

    <div class="modal-content buying-item">
        <div class="buying-item-close alert-close"><i class="fa-solid fa-xmark"></i></div>

        <div class="buying-item-name">
            <div id="alert-msg"></div>
        </div>
        <a class="modal-accept"><div>{{ __('Принять') }}</div></a>
    </div>
</div>

<div class="alertip-modal modal">
    <div class="modal-close ip-close"></div>

    <div class="modal-content buying-item">
        <div class="buying-item-close alertip-close"><i class="fa-solid fa-xmark"></i></div>

        <div class="buying-item-name">
            <p id="alertip-msg"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        let auth_login = @if(session()->has('auth_login') && session('auth_login') == 1){{ '1' }}@else{{ '0' }}@endif;
        if(auth_login == 1) {
            $('.login-modal').show();
        }

        $(".login-close").on("click", function() {
            $('.login-modal').hide();
        });

        $(".alert-close").on("click", function() {
            $('.alert-modal').hide();
        });
        $(".modal-close").on("click", function() {
            $('.alert-modal').hide();
        });
        $(".modal-accept").on("click", function() {
            $('.alert-modal').hide();
        });
        $(".ip-close").on("click", function() {
            $('.alertip-modal').hide();
        });
        $(".alertip-close").on("click", function() {
            $('.alertip-modal').hide();
        });



        @foreach (['danger', 'warning', 'success', 'info'] as $type)
            @if(Session::has('alert.' . $type))
                $('.alert-modal').show();
                @foreach(Session::get('alert.' . $type) as $message)
                    $('.alert-modal .buying-item-name').addClass('alert-{{ $type }}');
                    $('#alert-msg').html('{!! $message !!}');
                @endforeach
            @endif
        @endforeach
        @php session()->forget(['alert.danger', 'alert.warning', 'alert.success', 'alert.info']); @endphp

    });
    @php session()->forget('auth_login'); @endphp
</script>
@endpush