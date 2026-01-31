@php
    $title = "title_" .app()->getLocale();
    $name = "name_" .app()->getLocale();
@endphp
<section class="page-bonus-block">

    {{-- Секция корзины покупок с возможностью возврата --}}
    @if(isset($cart_items) && count($cart_items) > 0)
    <div class="stats tabs" style="margin-bottom: 30px;" id="cart-section">
        <div class="stats-content-rank servers-list-tab tab active">
            <h1>{{ __('Корзина покупок') }}</h1>

            <div class="stats-content-rank-description">
                <p class="shopitems-description">{{ __('Здесь отображаются товары, ожидающие получения в игре. Вы можете вернуть товар и получить деньги обратно на баланс.') }}</p>
            </div>

            <div class="page-bonus-prizes" id="cart-items-container">
                @foreach($cart_items as $cart_item)
                    @php
                        $shopitem = getshopitem($cart_item->item_id);
                    @endphp
                    @if (!$shopitem) @continue @endif

                    <div class="page-bonus-prize type-0" id="cart-item-{{ $cart_item->id }}">
                        <div class="quality-type"></div>
                        <img src="{{ $shopitem->image_url }}" alt="{{ $shopitem->$name }}">
                        <span style="margin-bottom: 12px;">{{ $shopitem->$name }}</span>
                        <span class="bonus-price" style="color: #4caf50;">
                            {{ number_format($cart_item->price, 0) }} {{ __('₽') }}
                        </span>
                        <span style="font-size: 11px; color: #888; margin-top: 2px;">
                            x{{ $cart_item->quantity }} • {{ $cart_item->server_id }}
                        </span>

                        <div class="action-btns activate-btns">
                            <button type="button" class="btn-activate btn-refund" data-cart-item-id="{{ $cart_item->id }}" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);">
                                <img src="/images/icons/activate.png" alt="refund btn" style="filter: brightness(0) invert(1);">
                                <i>{{ __('Вернуть') }}</i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast функция
        function showToast(type, message, title = '') {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.cssText = 'position:fixed;top:30px;right:30px;z-index:99999;display:flex;flex-direction:column;gap:12px;';
                document.body.appendChild(toastContainer);
            }

            let icon = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ');
            let borderColor = type === 'success' ? '#4caf50' : (type === 'error' ? '#f44336' : '#f77f00');
            let iconColor = borderColor;

            const toast = document.createElement('div');
            toast.className = 'toast toast-' + type;
            toast.style.cssText = 'font-family:Stem,sans-serif;display:flex;align-items:center;min-width:320px;max-width:420px;padding:18px 22px;border-radius:0;background:rgba(30,30,35,0.97);border-top:3px solid ' + borderColor + ';box-shadow:0 4px 20px rgba(0,0,0,0.5);opacity:0;transform:translateY(-20px);transition:all 0.4s ease;';

            toast.innerHTML = `
                <div style="font-size:22px;font-weight:bold;margin-right:16px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:${iconColor};">${icon}</div>
                <div style="flex:1;color:#fff;">
                    ${title ? '<div style="font-family:Rust,Salma Pro,sans-serif;font-weight:600;font-size:13px;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;color:#f77f00;">' + title + '</div>' : ''}
                    <div style="font-size:14px;color:rgba(255,255,255,0.9);line-height:1.4;">${message}</div>
                </div>
                <div class="toast-close" style="font-size:18px;cursor:pointer;opacity:0.5;color:#fff;padding:4px 8px;margin-left:12px;">×</div>
            `;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 10);

            const closeBtn = toast.querySelector('.toast-close');
            const removeToast = () => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 300);
            };

            closeBtn.addEventListener('click', removeToast);
            setTimeout(removeToast, 5000);
        }

        // Обработка кнопок возврата
        document.querySelectorAll('.btn-refund').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                if (this.disabled || this.classList.contains('processing')) {
                    return false;
                }

                if (!confirm('{{ __("Вы уверены, что хотите вернуть этот товар?") }}')) {
                    return false;
                }

                const cartItemId = this.dataset.cartItemId;
                const buttonEl = this;
                const originalHtml = buttonEl.innerHTML;

                // Блокируем кнопку
                buttonEl.disabled = true;
                buttonEl.classList.add('processing');
                buttonEl.innerHTML = '<span class="spinner" style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);border-radius:50%;border-top-color:#fff;animation:spin 0.8s linear infinite;margin-right:8px;"></span><i>{{ __("Возврат...") }}</i>';

                fetch('{{ route("account.inventory.refundCartItem") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ cart_item_id: cartItemId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', '{{ __("На баланс возвращено") }}: ' + data.refund_price + ' ₽', data.item_name);

                        // Удаляем элемент из DOM с анимацией
                        const itemEl = document.getElementById('cart-item-' + cartItemId);
                        if (itemEl) {
                            itemEl.style.transition = 'all 0.3s ease';
                            itemEl.style.opacity = '0';
                            itemEl.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                itemEl.remove();
                                // Если корзина пуста - скрываем секцию
                                const container = document.getElementById('cart-items-container');
                                if (container && container.children.length === 0) {
                                    document.getElementById('cart-section').style.display = 'none';
                                }
                            }, 300);
                        }
                    } else {
                        showToast('error', data.message);
                        // Разблокируем кнопку при ошибке
                        buttonEl.disabled = false;
                        buttonEl.classList.remove('processing');
                        buttonEl.innerHTML = originalHtml;
                    }
                })
                .catch(error => {
                    showToast('error', '{{ __("Произошла ошибка! Попробуйте позже.") }}');
                    buttonEl.disabled = false;
                    buttonEl.classList.remove('processing');
                    buttonEl.innerHTML = originalHtml;
                });

                return false;
            });
        });
    });
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-refund.processing { opacity: 0.7; cursor: not-allowed; }
        .btn-refund:disabled { opacity: 0.7; cursor: not-allowed; }
    </style>
    @endif

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
                            <div class="action-btns activate-btns fade-in-btn">
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

    <style>
        .fade-in-btn {
            opacity: 0;
            animation: fadeInBtn 0.5s ease forwards;
        }

        @keyframes fadeInBtn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>

</section>