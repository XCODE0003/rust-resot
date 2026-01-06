@extends('layouts.main')

@section('title', __('Пожертвование'))

@section('main')
    <main>

        <link rel="stylesheet" href="{{ asset('assets/css/ion.rangeSlider.css') }}"/>
        <link rel="stylesheet" href="{{ asset('css/css-update.css?ver=1.156') }}"/>


        <style>
            .anim {
                display: none;
            }
        </style>

        <div class="container donation-container">
            <div class="main__container _container inner-content">

                {{-- Alert --}}
                @foreach (['danger', 'warning', 'success', 'info'] as $type)
                    @if(Session::has('alert.' . $type))
                        @foreach(Session::get('alert.' . $type) as $message)
                            <div class="alert alert-fill alert-{{ $type }} alert-dismissible alert-icon">
                                @if ($type === 'danger')
                                    <em class="icon ni ni-cross-circle"></em>
                                @elseif($type === 'success')
                                    <em class="icon ni ni-check-circle"></em>
                                @else
                                    <em class="icon ni ni-alert-circle"></em>
                                @endif
                                {{ $message }}
                                <button class="close" data-dismiss="alert"></button>
                            </div>
                        @endforeach
                    @endif
                @endforeach
                @php
                    session()->forget(['alert.danger', 'alert.warning', 'alert.success', 'alert.info']);
                @endphp
                {{-- End Alert --}}

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-lg-12">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group">
                                        <h5 class="card-title">
                                            <span class="mr-2">@yield('title')</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="card-inner">
									<div class="donate-content">
									
										<div class="donate-content__col" style="width: calc( ( 100% - 20px));">
											<form action="{{ route('donation') }}" method="POST">
												<input type="radio" name="type_id" value="393" data-type="char" data-message="" data-long-name="Coin of Luck" data-price="1.000" data-price_coin="{{ config('options.server_'.$server_id.'_coin_price', '1.000') }}" data-short-name="{{ config('options.server_'.$server_id.'_coin_name', 'CoL') }}" style="display: none;" checked="">
												@csrf
												<input type="hidden" id="amount_bonus" name="amount_bonus" value="0">
												<input type="hidden" id="amount_col" name="amount_col" value="0">
												<input type="hidden" id="amount_val" name="amount_val" value="0">
												<input type="hidden" id="donat_items" name="donat_items" value="">
												
												<div class="form-group">
													<label class="form-label" for="server">{{ __('Игровой сервер') }}</label>
													<div class="input-group">
														<select id="server" name="server" class="form-select form-control @error('server') is-invalid @enderror">
															@foreach(getservers() as $server)
																<option value="{{ $server->id }}" @if($server_id == $server->id) selected @endif>{{ $server->name }}</option>
															@endforeach
														</select>
													</div>
												</div>

												<div class="form-group">
													<label class="form-label" for="payment">{{ __('Способ оплаты') }}</label>
													<div class="input-group">
														<select class="form-select form-control" id="payment" name="payment">
															@if(config('options.enot_merchant_id', '') != '')<option value="1" @if($payment_id == '1') selected @endif>Enot.io ({{ __('Юмани') }}, AdvCash, PerfectMoney, {{ __('Криптовалюты') }})</option> @endif
															@if(config('options.freekassa_merchant_id', '') != '')<option value="2" @if($payment_id == '2') selected @endif>Free-Kassa ({{ __('Мобильные') }}, {{ __('крипто') }}, PerfectMoney, Advcash, {{ __('карты') }})</option> @endif
															@if(config('options.cent_authorization', '') != '')<option value="3" @if($payment_id == '3') selected @endif>Cent.app (VISA, MasterCard, {{ __('МИР') }}, GPay, ApplePay, QIWI)</option> @endif
															@if(config('options.paypal_client_id', '') != '')<option value="4" @if($payment_id == '4') selected @endif>PayPal</option> @endif
															@if(config('options.primepayments_project', '') != '')<option value="5" @if($payment_id == '5') selected @endif>PrimePayments (VISA, MasterCard, {{ __('МИР') }}, GPay, ApplePay, QIWI, {{ __('Юмани') }})</option> @endif
															@if(config('options.qiwi_secret_key', '') != '')<option value="6" @if($payment_id == '6') selected @endif>Qiwi</option> @endif
															@if(config('options.pagseguro_email', '') != '')<option value="7" @if($payment_id == '7') selected @endif>PagSeguro</option> @endif
															@if(config('options.pix_client_id', '') != '')<option value="8" @if($payment_id == '8') selected @endif>Pix</option> @endif
															@if(config('options.paymentwall_public_key', '') != '')<option value="9" @if($payment_id == '9') selected @endif>Paymentwall (MercadoPago, Transferencia, Cards, PIX)</option> @endif
															@if(config('options.interkassa_id', '') != '')<option value="10" @if($payment_id == '10') selected @endif>Interkassa.com</option> @endif
															@if(config('options.paymentwall_public_key', '') != '')<option value="99" @if($payment_id == '99') selected @endif>Payment Test</option> @endif
														</select>
													</div>
												</div>

												@if($payment_id == '8')
												<div class="form-group">
													<label class="form-label" for="cpf">{{ __('Ваш CPF') }}</label>
													<div class="input-group">
														<input type="text" class="form-control form-control-lg" id="cpf" name="cpf" value="" required>
													</div>
													@error('cpf')
													<div class="invalid-feedback">
														{{ $message }}
													</div>
													@enderror
												</div>

												<div class="form-group">
													<label class="form-label" for="cpf_name">{{ __('Ваше полное имя') }}</label>
													<div class="input-group">
														<input type="text" class="form-control form-control-lg" id="cpf_name" name="cpf_name" value="" required>
													</div>
													@error('cpf_name')
													<div class="invalid-feedback">
														{{ $message }}
													</div>
													@enderror
												</div>
												@endif

												@if($payment_id == '9' || $payment_id == '8')
													<div class="form-group">
														<label class="form-label" for="email">{{ __('Email') }}</label>
														<div class="input-group">
															<input type="email" class="form-control form-control-lg" id="email" name="email" value="" required>
														</div>
														@error('email')
														<div class="invalid-feedback">
															{{ $message }}
														</div>
														@enderror
													</div>
												@endif

												<div class="form-group">
													<label class="form-label" for="char_name">{{ __('Имя персонажа') }}</label>
													<div class="input-group">
														<input type="text" class="form-control form-control-lg" id="char_name" name="char_name" value="" required>
													</div>
													@error('char_name')
													<div class="invalid-feedback">
														{{ $message }}
													</div>
													@enderror
												</div>
												<div class="form-group">
													<label class="form-label" for="amount">{{ __('Сумма') }}, {{ config('options.server_'.$server_id.'_coin_name', 'CoL') }}</label>
													<div class="input-group">
														<input type="number" class="form-control" min="@php echo (config('options.server_'.$server_id.'_min_coin_amount', '0') / config('options.server_'.$server_id.'_coin_price', '1.000')); @endphp" max="1000000"
															   id="coin" name="sum"
															   placeholder="Specify quantity Coin of Luck" value="0"
															   onchange="changeSum($(this).val(),false);"
															   onkeyup="changeSum($(this).val(),false);">
														<div class="input-group-append count-bonus"><span class="input-group-text" id="bonus_sum">0</span><span> </span></div>
													</div>
													<div class="input-group input-slider">
														<input type="text" class="" id="sum_slider" data-grid="true" tabindex="-1" readonly="">
													</div>
												</div>

												<div class="form-group payment-sum" style="display: none">
													{{ __('К оплате') }}: <span class="fw-bold" id="sumUsd">0</span>
												</div>

										<div class="donate-content-group" style="width: 100%;">
											<div class="donate-content__col">
												<div class="donate-content__bonuses">

													{{ __('К оплате') }}: <span class="fw-bold" id="sumUsd" style="display: none">0</span>
													<div class="exchange_rates">
														<div class="exchange_rate"><span id="sum_USD">0</span> USD</div>
														<div class="exchange_rate"><span id="sum_BRL">0</span> BRL</div>
														<div class="exchange_rate"><span id="sum_EUR">0</span> EUR</div>
														<div class="exchange_rate"><span id="sum_RUB">0</span> RUB</div>
													</div>

													<div class="form-group">
														<button type="submit" class="btn btn-lg btn-primary">{{ __('Пополнить') }}</button>
													</div>

												</div>
											</div>

											<div class="donate-content__col">

											<div class="donate-content__bonuses"  style="display: none;">
												@php
													$min = 0;
                                                    $max = 10000;
                                                    $index = 0;
												@endphp
												@if (config('options.server_'.$server_id.'_donat_active', '0') != '0' && config('options.server_'.$server_id.'_donat_payments'.$payment_id, '') != '' && config('options.server_'.$server_id.'_donat_date_start', '0') < date('Y-m-d H:i:s') && config('options.server_'.$server_id.'_donat_date_end', '0') > date('Y-m-d H:i:s'))

													<label class="form-label" for="server">{{ __('Бонус к сумме депозита') }}</label>

													@for($i=0;$i<1000;$i++)
														@if (config('options.server_'.$server_id.'_coin_donat_'.$i.'_amount', '') != '')
															@php
																$index++;
                                                                if ($max < config('options.server_'.$server_id.'_coin_donat_'.$i.'_end', 1)) {
                                                                    $max = config('options.server_'.$server_id.'_coin_donat_'.$i.'_end', 1);
                                                                }

															@endphp
															@if ($index == 1) @php $min = config('options.server_'.$server_id.'_coin_donat_'.$i.'_start', 1) @endphp @endif

															<div class="donate-content__bonuses-item">
																<div class="donate-content__bonuses-item-col">
																	<div class="donate-content__bonuses-item-name">{{ __('От суммы') }}</div>
																	<div class="donate-content__bonuses-item-val">{{ config('options.server_'.$server_id.'_coin_donat_'.$i.'_start', 1) }} {{ config('options.server_'.$server_id.'_coin_name', 'CoL') }}</div>
																</div>
																<div class="donate-content__bonuses-item-col">
																	<div class="donate-content__bonuses-item-name">{{ __('Бонус') }}</div>
																	<div class="donate-content__bonuses-item-val">
																		{{ config('options.server_'.$server_id.'_coin_donat_'.$i.'_amount', 1) }}%
																	</div>
																</div>
															</div>

														@endif
													@endfor
												@endif
											</div>

											<div class="donate-content__bonuses">
												@if (config('options.server_'.$server_id.'_donat_active', '0') != '0' && config('options.server_'.$server_id.'_donat_payments'.$payment_id, '') != '' && config('options.server_'.$server_id.'_donat_date_start', '0') < date('Y-m-d H:i:s') && config('options.server_'.$server_id.'_donat_date_end', '0') > date('Y-m-d H:i:s'))

													<label class="form-label" for="server" id="bonus-items-label">{{ __('Бонусный предмет к сумме депозита') }}</label>
													@for($i=0;$i<1000;$i++)
														@if (config('options.server_'.$server_id.'_coin_donat_'.$i.'_amount', '') != '')

															<div id="ditems_{{ $i }}" class="donate-content__bonuses-item ditems-block ditems">
																<div class="donate-content__bonuses-item-row donate-item-row">

																	@php $inp_items = ''; @endphp
																	@for($it=0;$it<1000;$it++)
																		@if (config('options.server_'.$server_id.'_coin_bitem_'.$i.'_'.$it.'_quantity', 0) > 0)
																			@php $item = getitem(config('options.server_'.$server_id.'_coin_bitem_'.$i.'_'.$it.'_id', 0)); @endphp
																			@php $inp_items .= config('options.server_'.$server_id.'_coin_bitem_'.$i.'_'.$it.'_id', 0) . '=' . config('options.server_'.$server_id.'_coin_bitem_'.$i.'_'.$it.'_quantity', 0) . ','; @endphp

																			<div class="donate-content__bonuses-item-val donate-item-val-block">
																				<div class="user-card">
																					<div class="donate-content__bonuses-item-name donate-item-name">
																						<div class="item-name">
																							@isset($item->icon0)
																								@if(strpos($item->icon0, 'images') === FALSE)
																									<img src="{{ asset("images/items/{$item->icon0}.png") }}" alt="bonus item">
																								@else
																									<img src="{{ asset("/storage/{$item->icon0}") }}" alt="bonus item">
																								@endif
																							@else
																								<img src="{{ asset("images/items/Premium_Gift_Box_93039.png") }}" alt="bonus item">
																							@endisset
																							<span class="">{{ config('options.server_'.$server_id.'_coin_bitem_'.$i.'_'.$it.'_name', '') }}</span>
																						</div>
																					</div>
																					<div class="donate-content__bonuses-item-val donate-item-val" style="width: calc( 100% - 150px - 5px);">
																						{{ config('options.server_'.$server_id.'_coin_bitem_'.$i.'_'.$it.'_quantity', 0) }}
																					</div>
																				</div>
																			</div>

																		@endif
																	@endfor
																	<input type="hidden" id="inpitems_{{ $i }}" value="{{ $inp_items }}"/>

																</div>
															</div>

														@endif
													@endfor

												@endif
											</div>


										</div>


									</div>

									</form>

								</div>
							</div>

						</div>

					</div>
				</div>
			</div>
		</div>

		<script>
			$( document ).ready(function(event) {
				$("#sum_slider").ionRangeSlider({
					min: Math.round({{ config('options.server_'.$server_id.'_min_coin_amount', '0') }} / parseFloat($("input[name='type_id']:checked").data('price_coin'))),
					max: {{ $max }},
					from: Math.round({{ config('options.server_'.$server_id.'_min_coin_amount', '0') }} / parseFloat($("input[name='type_id']:checked").data('price_coin'))),
					grid: true,
					postfix: ' {{ config('options.server_'.$server_id.'_coin_name', 'CoL') }}',
					skin: 'round',
					prettify: function (num) {
						let bonus = 0;
						let bonus_item = [];
						let bonus_item_key = [];
						let bonus_item_html = '<div class="row border-bottom text-center pt-5" style="border-bottom-style: dashed !important;"><div class="col-12">Gift items</div></div>';
						let bonus_item_show = false;
						let payment_method = $("input[name='payment_method']:checked").val();
						let price = parseFloat($("input[name='type_id']:checked").data('price'));
						let temp_agrigator_349 = ["all"];
						if (temp_agrigator_349.includes(payment_method) || temp_agrigator_349.includes('all')) {

							//Скрывааем все блоки итемов
							$('.ditems').hide();
							$('#donat_items').val('');
							$('#bonus-items-label').hide();

							@for($i=0;$i<1000;$i++)
									@if (config('options.server_'.$server_id.'_donat_active', '0') != '0' && config('options.server_'.$server_id.'_donat_payments'.$payment_id, '') != '' && config('options.server_'.$server_id.'_donat_date_start', '0') < date('Y-m-d H:i:s') && config('options.server_'.$server_id.'_donat_date_end', '0') > date('Y-m-d H:i:s'))
									@if (config('options.server_'.$server_id.'_coin_donat_'.$i.'_amount', '') != '')

							if (Math.floor(num * price) >= {{ config('options.server_'.$server_id.'_coin_donat_'.$i.'_start', 1) }} && Math.floor(num * price) <= {{ config('options.server_'.$server_id.'_coin_donat_'.$i.'_end', 1) }}) {
								bonus += (Math.floor(num * price) * {{ config('options.server_'.$server_id.'_coin_donat_'.$i.'_amount', 1) }} / 100);
								$('#donat_items').val($('#inpitems_{{ $i }}').val());
								$('.ditems').hide();
								$('#ditems_{{ $i }}').show();
								$('#bonus-items-label').show();
							}

							@endif
							@endif
							@endfor

						}
						$.each(bonus_item_key, function (key, value) {
							if (value !== "undefined") {
								if (typeof bonus_item[key] !== "undefined") {
									if (typeof bonus_item[key][value] !== "undefined") {
										bonus_item_show = true;
										$.each(bonus_item[key][value], function (idx, item) {
											bonus_item_html += '<div class="row border-bottom pt-5"><div class="col-10"><img src="' + item.icon + '" width="15px"> ' + item.name + ' ' + item.add_name + ' ' + (item.enc > 0 ? '<span style="color: #bbb529">+'.item.enc + '</span>' : '') + ' </div><div class="col-2"><span class="pull-right"><span>' + item.count + '</span>x</span></div></div>';
										});
									}
								}
							}
						});
						if (bonus_item_show) {
							$('#calculation_board').removeClass('col-md-12').addClass("col-md-6");
							$('#item_board').html(bonus_item_html);
							$('#item_board').show();
						} else {
							$('#calculation_board').removeClass('col-md-6').addClass("col-md-12");
							$('#item_board').html('');
							$('#item_board').hide();
						}

						/*calculation_board item_board*/

						$('#amount_bonus').val(Math.floor(bonus / price));

						if (bonus > 0) {
							$('#bonus_sum').html('+' + Math.floor(bonus / price) + ' {{ __("Бонусов") }}');
							return num + ", " + "+" + Math.floor(bonus / price) + " ";
						} else {
							$('#bonus_sum').html('+' + 0);
							return num;
						}
					},
					onChange: function (data) {
						$('#coin').val(data.from);
						changeSum(data.from, true);
					}
				});
				var sum_slider = $("#sum_slider").data("ionRangeSlider");
				window.changeSum = function (sum, slider) {
					let price = $("input[name='type_id']:checked").data('price');
					var sum_usd = Math.round(((sum * parseFloat($("input[name='type_id']:checked").data('price_coin')))) * 100) / 100;
					$('.sum').val(sum_usd);
					let oi = 0;
					oi = findFirstNonZeroIndex('1');
					$('#sum_USD').html(roundPlus(Math.ceil(sum_usd * 1 * oi) / oi, 2));
					oi = findFirstNonZeroIndex('{{ config('options.server_'.$server_id.'_exchange_rate_rub', 1) }}');
					$('#sum_RUB').html(roundPlus(Math.ceil(sum_usd / {{ config('options.server_'.$server_id.'_exchange_rate_rub', 1) }} * oi) / oi, 2));
					oi = findFirstNonZeroIndex('{{ config('options.server_'.$server_id.'_exchange_rate_eur', 1) }}');
					$('#sum_EUR').html(roundPlus(Math.ceil(sum_usd / {{ config('options.server_'.$server_id.'_exchange_rate_eur', 1) }} * oi) / oi, 2));
					oi = findFirstNonZeroIndex('{{ config('options.server_'.$server_id.'_exchange_rate_brl', 1) }}');
					$('#sum_BRL').html(roundPlus(Math.ceil(sum_usd / {{ config('options.server_'.$server_id.'_exchange_rate_brl', 1) }} * oi) / oi, 2));
					$('#sum_USD').html(sum_usd);
					if (!slider) {
						sum_slider.update({from: sum});
						$('#sumUsd').text(roundPlus((sum * parseFloat($("input[name='type_id']:checked").data('price_coin'))), 2));
					}

					let price_coin = parseFloat($("input[name='type_id']:checked").data('price_coin'));
					$('#sumUsd').text(roundPlus((sum * price_coin), 2));
					$('#amount_col').val(Number(sum) + Number($('#amount_bonus').val()));
					$('#amount_val').val(roundPlus((sum * price_coin), 2));
				};

				function findFirstNonZeroIndex(num) {
					let numberString = Number(num).toLocaleString('fullwide', {maximumSignificantDigits: 21}).replace('.', '');
					let oi = Array.from(numberString).findIndex(i => i > 0);
					let pos = '10';
					for (var i = 0; i < oi; i++) {
						pos += '0';
					}
					pos = parseInt(pos);
					if (pos < 100) pos = 100;
					return parseInt(pos);
				}

				function initInGame() {
					let input = $("input[name='type_id']:checked");
					let message = input.data('message');
					let long_name = input.data('long-name');
					let short_name = input.data('short-name');
					let type = input.data('type');
					setTextInputInGame(type, message, long_name, short_name);
				}

				function setTextInputInGame(type, message, long_name, short_name) {
					if (type == 'account') {
						$('#recipient-label').html('Specify a game account ID');
						$('.type_icon').html('<i class="fa fa-user"></i>');
						$('#recipient').attr("placeholder", "XX_Login");
					} else {
						$('#recipient-label').html('Specify name of the character');
						$('.type_icon').html('<i class="fa fa-street-view"></i>');
						$('#recipient').attr("placeholder", "MegaMag");
					}
					$('.short_name_icon').html('<i class="fa fa-plus-square"></i>');
					if (short_name.length) {
						$('.short_name_icon').html(short_name);
						let slider = $("#sum_slider").data("ionRangeSlider");
						if (slider) {
							slider.update({postfix: ' ' + short_name,});
						}
					}
					if (long_name.length) $('#coin').attr("placeholder", "Specify quantity " + long_name);
					$('.in-game-message').html('');
					if (message.length) $('.in-game-message').html(message.replace(/\n/g, '<br>'));
				}

				initInGame();
				$('body').on('change', "input[name='type_id']", function (e) {
					let type = $(this).data('type');
					let message = $(this).data('message');
					let long_name = $(this).data('long-name');
					let short_name = $(this).data('short-name');
					setTextInputInGame(type, message, long_name, short_name);
					window.changeSum(document.getElementById("coin").value, true);
				});
				window.changeSum(document.getElementById("coin").value, true);
			});
		</script>

		<script>
			$( document ).ready(function() {
				$('#server').on('change', function () {
					console.log($('#server').val());
					location.href = "{{ route('donation', '') }}?server=" + $('#server').val() + "&payment=" + $('#payment').val()  ;
				})
				$('#payment').on('change', function () {
					console.log($('#payment').val());
					location.href = "{{ route('donation', '') }}?server=" + $('#server').val() + "&payment=" + $('#payment').val();
				})

				//Задаем минимальную сумму коинов
				let min_start = Math.round({{ config('options.server_'.$server_id.'_min_coin_amount', '0') }} / parseFloat($("input[name='type_id']:checked").data('price_coin')));
				$('#coin').val(min_start);
				changeSum(min_start, true);
			});

			function roundPlus(x, n) {
				if(isNaN(x) || isNaN(n)) return false;
				var m = Math.pow(10,n);
				return Math.round(x*m)/m;
			}

		</script>


    </main>
@endsection