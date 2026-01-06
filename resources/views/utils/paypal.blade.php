<form method="post" action="https://www.paypal.com/cgi-bin/webscr" accept-charset="utf-8" id="paymentForm" style="display: none;">
    <input type="hidden" name="cmd" value="_cart" />
    <input type="hidden" name="upload" value="1" />
    <input type="hidden" name="business" value="{{ $dataSet['email'] }}" />
    <input type="hidden" name="item_name_1" value="{{ $dataSet['description'] }}" />
    <input type="hidden" name="amount_1" value="{{ $dataSet['amount'] }}" />
    <input type="hidden" name="currency_code" value="{{ $dataSet['currency'] }}" />
    <input type="hidden" name="no_shipping" value="1" />
    <input type="hidden" name="custom" value="{{ $dataSet['order_id'] }}" />
    <input type="hidden" name="return" value="{{ $dataSet['return_url'] }}" />
        <button type="submit">Оплатить</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('paymentForm').submit();
    });
</script>
