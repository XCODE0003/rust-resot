<form method="get" action="https://enot.io/pay" accept-charset="UTF-8" id="paymentForm" style="display: none;">
    <input type="hidden" name="m" value="{{ $merchant_id }}">
    <input type="hidden" name="cr" value="{{ $cur }}">
    <input type="hidden" name="oa" value="{{ $amount }}">
    <input type="hidden" name="o" value="{{ $payment_id }}">
    <input type="hidden" name="s" value="{{ $sign }}">
    <input type="submit" value="Оплатить {{ $amount }} {{ $cur }}">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('paymentForm').submit();
    });
</script>
