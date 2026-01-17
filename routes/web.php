<?php

use App\Http\Controllers\Auth\EmailVerifyController;
use App\Models\Session;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DownloadController;

use App\Http\Middleware\Localization;

use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\Auth\BackendLoginController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

//Переключение языков
Route::get('setlocale/{locale}', function ($locale) {

    $referer = Redirect::back()->getTargetUrl();
    $parse_url = parse_url($referer, PHP_URL_PATH);

    $segments = explode('/', $parse_url);

    if (isset($segments[1]) && in_array($segments[1], App\Http\Middleware\Localization::$languages)) {
        unset($segments[1]);
    }

    if ($locale != App\Http\Middleware\Localization::$mainLanguage){
        array_splice($segments, 1, 0, $locale);
    }

    $url = Request::root().implode("/", $segments);

    if(parse_url($referer, PHP_URL_QUERY)){
        $url = $url.'?'. parse_url($referer, PHP_URL_QUERY);
    }

    app()->setlocale($locale);
    session()->put('locale', $locale);

    //Фикс, потому что на главной дописывает в адрес сайта public/ и на http
    if (mb_substr($url, -1) == "/") {
        $url = substr($url,0,-1);
    }


    return redirect($url);

})->name('setlocale');

//Переключение серверов
Route::get('setserver/{server_id}', function ($server_id) {

    $referer = Redirect::back()->getTargetUrl();

    //app()->setlocale($locale);
    session()->put('server_id', $server_id);

    return redirect($referer);

})->name('setserver');

//Переключение темы
Route::get('settheme/{theme}', function ($theme) {

    $url = Redirect::back()->getTargetUrl();
    session()->put('theme', $theme);

    return redirect($url);

})->name('settheme');


Route::group(['prefix' => App\Http\Middleware\Localization::getLocale()], function() {
    Route::middleware('server.config')->group(function () {
        Route::middleware('visit.statistics')->group(function () {

                Route::any('/', function () { return view('pages.main.home'); })->name('index');

                Route::get('/term', function () { return view('pages.main.term'); })->name('term');
                Route::get('/policy', function () { return view('pages.main.policy'); })->name('policy');
                Route::get('/refund', function () { return view('pages.main.refund'); })->name('refund');

                Route::get('/login', [LoginController::class, 'index']);
                Route::get('/login_steam', [LoginController::class, 'login_steam'])->name('login_steam');
                Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
                Route::get('/register', [RegisterController::class, 'index']);

                Route::get('/news', [\App\Http\Controllers\ArticleController::class, 'index'])->name('news');
                Route::get('/news/{path}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('news.show');

                Route::get('/guides', [\App\Http\Controllers\GuideController::class, 'index'])->name('guides');
                Route::get('/guides/{category}/{path}', [\App\Http\Controllers\GuideController::class, 'show'])->name('guides.show');

                Route::get('/servers', [\App\Http\Controllers\ServersController::class, 'index'])->name('servers');
                Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop');
                Route::get('/faq', [\App\Http\Controllers\FaqController::class, 'index'])->name('faq');

            //Route::get('/test/rcon', [\App\Http\Controllers\TestController::class, 'rcon']);
            //Route::get('/test/setonline', [\App\Http\Controllers\TestController::class, 'setonline']);
            //Route::get('/test/searchItemsByName', [\App\Http\Controllers\TestController::class, 'searchItemsByName']);
            //Route::get('/test/testServerSocket', [\App\Http\Controllers\TestController::class, 'testServerSocket']);
            //Route::get('/test/news', [\App\Http\Controllers\ArticleController::class, 'news_test'])->name('news.test');
            //Route::get('/test', [\App\Http\Controllers\IndexController::class, 'test'])->name('test');
            //Route::get('/test/resetonline', [\App\Http\Controllers\TestController::class, 'resetonline']);


            Route::middleware('server.config')->group(function () {
                Route::get('/donation', [\App\Http\Controllers\DonationController::class, 'index'])->name('donation');
                Route::post('/donation', [\App\Http\Controllers\DonationController::class, 'create']);
                Route::post('donation/transfer', [\App\Http\Controllers\DonationController::class, 'transfer_store']);

                Route::get('/store', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop');
                Route::get('/store/{server_id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop.item.show');
                Route::get('/store_test', [\App\Http\Controllers\ShopController::class, 'show_test'])->name('shop.item.test');

                Route::get('/cases/show_shop/{case}', [\App\Http\Controllers\CasesController::class, 'show_shop'])->name('cases.show_shop');
            });
        });
    });

Route::middleware('auth')->group(function () {
    Route::middleware('server.config')->group(function () {
        Route::middleware('visit.statistics')->group(function () {

            Route::get('/stats', [\App\Http\Controllers\StatsController::class, 'index'])->name('stats');
            Route::get('/stats_old', [\App\Http\Controllers\StatsController::class, 'stats_old'])->name('stats.old');
            Route::get('/stats/{player}', [\App\Http\Controllers\StatsController::class, 'account_stats'])->name('account.stats');

            Route::get('/account/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets');
            Route::get('/account/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
            Route::get('/account/tickets/{ticket}/delete', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.delete');
            Route::post('/account/tickets/{ticket}/answer', [\App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
            Route::get('/account/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
            Route::post('/account/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
            Route::post('/account/tickets/searchPlayer', [\App\Http\Controllers\TicketController::class, 'searchPlayer'])->name('tickets.searchPlayer');

            Route::post('/account/store/cart/add', [\App\Http\Controllers\ShopController::class, 'add_cart'])->name('shop.cart.add');
            Route::post('/account/store/cart/delete', [\App\Http\Controllers\ShopController::class, 'delete_cart'])->name('shop.cart.delete');
            Route::post('/account/store/cart/update', [\App\Http\Controllers\ShopController::class, 'update_cart'])->name('shop.cart.update');
            Route::get('/account/store/checkout', [\App\Http\Controllers\ShopController::class, 'checkout'])->name('shop.cart.checkout');
            Route::post('/account/store/coupon/apply', [\App\Http\Controllers\ShopController::class, 'apply_coupon'])->name('shop.coupon.apply');
            Route::post('/account/store/coupon/remove', [\App\Http\Controllers\ShopController::class, 'remove_coupon'])->name('shop.coupon.remove');
            Route::post('/account/store/checkout', [\App\Http\Controllers\ShopController::class, 'complete'])->name('shop.cart.complete');
            Route::get('/account/store/checkout/success', [\App\Http\Controllers\ShopController::class, 'success'])->name('shop.cart.success');

            Route::post('/account/store/buy', [\App\Http\Controllers\ShopController::class, 'buy_item'])->name('shop.item.buy');
            Route::post('/account/store/buy_set', [\App\Http\Controllers\ShopController::class, 'buy_set'])->name('shop.set.buy');

            Route::get('/account/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('account.profile');
            Route::post('/account/profile/setTradeUrl', [\App\Http\Controllers\ProfileController::class, 'setTradeUrl'])->name('account.profile.setTradeUrl');
            Route::post('/account/balance/topup', [\App\Http\Controllers\DonateController::class, 'create'])->name('account.balance.topup');
            Route::post('/account/inventory/sell', [\App\Http\Controllers\InventoryController::class, 'sell'])->name('account.inventory.sell');
            Route::post('/account/inventory/send', [\App\Http\Controllers\InventoryController::class, 'send'])->name('account.inventory.send');
            Route::post('/account/inventory/activate', [\App\Http\Controllers\InventoryController::class, 'activate'])->name('account.inventory.activate');
            Route::post('/account/inventory/apply', [\App\Http\Controllers\InventoryController::class, 'apply'])->name('account.inventory.apply');
            Route::post('/account/inventory/activateShopItem', [\App\Http\Controllers\InventoryController::class, 'activateShopItem'])->name('account.inventory.activateShopItem');
            Route::post('/account/inventory/sendShopItem', [\App\Http\Controllers\InventoryController::class, 'sendShopItem'])->name('account.inventory.sendShopItem');

            Route::get('/account/store-test', [\App\Http\Controllers\ShopController::class, 'test'])->name('shop.test');

            Route::get('/account/balance/tebex', [\App\Http\Controllers\DonateController::class, 'tebex'])->name('account.balance.tebex');

            Route::get('/cabinet', [\App\Http\Controllers\ProfileController::class, 'cabinet'])->name('cabinet');

            Route::get('/promocode', [\App\Http\Controllers\PromoCodeController::class, 'index'])->name('promocode');
            Route::post('/promocode/activate', [\App\Http\Controllers\PromoCodeController::class, 'activate'])->name('promocode.activate');


            Route::get('/bonus', [\App\Http\Controllers\BonusController::class, 'index'])->name('bonus');
            Route::post('/bonus/getBonusItemsForRoll', [\App\Http\Controllers\BonusController::class, 'getBonusItemsForRoll'])->name('bonus.getBonusItemsForRoll');
            Route::post('/bonus/open', [\App\Http\Controllers\BonusController::class, 'open'])->name('bonus.open');

            Route::get('/bonus_monday', [\App\Http\Controllers\BonusController::class, 'indexMonday'])->name('bonus_monday');
            Route::post('/bonus_monday/getBonusItemsForRoll', [\App\Http\Controllers\BonusController::class, 'getBonusItemsForRollMonday'])->name('bonus_monday.getBonusItemsForRoll');
            Route::post('/bonus_monday/open', [\App\Http\Controllers\BonusController::class, 'openMonday'])->name('bonus_monday.open');

            Route::get('/bonus_thursday', [\App\Http\Controllers\BonusController::class, 'indexThursday'])->name('bonus_thursday');
            Route::post('/bonus_thursday/getBonusItemsForRoll', [\App\Http\Controllers\BonusController::class, 'getBonusItemsForRollThursday'])->name('bonus_thursday.getBonusItemsForRoll');
            Route::post('/bonus_thursday/open', [\App\Http\Controllers\BonusController::class, 'openThursday'])->name('bonus_thursday.open');

            Route::get('/cases', [\App\Http\Controllers\CasesController::class, 'index'])->name('cases');
            Route::get('/cases/show/{case}', [\App\Http\Controllers\CasesController::class, 'show'])->name('cases.show');

            Route::post('/cases/open', [\App\Http\Controllers\CasesController::class, 'open'])->name('cases.open');
            Route::post('/cases/open_pay', [\App\Http\Controllers\CasesController::class, 'open_pay'])->name('cases.open_pay');
            Route::post('/cases/getCaseItemsForRoll', [\App\Http\Controllers\CasesController::class, 'getCaseItemsForRoll'])->name('cases.getCaseItemsForRoll');

            Route::get('/account/cases/show/{inventory}', [\App\Http\Controllers\CasesController::class, 'account_show'])->name('account.cases.show');
            Route::post('/account/cases/open', [\App\Http\Controllers\CasesController::class, 'account_open'])->name('account.cases.open');

            Route::get('/account/delivery_requests', [\App\Http\Controllers\DeliveryRequestsController::class, 'list'])->name('account.delivery_requests');
            Route::get('/account/delivery_requests/cancel/{deliveryrequest}', [\App\Http\Controllers\DeliveryRequestsController::class, 'cancel'])->name('delivery_requests.cancel');

            Route::get('/email/verify', [EmailVerifyController::class, 'notice'])->name('verification.notice');
            Route::post('/email/notify', [EmailVerifyController::class, 'resend'])->middleware('throttle:6,1')->name('verification.resend');
        });

    });
});


    //Панель управления
    Route::get('/backend_uc7BgHFmw32FDIEp/login', [BackendLoginController::class, 'index']);
    Route::get('/backend_uc7BgHFmw32FDIEp/logout', [BackendLoginController::class, 'logout'])->name('backend.logout');

    Route::post('/backend_uc7BgHFmw32FDIEp/login', [BackendLoginController::class, 'authenticate'])->name('backend.login');

    Route::middleware('server.config')->group(function () {
    Route::middleware('backend')->group(function () {

        Route::prefix('backend_uc7BgHFmw32FDIEp')->group(function () {
            Route::get('/', [BackendController::class, 'index'])->name('backend');

            Route::get('/updateitems', [\App\Http\Controllers\Backend\UpdateitemsController::class, 'index']);

            Route::get('/settings/security', [\App\Http\Controllers\Backend\UserSettingsController::class, 'security'])->name('backend.settings.security');
            Route::patch('/settings/security', [\App\Http\Controllers\Backend\UserSettingsController::class, 'security_store']);

            Route::get('/settings/profile', [\App\Http\Controllers\Backend\UserSettingsController::class, 'profile'])->name('backend.settings.profile');
            Route::patch('/settings/profile', [\App\Http\Controllers\Backend\UserSettingsController::class, 'profile_store']);

            Route::get('/settings/activity', [\App\Http\Controllers\Backend\UserSettingsController::class, 'activity'])->name('backend.settings.activity');
            Route::get('/settings/activity/{id}', [\App\Http\Controllers\Backend\UserSettingsController::class, 'activity_destroy'])->name('backend.settings.activity.destroy');

            Route::get('/settings', [\App\Http\Controllers\Backend\SettingsController::class, 'index'])->name('backend.settings');
            Route::get('/settings/site', [\App\Http\Controllers\Backend\SettingsController::class, 'site'])->name('settings.site');
            Route::get('/settings/project_name', [\App\Http\Controllers\Backend\SettingsController::class, 'project_name'])->name('settings.project_name');
            Route::get('/settings/robots', [\App\Http\Controllers\Backend\SettingsController::class, 'robots'])->name('settings.robots');
            Route::get('/settings/sitemap', [\App\Http\Controllers\Backend\SettingsController::class, 'sitemap'])->name('settings.sitemap');
            Route::get('/settings/langs', [\App\Http\Controllers\Backend\SettingsController::class, 'langs'])->name('settings.langs');
            Route::get('/settings/analitics', [\App\Http\Controllers\Backend\SettingsController::class, 'analitics'])->name('settings.analitics');
            Route::get('/settings/about', [\App\Http\Controllers\Backend\SettingsController::class, 'about'])->name('settings.about');
            Route::get('/settings/download', [\App\Http\Controllers\Backend\SettingsController::class, 'download'])->name('settings.download');
            Route::get('/settings/login', [\App\Http\Controllers\Backend\SettingsController::class, 'login'])->name('settings.login');
            Route::get('/settings/login_steam', [\App\Http\Controllers\Backend\SettingsController::class, 'login_steam'])->name('settings.login_steam');
            Route::get('/settings/policy', [\App\Http\Controllers\Backend\SettingsController::class, 'policy'])->name('settings.policy');
            Route::get('/settings/forum', [\App\Http\Controllers\Backend\SettingsController::class, 'forum'])->name('settings.forum');
            Route::get('/settings/social', [\App\Http\Controllers\Backend\SettingsController::class, 'social'])->name('settings.social');
            Route::get('/settings/donat', [\App\Http\Controllers\Backend\SettingsController::class, 'donat'])->name('settings.donat');
            Route::get('/settings/services', [\App\Http\Controllers\Backend\SettingsController::class, 'services'])->name('settings.services');
            Route::get('/settings/smtp', [\App\Http\Controllers\Backend\SettingsController::class, 'smtp'])->name('settings.smtp');
            Route::get('/settings/recaptcha', [\App\Http\Controllers\Backend\SettingsController::class, 'recaptcha'])->name('settings.recaptcha');
            Route::get('/settings/sms', [\App\Http\Controllers\Backend\SettingsController::class, 'sms'])->name('settings.sms');
            Route::get('/settings/payments', [\App\Http\Controllers\Backend\SettingsController::class, 'payments'])->name('settings.payments');
            Route::get('/settings/streams', [\App\Http\Controllers\Backend\SettingsController::class, 'streams'])->name('settings.streams');
            Route::get('/settings/game_api', [\App\Http\Controllers\Backend\SettingsController::class, 'game_api'])->name('settings.game_api');
            Route::get('/settings/waxpeer_api', [\App\Http\Controllers\Backend\SettingsController::class, 'waxpeer_api'])->name('settings.waxpeer_api');
            Route::get('/settings/skinsback_api', [\App\Http\Controllers\Backend\SettingsController::class, 'skinsback_api'])->name('settings.skinsback_api');
            Route::get('/settings/falling_snow', [\App\Http\Controllers\Backend\SettingsController::class, 'falling_snow'])->name('settings.falling_snow');
            Route::get('/settings/bonuses', [\App\Http\Controllers\Backend\SettingsController::class, 'bonuses'])->name('settings.bonuses');
            Route::get('/settings/bonuses_monday', [\App\Http\Controllers\Backend\SettingsController::class, 'bonuses_monday'])->name('settings.bonuses_monday');
            Route::get('/settings/bonuses_thursday', [\App\Http\Controllers\Backend\SettingsController::class, 'bonuses_thursday'])->name('settings.bonuses_thursday');
            Route::post('/settings', [\App\Http\Controllers\Backend\SettingsController::class, 'store']);

            Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'support'])->name('tickets.all');
            Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'backend_show'])->name('backend.tickets.show');
            Route::post('/tickets/{ticket}/answer', [\App\Http\Controllers\TicketController::class, 'backend_update'])->name('backend.tickets.update');
            Route::post('/tickets/{ticket}/isread', [\App\Http\Controllers\TicketController::class, 'backend_isread'])->name('backend.tickets.isread');
            Route::post('/tickets/{ticket}/close', [\App\Http\Controllers\TicketController::class, 'backend_close'])->name('backend.tickets.close');
            Route::post('/tickets/{ticket}/update_reply', [\App\Http\Controllers\TicketController::class, 'backend_update_reply'])->name('backend.tickets.reply.update');
            Route::post('/tickets/{ticket}/update_question', [\App\Http\Controllers\TicketController::class, 'backend_update_question'])->name('backend.tickets.question.update');

            Route::get('/cases/{case}/duplicate', [\App\Http\Controllers\Backend\CasesController::class, 'duplicate'])->name('cases.duplicate');

            Route::get('/logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.all');
            Route::get('/logs/payments', [\App\Http\Controllers\Backend\LogController::class, 'payments'])->name('logs.payments');
            Route::get('/logs/shop', [\App\Http\Controllers\Backend\LogController::class, 'shop'])->name('logs.shop');
            Route::get('/logs/visits', [\App\Http\Controllers\Backend\LogController::class, 'visits'])->name('logs.visits');
            Route::get('/logs/registrations', [\App\Http\Controllers\Backend\LogController::class, 'registrations'])->name('logs.registrations');
            Route::get('/logs/gamecurrencylogs', [\App\Http\Controllers\Backend\LogController::class, 'gamecurrencylogs'])->name('logs.gamecurrencylogs');
            Route::get('/logs/adminlogs', [\App\Http\Controllers\Backend\LogController::class, 'adminlogs'])->name('logs.adminlogs');
            Route::get('/logs/servererrors', [\App\Http\Controllers\Backend\LogController::class, 'servererrors'])->name('logs.servererrors');
            Route::get('/logs/statistics_game_items', [\App\Http\Controllers\Backend\LogController::class, 'statistics_game_items'])->name('logs.statistics_game_items');
            Route::get('/logs/userlogs/{user}', [\App\Http\Controllers\Backend\LogController::class, 'userlogs'])->name('logs.userlogs');

            Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
            Route::get('/users/admin/{user}', [\App\Http\Controllers\UserController::class, 'admin'])->name('user.role.admin');
            Route::get('/users/support/{user}', [\App\Http\Controllers\UserController::class, 'support'])->name('user.role.support');
            Route::get('/users/user/{user}', [\App\Http\Controllers\UserController::class, 'user'])->name('user.role.user');
            Route::get('/users/investor/{user}', [\App\Http\Controllers\UserController::class, 'investor'])->name('user.role.investor');
            Route::get('/users/details/{user}', [\App\Http\Controllers\UserController::class, 'details'])->name('backend.user.details');
            Route::post('/users/setbalance', [\App\Http\Controllers\UserController::class, 'setBalance'])->name('user.balance.set');
            Route::post('/users/mute', [\App\Http\Controllers\UserController::class, 'mute'])->name('user.mute');
            Route::get('/users/unmute/{user}', [\App\Http\Controllers\UserController::class, 'unmute'])->name('user.unmute');
            Route::post('/users/getUserByName', [\App\Http\Controllers\UserController::class, 'getUserByName'])->name('backend.users.getuserbyname');

            Route::get('/shop/cases', [\App\Http\Controllers\Backend\CasesController::class, 'shop_list'])->name('cases.shop_list');

            Route::get('/shopitems/{shopitem}/duplicate', [\App\Http\Controllers\Backend\ShopItemController::class, 'duplicate'])->name('shopitems.duplicate');
            Route::post('/shopitems/getVariations', [\App\Http\Controllers\Backend\ShopItemController::class, 'getVariations'])->name('shopitems.getVariations');
            Route::get('/shopitems/resetCache', [\App\Http\Controllers\Backend\ShopItemController::class, 'resetCache'])->name('shopitems.resetCache');

            Route::get('/bonuses', [\App\Http\Controllers\Backend\BonusesController::class, 'index'])->name('bonuses');
            Route::get('/bonuses/{wonitem}/issued', [\App\Http\Controllers\Backend\BonusesController::class, 'issued'])->name('bonuses.issued');

            Route::get('/promocodes/generate', [\App\Http\Controllers\Backend\PromoCodeController::class, 'generate'])->name('promocodes.generate');
            Route::post('/promocodes/generate', [\App\Http\Controllers\Backend\PromoCodeController::class, 'generate_store'])->name('promocodes.generate_store');

            Route::get('/caseopen_history', [\App\Http\Controllers\Backend\CaseopenHistoryController::class, 'index'])->name('backend.caseopen_history');

            Route::get('/delivery_requests', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'index'])->name('backend.delivery_requests');
            Route::post('/delivery_requests/set_pricecap/{deliveryrequest}', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'setPricecap'])->name('delivery_requests.pricecap.set');
            Route::get('/delivery_requests/inprocessing/{deliveryrequest}', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'setStatusInprocessing'])->name('delivery_requests.status.set.inprocessing');
            Route::get('/delivery_requests/completed/{deliveryrequest}', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'setStatusCompleted'])->name('delivery_requests.status.set.completed');
            Route::get('/delivery_requests/canceled/{deliveryrequest}', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'setStatusCanceled'])->name('delivery_requests.status.set.canceled');
            Route::get('/delivery_requests/waxpeer_api/{deliveryrequest}', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'setStatusWaxpeerAPI'])->name('delivery_requests.status.set.waxpeer_api');
            Route::get('/delivery_requests/skinsback_api/{deliveryrequest}', [\App\Http\Controllers\Backend\DeliveryRequestsController::class, 'setStatusSkinsbackAPI'])->name('delivery_requests.status.set.skinsback_api');

            Route::resource('articles', \App\Http\Controllers\Backend\ArticleController::class)->except('show');
            Route::resource('faqs', \App\Http\Controllers\Backend\FaqController::class)->except('show');
            Route::resource('servers', \App\Http\Controllers\Backend\ServerController::class)->except('show');
            Route::resource('servercategories', \App\Http\Controllers\Backend\ServerCategoryController::class)->except('show');
            Route::resource('banners', \App\Http\Controllers\Backend\BannerController::class)->except('show');
            Route::resource('shopitems', \App\Http\Controllers\Backend\ShopItemController::class)->except('show');
            Route::resource('shopsets', \App\Http\Controllers\Backend\ShopSetController::class)->except('show');
            Route::resource('shopcoupons', \App\Http\Controllers\Backend\ShopCouponController::class)->except('show');
            Route::resource('shopcategories', \App\Http\Controllers\Backend\ShopCategoryController::class)->except('show');
            Route::resource('guides', \App\Http\Controllers\Backend\GuideController::class)->except('show');
            Route::resource('guidecategories', \App\Http\Controllers\Backend\GuideCategoryController::class)->except('show');
            Route::resource('promocodes', \App\Http\Controllers\Backend\PromoCodeController::class);
            Route::resource('cases', \App\Http\Controllers\Backend\CasesController::class)->except('show');
            Route::resource('casesitems', \App\Http\Controllers\Backend\CasesItemController::class)->except('show');

            Route::get('/servers/{id}/settings', [\App\Http\Controllers\Backend\ServerController::class, 'settings'])->name('server.settings');
        });

    });
});

Route::get('/email/verify/{token}', [EmailVerifyController::class, 'verify'])->name('verification.verify');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
Route::post('/login_steam', [LoginController::class, 'authenticateSteam'])->name('authenticateSteam');
//Route::post('/register', [RegisterController::class, 'register'])->name('register');

Auth::routes(['register' => false, 'login' => false, 'logout' => false, 'verify' => false]);

});

//Роуты без языкового флага
Route::get('/server/offline', function () {
    if (server_status(session('server_id')) === 'Online') {
        return redirect()->route('cabinet');
    }
    return view('errors.server-offline');
})->name('server.offline');

Route::post('/register/sendcode', [RegisterController::class, 'sendcode'])->name('register.sendcode');
Route::post('/password/sms', [ResetPasswordController::class, 'sms'])->name('password.sms');
Route::get('/download', [DownloadController::class, 'registrationData'])->name('download.registrationData');

Route::get('/ref/{ref}', [\App\Http\Controllers\ReferralController::class, 'setRefSession'])->name('referrals.set');

Route::middleware('server.config')->group(function () {
    Route::get('/paymentwall/status', [\App\Http\Controllers\PaymentsController::class, 'paymentwallStatus'])->name('paymentwall.status');
    Route::get('/qiwi/{method}', [\App\Http\Controllers\PaymentsController::class, 'qiwiStatus'])->name('qiwi.status');
    Route::get('/enot/{method}', [\App\Http\Controllers\PaymentsController::class, 'enotStatus'])->name('enot.status');
    Route::get('/freekassa/{method}', [\App\Http\Controllers\PaymentsController::class, 'freekassaStatus'])->name('freekassa.status');
    Route::get('/yookassa/{method}', [\App\Http\Controllers\PaymentsController::class, 'yookassaStatus'])->name('yookassa.status');
    Route::get('/unitpay/{method}', [\App\Http\Controllers\PaymentsController::class, 'unitpayStatus'])->name('unitpay.status');
    Route::get('/cryptocloud/{method}', [\App\Http\Controllers\PaymentsController::class, 'cryptocloudStatus'])->name('cryptocloud.status');
    Route::get('/paykeeper/{method}', [\App\Http\Controllers\PaymentsController::class, 'paykeeperStatus'])->name('paykeeper.status');
    Route::get('/alfabank/{method}', [\App\Http\Controllers\PaymentsController::class, 'alfabankStatus'])->name('alfabank.status');
    Route::get('/tebex/{method}', [\App\Http\Controllers\PaymentsController::class, 'tebexStatus'])->name('tebex.status');
    Route::get('/heleket/success', [\App\Http\Controllers\Api\HeleketController::class, 'success'])->name('heleket.success');
    Route::get('/heleket/fail', [\App\Http\Controllers\Api\HeleketController::class, 'fail'])->name('heleket.fail');
    Route::match(['get', 'post'], '/pally/success', [\App\Http\Controllers\Api\PallyController::class, 'success'])->name('pally.success');
    Route::match(['get', 'post'], '/pally/fail', [\App\Http\Controllers\Api\PallyController::class, 'fail'])->name('pally.fail');
});
Route::get('/test/leaderboard', function () {
    return view('pages.main.stats-test');
});
// Route::get('/test/login', function () {
//     $user = User::where('id', 521)->first();
//     Auth::login($user);
//     return redirect('/');

// });
