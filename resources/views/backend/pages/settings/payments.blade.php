@extends('backend.layouts.backend')

@section('title', 'Панель управления - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки платежных систем') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">

                    <div class="tab-pane" id="shop">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-4">

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Enot.io</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="enot_merchant_id">{{ __('Merchant ID') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="enot_merchant_id"
                                                                   name="setting_enot_merchant_id"
                                                                   value="{{ config('options.enot_merchant_id', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="enot_secret_word">{{ __('Secret Word') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="enot_secret_word"
                                                                   name="setting_enot_secret_word"
                                                                   value="{{ config('options.enot_secret_word', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="enot_secret_word_2">{{ __('Secret Word') }} 2</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="enot_secret_word_2"
                                                                   name="setting_enot_secret_word_2"
                                                                   value="{{ config('options.enot_secret_word_2', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Cent.app</div>
                                            <div class="payments-options" style="display: none;">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="setting_cent_authorization">{{ __('Authorization key') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="setting_cent_authorization"
                                                               name="setting_cent_authorization"
                                                               value="{{ config('options.cent_authorization', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="setting_cent_shop_id">{{ __('Shop ID') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="setting_cent_shop_id"
                                                               name="setting_cent_shop_id"
                                                               value="{{ config('options.cent_shop_id', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Qiwi</div>
                                            <div class="payments-options" style="display: none;">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="qiwi_secret_key">{{ __('SECRET KEY') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="qiwi_secret_key"
                                                               name="setting_qiwi_secret_key"
                                                               value="{{ config('options.qiwi_secret_key', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="qiwi_account">{{ __('ACCOUNT') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="qiwi_account"
                                                               name="setting_qiwi_account"
                                                               value="{{ config('options.qiwi_account', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Paymentwall</div>
                                            <div class="payments-options" style="display: none;">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="paymentwall_public_key">{{ __('PUBLIC KEY') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="paymentwall_public_key"
                                                               name="setting_paymentwall_public_key"
                                                               value="{{ config('options.paymentwall_public_key', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="paymentwall_private_key">{{ __('PRIVATE KEY') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="paymentwall_private_key"
                                                               name="setting_paymentwall_private_key"
                                                               value="{{ config('options.paymentwall_private_key', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Free-Kassa</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="freekassa_merchant_id">{{ __('Merchant ID') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="freekassa_merchant_id"
                                                                   name="setting_freekassa_merchant_id"
                                                                   value="{{ config('options.freekassa_merchant_id', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="freekassa_secret_word">{{ __('Secret Word') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="freekassa_secret_word"
                                                                   name="setting_freekassa_secret_word"
                                                                   value="{{ config('options.freekassa_secret_word', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="freekassa_secret_word_2">{{ __('Secret Word') }} 2</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="freekassa_secret_word_2"
                                                                   name="setting_freekassa_secret_word_2"
                                                                   value="{{ config('options.freekassa_secret_word_2', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">PayPal</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="paypal_email">{{ __('Email') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="paypal_email"
                                                                   name="setting_paypal_email"
                                                                   value="{{ config('options.paypal_email', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Yookassa.ru</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="yookassa_merchant_id">{{ __('Merchant ID') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="yookassa_merchant_id"
                                                                   name="setting_yookassa_merchant_id"
                                                                   value="{{ config('options.yookassa_merchant_id', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="yookassa_key">{{ __('Secret Key') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="yookassa_key"
                                                                   name="setting_yookassa_key"
                                                                   value="{{ config('options.yookassa_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">UnitPay</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="unitpay_project_id">{{ __('ID проекта') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="unitpay_project_id" name="setting_unitpay_project_id" value="{{ config('options.unitpay_project_id', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="unitpay_public_key">{{ __('Public key') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="unitpay_public_key" name="setting_unitpay_public_key" value="{{ config('options.unitpay_public_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="unitpay_secret_key">{{ __('Secret key') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="unitpay_secret_key" name="setting_unitpay_secret_key" value="{{ config('options.unitpay_secret_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Cryptocloud.plus</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="cryptocloud_shop_id">{{ __('ID Магазина') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="cryptocloud_shop_id" name="setting_cryptocloud_shop_id" value="{{ config('options.cryptocloud_shop_id', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="cryptocloud_api_key">{{ __('API KEY') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="cryptocloud_api_key" name="setting_cryptocloud_api_key" value="{{ config('options.cryptocloud_api_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="cryptocloud_secret_key">{{ __('SECRET KEY') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="cryptocloud_secret_key" name="setting_cryptocloud_secret_key" value="{{ config('options.cryptocloud_secret_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">PayKeeper</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="paykeeper_user">{{ __('User') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="paykeeper_user" name="setting_paykeeper_user" value="{{ config('options.paykeeper_user', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="paykeeper_password">{{ __('Password') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="paykeeper_password" name="setting_paykeeper_password" value="{{ config('options.paykeeper_password', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="paykeeper_secret">{{ __('Секретное слово') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="paykeeper_secret" name="setting_paykeeper_secret" value="{{ config('options.paykeeper_secret', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="paykeeper_server_url">{{ __('Адрес сервера') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="paykeeper_server_url" name="setting_paykeeper_server_url" value="{{ config('options.paykeeper_server_url', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">AlfaBank</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="alfabank_user">{{ __('User') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="alfabank_user" name="setting_alfabank_user" value="{{ config('options.alfabank_user', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="alfabank_password">{{ __('Password') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="alfabank_password" name="setting_alfabank_password" value="{{ config('options.alfabank_password', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="alfabank_open_key">{{ __('Открытый ключ') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="alfabank_open_key" name="setting_alfabank_open_key" value="{{ config('options.alfabank_open_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="alfabank_callback_token">{{ __('CallBack Токен') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="alfabank_callback_token" name="setting_alfabank_callback_token" value="{{ config('options.alfabank_callback_token', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title py-menu-toggle">Tebex</div>
                                            <div class="payments-options" style="display: none;">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="tebex_project_id">Project ID</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="tebex_project_id" name="setting_tebex_project_id" value="{{ config('options.tebex_project_id', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="tebex_private_key">Private Key</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="tebex_private_key" name="setting_tebex_private_key" value="{{ config('options.tebex_private_key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="tebex_public_token">Public Token</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="tebex_public_token" name="setting_tebex_public_token" value="{{ config('options.tebex_public_token', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="tebex_webhook_Key">Webhook Key</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="tebex_webhook_Key" name="setting_tebex_webhook_Key" value="{{ config('options.tebex_webhook_Key', "") }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-lg btn-primary ml-auto">{{ __('Отправить') }}</button>
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
    <!-- .nk-block -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.payments-title').on('click', function () {
                if ($(this).parent().hasClass('active')) {
                    $(this).parent().removeClass('active');
                    $(this).parent().find('.payments-options').hide();
                } else {
                    $(this).parent().addClass('active');
                    $(this).parent().find('.payments-options').show();
                }
            });
        });
    </script>
@endpush