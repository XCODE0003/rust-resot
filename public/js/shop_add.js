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
    // Ищем контейнер - либо форма, либо карточка товара
    let container = $(this).closest('form');
    if (container.length === 0) {
        container = $(this).closest('.shop-item-buy');
    }

    let basePrice = parseFloat($(this).find('option:selected').attr('data-varprice'));
    let discountPercent = parseFloat(container.find('.item-discount-percent').val()) || parseFloat(container.data('discountpercent')) || 0;
    let discountedPrice = discountPercent > 0 ? basePrice * (1 - discountPercent / 100) : basePrice;

    container.find('.buy-item-button').attr('data-itemprice', discountedPrice);
    container.find('.buy-item-button').attr('data-varid', $(this).find('option:selected').attr('data-varid'));
    container.find('.buy-item-price').text(Math.round(discountedPrice));
    container.find('.var_id').val($(this).find('option:selected').attr('data-varid'));

    // Обновляем старую цену если есть скидка
    if (discountPercent > 0) {
        container.find('.buy-item-price-old').text(Math.round(basePrice));
    }

    // Обновляем скрытое поле цены
    container.find('.item-price-value').val(Math.round(discountedPrice));
});

$('.variation-shopitem').on('change', function () {
    let form = $(this).closest('form');
    let basePrice = parseFloat($(this).find('option:selected').attr('data-varprice'));
    let discountPercent = parseFloat(form.find('.item-discount-percent').val()) || parseFloat(form.data('discountpercent')) || 0;
    let discountedPrice = discountPercent > 0 ? basePrice * (1 - discountPercent / 100) : basePrice;

    form.find('.var_id').val($(this).find('option:selected').attr('data-varid'));
    form.find('.buy-item-price').text(Math.round(discountedPrice));

    // Обновляем старую цену если есть скидка
    if (discountPercent > 0) {
        form.find('.buy-item-price-old').text(Math.round(basePrice));
    }

    // Обновляем скрытое поле цены
    form.find('.item-price-value').val(Math.round(discountedPrice));
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