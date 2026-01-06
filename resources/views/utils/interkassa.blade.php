<form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8" id="paymentForm">
    <input type="hidden" name="ik_co_id" value="{{ $dataSet['ik_co_id'] }}"/>
    <input type="hidden" name="ik_pm_no" value="{{ $dataSet['ik_pm_no'] }}"/>
    <input type="hidden" name="ik_am" value="{{ $dataSet['ik_am'] }}"/>
    <input type="hidden" name="ik_cur" value="{{ $dataSet['ik_cur'] }}"/>
    <input type="hidden" name="ik_desc" value="{{ $dataSet['ik_desc'] }}"/>
    <input type="hidden" name="ik_sign" value="{{ $sign }}"/>
    <input type="submit" value="Оплатить {{ $dataSet['ik_am'] }} RUB">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('paymentForm').submit();
    });
</script>
