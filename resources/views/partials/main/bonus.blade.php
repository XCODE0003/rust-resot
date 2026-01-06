@if(url()->current() == route('cases'))
<div class="bonus-content">
    <div class="bonus-block">
        <div class="bonus-present">
            <div class="bonus-img">
                <img src="/images/bonuse_case_600.png" alt="bonus">
            </div>
            <div class="bonus-left">
                {{ __('Left') }} {{ get_bonus_items_left() }}/100
            </div>

        </div>
        <div class="bonus-progress">
            <div class="bonus-progress-title">
                <span>{{ __('FREE SKINS') }}</span>
            </div>
            <div class="bonus-progress-line">
                <div class="bonus-progress-elems">
                    @if(isset(auth()->user()->online_time))
                        @php
                            $progress_count = intval(getHoursAmount(auth()->user()->online_time) / (config('options.bonus_online_amount', '100') / 10));
                            if ($progress_count > 10) $progress_count = 10;
                        @endphp
                        @for($p=0;$p<$progress_count;$p++)
                            <span class="bonus-progress-elem"></span>
                        @endfor
                    @endif
                </div>
                <span class="bonus-progress-count">
                    @if(isset(auth()->user()->online_time)){{ getHoursAmount(auth()->user()->online_time) }}@else{{ '0' }}@endif/{{ config('options.bonus_online_amount', '100') }}
                </span>
            </div>
            <div class="bonus-progress-btns-block">
                <a href="{{ route('bonus') }}" class="bonus-progress-btn active">
                    {{ __('SHOW PRIZES') }}
                </a>
                <a @if(isset(auth()->user()->online_time) && getHoursAmount(auth()->user()->online_time) >= config('options.bonus_online_amount', '100')) href="{{ route('bonus') }}" @endif class="bonus-progress-btn @if(isset(auth()->user()->online_time) && getHoursAmount(auth()->user()->online_time) >= config('options.bonus_online_amount', '100')) active @else disable @endif">
                    {{ __('OPEN CASE') }}
                </a>
            </div>

        </div>
        <div class="bonus-info">
            <span><i id="bonus-info" class="fa-solid fa-circle-question"></i></span>
        </div>
    </div>
</div>


<div class="modal-bonus-content modal">
    <div class="modal-close"></div>

    <div class="modal-content buying-item">
        <div class="buying-item-close modal-bonus-close"><i class="fa-solid fa-xmark"></i></div>

            <div class="modal-bonus-title">
                <span>{{ __('Информация') }}</span>
            </div>
            <div class="modal-bonus-prizes">
                <p>{{ config('options.bonus_description_' . app()->getLocale()) }}</p>
            </div>

            <div class="modal-bonus-btns-block">
                <a @if(isset(auth()->user()->online_time) && getHoursAmount(auth()->user()->online_time) >= config('options.bonus_online_amount', '100')) href="{{ route('bonus') }}" @endif class="modal-bonus-btn red modal-accept @if(isset(auth()->user()->online_time) && getHoursAmount(auth()->user()->online_time) >= config('options.bonus_online_amount', '100')) active @else disable @endif">
                    {{ __('Get a prize') }}
                </a>
            </div>

    </div>
</div>
@endif

@push('scripts')
<script>
    $('#bonus-info').click(function() {
        $('.modal-bonus-content').show();
    });
    $('.modal-bonus-close').click(function() {
        $('.modal-bonus-content').hide();
    });

</script>
@endpush