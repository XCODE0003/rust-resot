<div class="bonus-content" style="top: 315px;">
    <div class="bonus-block">
        <div class="bonus-present">
            <div class="bonus-img">
                <img src="/images/case_thursday.png" alt="bonusth">
            </div>
            <div class="bonus-left">
                {{ __('Left') }} {{ get_bonusth_items_left() }}/25
            </div>

        </div>
        <div class="bonus-progress">
            <div class="bonus-progress-title">
                <span>{{ __('FREE SKINS') }}</span>
            </div>
            <div class="bonus-progress-line">
                <div class="bonus-progress-elems">
                    @if(isset(auth()->user()->online_time_thursday))
                        @php
                            $progress_count = intval(getHoursAmount(auth()->user()->online_time_thursday) / (config('options.bonusth_online_amount', '100') / 10));
                            if ($progress_count > 10) $progress_count = 10;
                        @endphp
                        @for($p=0;$p<$progress_count;$p++)
                            <span class="bonus-progress-elem"></span>
                        @endfor
                    @endif
                </div>
                <span class="bonus-progress-count">
                    @if(isset(auth()->user()->online_time_thursday)){{ getHoursAmount(auth()->user()->online_time_thursday) }}@else{{ '0' }}@endif/{{ config('options.bonusth_online_amount', '100') }}
                </span>
            </div>
            <div class="bonus-progress-btns-block">
                <a href="{{ route('bonus_thursday') }}" class="bonus-progress-btn active">
                    {{ __('SHOW PRIZES') }}
                </a>
                <a @if(isset(auth()->user()->online_time_thursday) && getHoursAmount(auth()->user()->online_time_thursday) >= config('options.bonusth_online_amount', '100')) href="{{ route('bonus_monday') }}" @endif class="bonus-progress-btn @if(isset(auth()->user()->online_time_thursday) && getHoursAmount(auth()->user()->online_time_thursday) >= config('options.bonusth_online_amount', '100')) active @else disable @endif">
                    {{ __('OPEN CASE') }}
                </a>
            </div>

        </div>
        <div class="bonus-info">
            <span><i id="bonusth-info" class="fa-solid fa-circle-question"></i></span>
        </div>
    </div>
</div>


<div class="modal-bonusth-content modal">
    <div class="modal-close"></div>

    <div class="modal-content buying-item" style="top: -100px;">
        <div class="buying-item-close modal-bonusth-close"><i class="fa-solid fa-xmark"></i></div>

            <div class="modal-bonus-title">
                <span>{{ __('Информация') }}</span>
            </div>
            <div class="modal-bonus-prizes">
                <p>{{ config('options.bonusth_description_' . app()->getLocale()) }}</p>
            </div>

            <div class="modal-bonus-btns-block">
                <a @if(isset(auth()->user()->online_time_thursday) && getHoursAmount(auth()->user()->online_time_thursday) >= config('options.bonusth_online_amount', '100')) href="{{ route('bonus_monday') }}" @endif class="modal-bonus-btn red modal-accept @if(isset(auth()->user()->online_time_thursday) && getHoursAmount(auth()->user()->online_time_thursday) >= config('options.bonusth_online_amount', '100')) active @else disable @endif">
                    {{ __('Get a prize') }}
                </a>
            </div>

    </div>
</div>

@push('scripts')
<script>
    $('#bonusth-info').click(function() {
        $('.modal-bonusth-content').show();
    });
    $('.modal-bonusth-close').click(function() {
        $('.modal-bonusth-content').hide();
    });

</script>
@endpush