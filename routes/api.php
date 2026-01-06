<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('server.config')->group(function () {
	Route::any('payments/notification/freekassa', [\App\Http\Controllers\Api\PaymentsController::class, 'freekassa']);
	Route::any('payments/notification/app', [\App\Http\Controllers\Api\PaymentsController::class, 'appCent']);
	Route::any('payments/notification/qiwi', [\App\Http\Controllers\Api\PaymentsController::class, 'qiwi']);
	Route::any('payments/notification/enot', [\App\Http\Controllers\Api\PaymentsController::class, 'enot']);
	Route::any('payments/notification/primepayments', [\App\Http\Controllers\Api\PaymentsController::class, 'primepayments']);
	Route::any('payments/notification/pagseguro', [\App\Http\Controllers\Api\PaymentsController::class, 'pagseguro']);
	Route::any('payments/notification/paymentwall', [\App\Http\Controllers\Api\PaymentsController::class, 'paymentwall']);
    Route::any('payments/notification/unitpay', [\App\Http\Controllers\Api\PaymentsController::class, 'unitpay']);
    Route::any('payments/notification/paypal', [\App\Http\Controllers\Api\PaymentsController::class, 'paypal']);
    Route::any('pays/notification/tebex', [\App\Http\Controllers\Api\PaymentsController::class, 'tebex']);
    Route::any('payments/notification/yookassa', [\App\Http\Controllers\Api\PaymentsController::class, 'yookassa']);
    Route::any('payments/notification/unitpay', [\App\Http\Controllers\Api\PaymentsController::class, 'unitpay']);
    Route::any('payments/notification/cryptocloud', [\App\Http\Controllers\Api\PaymentsController::class, 'cryptocloud']);
    Route::any('payments/notification/paykeeper', [\App\Http\Controllers\Api\PaymentsController::class, 'paykeeper']);
    Route::any('payments/notification/skinspay', [\App\Http\Controllers\Api\PaymentsController::class, 'skinspay']);
    Route::any('payments/notification/alfabank', [\App\Http\Controllers\Api\PaymentsController::class, 'alfabank']);
    Route::any('payments/notification/pally', [\App\Http\Controllers\Api\PallyController::class, 'paymentWebhook']);
    Route::any('payments/notification/pally/payout', [\App\Http\Controllers\Api\PallyController::class, 'payoutWebhook']);
    Route::any('payments/notification/pally/refund', [\App\Http\Controllers\Api\PallyController::class, 'refundWebhook']);
    Route::any('payments/notification/pally/chargeback', [\App\Http\Controllers\Api\PallyController::class, 'chargebackWebhook']);
    Route::any('payments/notification/heleket', [\App\Http\Controllers\Api\HeleketController::class, 'paymentWebhook']);
    Route::any('payments/notification/heleket/payout', [\App\Http\Controllers\Api\HeleketController::class, 'payoutWebhook']);

    Route::any('/app/{method}', [\App\Http\Controllers\PaymentsController::class, 'appStatus'])->name('app.status');


    Route::get('shop/getUser', [\App\Http\Controllers\Api\ShopController::class, 'getUser']);
    Route::post('shop/deleteItem', [\App\Http\Controllers\Api\ShopController::class, 'deleteItem']);
    Route::post('shop/hasItem', [\App\Http\Controllers\Api\ShopController::class, 'hasItem']);
    Route::get('shop/changeBalace', [\App\Http\Controllers\Api\ShopController::class, 'changeBalace']);
    Route::post('shop/getImageUrls', [\App\Http\Controllers\Api\ShopController::class, 'getImageUrls']);
    Route::get('/v2/shop/getImageUrls', [\App\Http\Controllers\Api\ShopController::class, 'getImageUrlsV2']);
    Route::post('shop/reportService', [\App\Http\Controllers\Api\ShopController::class, 'reportService']);
    Route::post('statistics/setStatistics', [\App\Http\Controllers\Api\ServersStatisticsController::class, 'setStatistics']);
    //Route::post('statistics/test', [\App\Http\Controllers\Api\ServersStatisticsController::class, 'test']);
    Route::post('statistics/processStatistics_jr7wu23g4tv0dr', [\App\Http\Controllers\Api\ServersStatisticsController::class, 'processStatistics']);


    Route::get('statistics/clearStatistics', [\App\Http\Controllers\Api\ClearStatisticsController::class, 'clearStatistics']);

    //Route::post('statistics/test', [\App\Http\Controllers\Api\ServersStatisticsController::class, 'test']);

    Route::post('server/setLastWipeDate', [\App\Http\Controllers\Api\ServersWipeController::class, 'setLastWipeDate']);
    Route::post('server/forgetCacheOnline', [\App\Http\Controllers\Api\ServersWipeController::class, 'forgetCacheOnline']);
    Route::get('server/refreshStatus', [\App\Http\Controllers\Api\ServersWipeController::class, 'refreshStatus']);

    Route::any('server/getPlayersOnline', [\App\Http\Controllers\Api\PlayersOnlineController::class, 'getPlayersOnline']);
    Route::any('server/getPlayersOnlineTest', [\App\Http\Controllers\Api\PlayersOnlineController::class, 'getPlayersOnlineTest']);
    Route::any('server/getPlayersOnlineTest2', [\App\Http\Controllers\Api\PlayersOnlineController::class, 'getPlayersOnlineTest2']);

    Route::any('delivery_requests/buyItemsFromWaxpeer', [\App\Http\Controllers\Api\DeliveryRequestController::class, 'buyItemsFromWaxpeer']);
    Route::any('delivery_requests/checkStatusFromWaxpeer', [\App\Http\Controllers\Api\DeliveryRequestController::class, 'checkStatusFromWaxpeer']);
    Route::any('delivery_requests/buyItemsFromSkinsback', [\App\Http\Controllers\Api\DeliveryRequestController::class, 'buyItemsFromSkinsback']);
    Route::any('delivery_requests/checkStatusFromSkinsback', [\App\Http\Controllers\Api\DeliveryRequestController::class, 'checkStatusFromSkinsback']);
});