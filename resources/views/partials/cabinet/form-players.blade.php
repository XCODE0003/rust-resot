<div class="search-block" style="display: none;">

    <!--not found message-->
        <div class="not-found"><i class="fa-solid fa-circle-xmark"></i>{{ __('Игроки не найдены') }}</div>
    <!--end not found message-->

    <ul class="list select-char">

            <li data-char="">
                <div class="player-picture"><img src="/images/bg/1-4.jpg"></div>
                <div class="player-info">
                    <div>
                        <div class="player"></div>
                        <div>ID: <span class="id"></span></div>
                    </div>
                    <div>
                        <div class="offline" style="display: none;">{{ __('Оффлайн') }}</div>
                        <div class="online" style="display: none;">{{ __('Онлайн') }}</div>
                    </div>
                </div>
            </li>

    </ul>
</div>