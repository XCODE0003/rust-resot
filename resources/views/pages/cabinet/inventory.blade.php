@php
    $title = "title_" .app()->getLocale();
@endphp
<section class="page-bonus-block">

    <div class="stats tabs">
        <div class="stats-content-rank servers-list-tab tab active" id="tab_5">
            <h1>{{ __('Товары магазина') }}</h1>

            <div class="stats-content-rank-description">
                <p class="shopitems-description">{{ __('В инвентаре вы можете активировать купленную привилегию. Что бы получить купленные предметы, перейдите на игровой сервер и пропишите команду') }}: <span style="font-weight: bold">/store</span></p>
            </div>

            <div class="page-bonus-prizes">

                @foreach($inventory_shopitems as $inventory_shopitem)
                    @php $item = getshopitem($inventory_shopitem->shop_item_id);
                        list($price, $price_usd, $name) = get_coupon_price(5, $inventory_shopitem->shop_item_id, $inventory_shopitem->variation_id);
                    @endphp
                    @if (!$item) @continue @endif

                    <div class="page-bonus-prize type-0">
                        <div class="quality-type"></div>
                        <img src="@if(isset($item->image)){{ getImageUrl($item->image) }}@endif" alt="">
                        <span>{{ $name }}</span>
                        <span class="bonus-price">
                            @if(app()->getLocale() == 'ru'){{ $price }} {{ __('₽') }}@else${{ $price_usd }}@endif
                        </span>

                        @if(isset($item->is_item) && $item->is_item == 1)
                            <div class="action-btns activate-btns">
                                <form action="{{ route('account.inventory.sendShopItem') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="inventory_id" value="{{ $inventory_shopitem->id }}">
                                    <select name="server_id">
                                        @foreach(getservers() as $server)
                                            @if($item->server !== 0 && $server->id != $item->server) @continue @endif
                                            <option value="{{ $server->id }}">{{ $server->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-activate">
                                        <img src="/images/icons/activate.png" alt="activate btn">
                                        <i>{{ __('Отправить') }}</i>
                                    </button>
                                </form>
                            </div>

                        @else

                        <div class="action-btns activate-btns">
                            <form action="{{ route('account.inventory.activateShopItem') }}" method="POST">
                                @csrf
                                <input type="hidden" name="inventory_id" value="{{ $inventory_shopitem->id }}">
                                <select name="server_id">
                                    @foreach(getservers() as $server)
                                        @if($item->server !== 0 && $server->id != $item->server) @continue @endif
                                        <option value="{{ $server->id }}">{{ $server->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-activate">
                                    <img src="/images/icons/activate.png" alt="activate btn">
                                    <i>{{ __('Активировать') }}</i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                @endforeach


            </div>
        </div>

    </div>


</section>