<div class="bonus-content" style="top: 315px;">
    <div class="bonus-block">
        <div class="bonus-present">
            <div class="bonus-img">
                <img src="/images/case_monday.png" alt="bonusm">
            </div>
            <div class="bonus-left">
                {{ __('Left') }} {{ get_bonusm_items_left() }}/25
            </div>

        </div>
        <div class="bonus-progress">
            <div class="bonus-progress-title">
                <span>{{ __('FREE SKINS') }}</span>
            </div>
            <div class="bonus-progress-line">
                <div class="bonus-progress-elems">
                    @if(isset(auth()->user()->online_time_monday))
                        @php
                            $progress_count = intval(getHoursAmount(auth()->user()->online_time_monday) / (config('options.bonusm_online_amount', '100') / 10));
                            if ($progress_count > 10) $progress_count = 10;
                        @endphp
                        @for($p=0;$p<$progress_count;$p++)
                            <span class="bonus-progress-elem"></span>
                        @endfor
                    @endif
                </div>
                <span class="bonus-progress-count">
                    @if(isset(auth()->user()->online_time_monday)){{ getHoursAmount(auth()->user()->online_time_monday) }}@else{{ '0' }}@endif/{{ config('options.bonusm_online_amount', '100') }}
                </span>
            </div>
            <div class="bonus-progress-btns-block">
                <a href="{{ route('bonus_monday') }}" class="bonus-progress-btn active">
                    {{ __('SHOW PRIZES') }}
                </a>
                <a @if(isset(auth()->user()->online_time_monday) && getHoursAmount(auth()->user()->online_time_monday) >= config('options.bonusm_online_amount', '100')) href="{{ route('bonus_monday') }}" @endif class="bonus-progress-btn @if(isset(auth()->user()->online_time_monday) && getHoursAmount(auth()->user()->online_time_monday) >= config('options.bonusm_online_amount', '100')) active @else disable @endif">
                    {{ __('OPEN CASE') }}
                </a>
            </div>

        </div>
        <div class="bonus-info">
            <span><i id="bonusm-info" class="fa-solid fa-circle-question"></i></span>
        </div>
    </div>
</div>


<div class="modal-bonusm-content modal">
    <div class="modal-close"></div>

    <div class="modal-content buying-item" style="top: -100px;">
        <div class="buying-item-close modal-bonusm-close"><i class="fa-solid fa-xmark"></i></div>

            <div class="modal-bonus-title">
                <span>{{ __('Информация') }}</span>
            </div>
            <div class="modal-bonus-prizes">
                <p>{{ config('options.bonusm_description_' . app()->getLocale()) }}</p>
            </div>

            <div class="modal-bonus-btns-block">
                <a @if(isset(auth()->user()->online_time_monday) && getHoursAmount(auth()->user()->online_time_monday) >= config('options.bonusm_online_amount', '100')) href="{{ route('bonus_monday') }}" @endif class="modal-bonus-btn red modal-accept @if(isset(auth()->user()->online_time_monday) && getHoursAmount(auth()->user()->online_time_monday) >= config('options.bonusm_online_amount', '100')) active @else disable @endif">
                    {{ __('Get a prize') }}
                </a>
            </div>

    </div>
</div>

@push('scripts')
<script>
    $('#bonusm-info').click(function() {
        $('.modal-bonusm-content').show();
    });
    $('.modal-bonusm-close').click(function() {
        $('.modal-bonusm-content').hide();
    });

</script>
@endpush