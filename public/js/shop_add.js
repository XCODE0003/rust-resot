$('.buy-item-button').on('click', function () {
    $('#item-name').text($(this).attr('data-itemname'));
    $('#item-price').val($(this).attr('data-itemprice'));
    $('#item_id').val($(this).attr('data-itemid'));
    $('#steam_id').val($(this).attr('data-streamid'));
    $('#var_id').val($(this).attr('data-varid'));

    let steam_id = $(this).parent().parent().find('.steam-id').val();
    $('#steam_id').val(steam_id);
});

$('.variation').on('change', function () {
    $(this).parent().parent().parent().parent().find('.buy-item-button').attr('data-itemprice', $(this).parent().find('option:selected').attr('data-varprice'));
    $(this).parent().parent().parent().parent().find('.buy-item-button').attr('data-varid', $(this).parent().find('option:selected').attr('data-varid'));
    $(this).parent().parent().parent().parent().find('.buy-item-price').text($(this).parent().find('option:selected').attr('data-varprice'));
});

$('.variation-shopitem').on('change', function () {
    let price = parseFloat($(this).parent().find('option:selected').attr('data-varprice'));
    $(this).parent().parent().parent().parent().find('.var_id').val($(this).parent().find('option:selected').attr('data-varid'));
    $(this).parent().parent().parent().parent().find('.buy-item-price').text(price);
});


$('.payment-checkbox').on('click', function () {
    $('#payment_id').val($(this).attr('data-paymentid'));
});

$('.shop-item-buy-gift-text').on('click', function () {
    if ($(this).parent().hasClass('active') == true) {
        $(this).parent().parent().find('.buy-button').attr('data-streamid', '');
        $(this).parent().parent().find('.steam-id').val('');
    }
});

$('.steam-id').on('change', function () {
    $(this).parent().parent().parent().find('.buy-button').attr('data-streamid', $(this).val());
});