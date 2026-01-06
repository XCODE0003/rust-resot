@php
    $title = "title_" . app()->getLocale();
    $subtitle = "subtitle_" . app()->getLocale();
    $description = "description_" . app()->getLocale();
@endphp
@if(isset(auth()->user()->role) && auth()->user()->role === 'admin' || 1===1)
    @foreach($cases as $case)
    <div class="bonus-content" style="margin-top: {{ 200 * $loop->index }}px;">
        <div class="bonus-block">
            <div class="bonus-present">
                <div class="bonus-img">
                    <img src="{{ $case->image_url }}" alt="{{ $case->$title }}">
                </div>
                <div class="bonus-left">
                    {{ __('Осталось') }} {{ getItemsLeftCase($case->id) }}/{{ $case->prizes_max }}
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
                                if ($case->online_amount == 0) {
                                    $case->online_amount = 1;
                                }
                                $progress_count = intval(getHoursAmount(getOnlineTimeCase($case->id)) / ($case->online_amount / 10));
                                if ($progress_count > 10) $progress_count = 10;
                            @endphp
                            @for($p=0;$p<$progress_count;$p++)
                                <span class="bonus-progress-elem"></span>
                            @endfor
                        @endif
                    </div>
                    <span class="bonus-progress-count">
                        {{ getHoursAmount(getOnlineTimeCase($case->id)) }}/{{ $case->online_amount }}
                    </span>
                </div>
                <div class="bonus-progress-btns-block">
                    <a href="{{ route('cases.show', $case) }}" class="bonus-progress-btn active">
                        {{ __('Открыть кейс') }}
                    </a>
                </div>

            </div>
            <div class="bonus-info">
                <span><i id="bonus-info-{{ $case->id }}" class="fa-solid fa-circle-question"></i></span>
            </div>
        </div>
    </div>

    <div id="modal-bonus-content-{{ $case->id }}" class="modal-bonus-content modal">
        <div class="modal-close"></div>

        <div class="modal-content buying-item">
            <div id="modal-bonus-close-{{ $case->id }}" class="buying-item-close modal-bonus-close"><i class="fa-solid fa-xmark"></i></div>

            <div class="modal-bonus-title">
                <span>{{ __('Информация') }}</span>
            </div>
            <div class="modal-bonus-prizes">
                <p>{!! $case->$description !!}</p>
            </div>

            <div class="modal-bonus-btns-block">
                <a @if(getHoursAmount(getOnlineTimeCase($case->id)) >= $case->online_amount) href="{{ route('cases.show', $case) }}" @endif class="modal-bonus-btn red modal-accept @if(getHoursAmount(getOnlineTimeCase($case->id)) >= $case->online_amount) active @else disable @endif">
                    {{ __('Получить приз') }}
                </a>
            </div>

        </div>
    </div>
    @endforeach
@endif
@push('scripts')
    <script>
        @foreach($cases as $case)
            $('#bonus-info-{{ $case->id }}').click(function() {
                $('#modal-bonus-content-{{ $case->id }}').show();
            });
            $('#modal-bonus-close-{{ $case->id }}').click(function() {
                $('#modal-bonus-content-{{ $case->id }}').hide();
            });
        @endforeach
    </script>
@endpush
