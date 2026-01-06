<?php

use App\Models\Server;
use App\Models\Option;
use App\Models\Referral;
use App\Models\Ticket;
use App\Models\LineageItem;
use App\Models\ShopCoupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Lib\GameServer\GameServerFacade as GameServer;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('getlangs')) {
    function getlangs()
    {
        $langs = [];
        if (config('options.language1') !== NULL && config('options.language1') === 'en') $langs['en'] = __('Английский');
        if (config('options.language2') !== NULL && config('options.language2') === 'ru') $langs['ru'] = __('Русский');
        if (config('options.language3') !== NULL && config('options.language3') === 'de') $langs['de'] = __('Немецкий');
        if (config('options.language4') !== NULL && config('options.language4') === 'fr') $langs['fr'] = __('Французский');
        if (config('options.language5') !== NULL && config('options.language5') === 'it') $langs['it'] = __('Итальянский');
        if (config('options.language6') !== NULL && config('options.language6') === 'es') $langs['es'] = __('Испанский');
        if (config('options.language7') !== NULL && config('options.language7') === 'uk') $langs['uk'] = __('Украинский');

        return $langs;
    }
}

if (!function_exists('getImageUrl')) {
    function getImageUrl($image)
    {
        if ($image === NULL) return '';

        if (str_contains($image, '/storage/')) {
            return $image;
        }

        return '/storage/' . $image;
    }
}

if (!function_exists('articles_count')) {
    function articles_count()
    {
        return \App\Models\Article::count();
    }
}

if (!function_exists('users_count')) {
    function users_count()
    {
        return \App\Models\User::count();
    }
}

if (!function_exists('tickets_count')) {
    function tickets_count()
    {
        return \App\Models\Ticket::where('status', 1)->count();
    }
}

if (!function_exists('faqs_count')) {
    function faqs_count()
    {
        return \App\Models\Faq::count();
    }
}

if (!function_exists('videos_count')) {
    function videos_count()
    {
        return \App\Models\Video::count();
    }
}

if (!function_exists('streams_count')) {
    function streams_count()
    {
        return \App\Models\Stream::count();
    }
}

if (!function_exists('auctions_count')) {
    function auctions_count()
    {
        return \App\Models\Auction::count();
    }
}

if (!function_exists('referrals_count')) {
    function referrals_count()
    {
        return \App\Models\Stream::count();
    }
}

if (!function_exists('banners_count')) {
    function banners_count()
    {
        return \App\Models\Banner::count();
    }
}

if (!function_exists('shopitems_count')) {
    function shopitems_count()
    {
        return \App\Models\ShopItem::count();
    }
}

if (!function_exists('shopcoupons_count')) {
    function shopcoupons_count()
    {
        return \App\Models\ShopCoupon::count();
    }
}

if (!function_exists('guides_count')) {
    function guides_count()
    {
        return \App\Models\Guide::count();
    }
}

if (!function_exists('promocodes_count')) {
    function promocodes_count()
    {
        return \App\Models\PromoCode::count();
    }
}

if (!function_exists('сases_count')) {
    function сases_count()
    {
        return \App\Models\Cases::count();
    }
}

if (!function_exists('servers_count')) {
    function servers_count()
    {
        return \App\Models\Server::count();
    }
}

if (!function_exists('deliveries_count')) {
    function deliveries_count()
    {
        return \App\Models\DeliveryRequest::count();
    }
}

if (!function_exists('free_case_left_time')) {
    function free_case_left_time($case_id)
    {
        $caseopen_history = \App\Models\CaseopenHistory::where('user_id', auth()->id())->where('case_id', $case_id)->latest('date')->first();
        if ($caseopen_history) {
            $next_date = strtotime($caseopen_history->date) + 60*60*24;
            if (strtotime('now') > $next_date) return 0;
            return $next_date - strtotime('now');
        }
        return 0;
    }
}



if (!function_exists('getserversTest')) {
    function getserversTest()
    {
        $servers = [];
        $servers[] = (object)[
            'id' => 99,
            'options' => '{"ip": "46.174.52.218:38045", "api_key": "", "api_url": "", "rcon_ip": "46.174.52.218:38045", "rcon_passw": "kGkMdjwsUc1H", "rsworld_db_type": "1"}',
        ];
        return $servers;
    }
}


if (!function_exists('getservers')) {
    function getservers()
    {
        //Cache::forget('servers');
        return Cache::remember('servers', '600', function () {
            return \App\Models\Server::query()->where('status', 1)->orderBy('sort')->get();
        });
    }
}

if (!function_exists('getserver')) {
    function getserver($id)
    {
        //Cache::forget('servers'.$id);
        return Cache::remember('servers'.$id, '600', function () use ($id) {
            return \App\Models\Server::query()->where('id', $id)->first();
        });

    }
}

if (!function_exists('getplayers')) {
    function getplayers($server_id, $search='')
    {
        if ($search != '') {
            return Cache::remember('players' . $server_id . $search, '3600', function () use ($server_id, $search) {
                return \App\Models\Statistic::where('server', $server_id)->where('general', 1)->where('name', 'LIKE', "%{$search}%")->get();
            });
        } else {

            return Cache::remember('players' . $server_id . $search, '3600', function () use ($server_id) {
                return \App\Models\Statistic::where('server', $server_id)->where('general', 1)->get();
            });

        }
    }
}

if (!function_exists('getguidecategories')) {
    function getguidecategories()
    {
        return Cache::remember('getguidecategories', '3600', function () {
            return \App\Models\GuideCategory::query()->get();
        });
    }
}

if (!function_exists('getguidecategory')) {
    function getguidecategory($id)
    {
        return \App\Models\GuideCategory::find($id);
    }
}

if (!function_exists('get_caseitem_categories')) {
    function get_caseitem_categories()
    {
        return [
            '1' => 'Weapon',
            '2' => 'Door',
            '3' => 'Cloth',
            '4' => 'Tool',
            '5' => 'Furnace',
            '6' => 'Box',
        ];
    }
}

if (!function_exists('get_caseitem_category')) {
    function get_caseitem_category($id)
    {
        return get_caseitem_categories($id);
    }
}

if (!function_exists('getshopcategories')) {
    function getshopcategories()
    {
        //Cache::forget('getshopcategories');
        return Cache::remember('getshopcategories', '300', function () {
            return \App\Models\ShopCategory::query()->orderBy('sort')->get();
        });
    }
}
if (!function_exists('getshopcategory')) {
    function getshopcategory($id)
    {
        return \App\Models\ShopCategory::find($id);
    }
}

if (!function_exists('getservercategories')) {
    function getservercategories()
    {
        return Cache::remember('getservercategories', '300', function () {
            return \App\Models\ServerCategory::query()->where('status', 1)->orderBy('sort')->get();
        });
    }
}

if (!function_exists('getshopitem')) {
    function getshopitem($id)
    {
        return \App\Models\ShopItem::find($id);
    }
}
if (!function_exists('getshopitemvariation')) {
    function getshopitemvariation($id)
    {
        $shopitem = \App\Models\ShopItem::find($id);

        if(isset($shopitem->variations)) {
            $variations = json_decode($shopitem->variations);
        } else {
            $variations = [];
        }

        return $variations;
    }
}

if (!function_exists('getuser')) {
    function getuser($id)
    {
        return \App\Models\User::query()->where('id', $id)->first();
    }
}

if (!function_exists('getuser_by_steamid')) {
    function getuser_by_steamid($steam_id)
    {
        return \App\Models\User::query()->where('steam_id', $steam_id)->first();
    }
}

if (!function_exists('marketitems_count_bycategory')) {
    function marketitems_count_bycategory($id, $server_id)
    {
        return \App\Models\MarketItem::query()->where('category_id', $id)->where('server', $server_id)->count();
    }
}

if (!function_exists('downcounter')) {
    function downcounter($date, $down_time = 720)
    {

        $date_str = strtotime($date) + 60 * $down_time;
        $date = date('Y-m-d H:i:s', $date_str);

        $check_time = strtotime($date) - time();
        if ($check_time <= 0) {
            return '-';
        }

        $days = floor($check_time / 86400);
        $hours = floor(($check_time % 86400) / 3600);
        $minutes = floor(($check_time % 3600) / 60);
        $seconds = $check_time % 60;

        $str = $hours . __('ч') . ' : ' . $minutes . __('м') . ' : ' . $seconds . __('c');

        return $str;
    }
}

if (!function_exists('get_statistics_game_items_name')) {
    function get_statistics_game_items_name($id)
    {
        for ($it = 0; $it < 100; $it++) {
            if (config('options.statistics_game_item_' . $it . '_id', 0) == $id) {
                return config('options.statistics_game_item_' . $it . '_name', '');
            }
        }
        return '-';
    }
}

if (!function_exists('checkItemPurchaseStatus')) {
    function checkItemPurchaseStatus($id)
    {
        $shopitems = \App\Models\ShopItem::where('id', $id)->first();
        $shop_purchases = \App\Models\ShopPurchase::where('item_id', $id)->where('user_id', auth()->user()->id)->where('validity', '>', date('Y-m-d H:i:s'))->first();

        if ($shopitems->purchase_type == '1' && $shop_purchases) {

            if (strtotime($shop_purchases->validity) < strtotime(date('Y-m-d H:i:s'))) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }
}


if (!function_exists('getdelivery_statuses')) {
    function getdelivery_statuses() {
        return [
            '0' => __('Ожидает подтверждения'),
            '1' => __('В обработке'),
            '4' => __('WaxpeerAPI') . '. ' . __('В обработке'),
            '5' => __('WaxpeerAPI') . '. ' . __('Куплена'),
            '6' => __('WaxpeerAPI') . '. ' . __('Выполнена'),
            '7' => __('WaxpeerAPI') . '. ' . __('Отменена'),
            '8' => __('SkinsbackAPI') . '. ' . __('В обработке'),
            '9' => __('SkinsbackAPI') . '. ' . __('Куплена'),
            '10' => __('SkinsbackAPI') . '. ' . __('Выполнена'),
            '11' => __('SkinsbackAPI') . '. ' . __('Отменена'),
            '12' => __('SkinsbackAPI') . '. ' . __('Нет в наличии'),
            '2' => __('Выполнена'),
            '3' => __('Отменена'),
        ];
    }
}
if (!function_exists('getdelivery_status')) {
    function getdelivery_status($id) {
        return getdelivery_statuses()[$id];
    }
}


if (!function_exists('getdelivery_user_statuses')) {
    function getdelivery_user_statuses() {
        return [
            '0' => __('Ожидает подтверждения'),
            '1' => __('В обработке'),
            '4' => __('В обработке'),
            '5' => __('В обработке'),
            '6' => __('Выполнена'),
            '7' => __('Отменена'),
            '8' => __('В обработке'),
            '9' => __('В обработке'),
            '10' => __('Выполнена'),
            '11' => __('Отменена'),
            '12' => __('В обработке'),
            '2' => __('Выполнена'),
            '3' => __('Отменена'),
        ];
    }
}
if (!function_exists('getdelivery_user_status')) {
    function getdelivery_user_status($id) {
        return getdelivery_user_statuses()[$id];
    }
}

if (!function_exists('get_quality_types')) {
    function get_quality_types() {
        return [
            '1' => __('Серый'),
            '2' => __('Синий'),
            '3' => __('Зелёный'),
            '4' => __('Красный'),
        ];
    }
}
if (!function_exists('get_quality_type')) {
    function get_quality_type($id) {
        return get_quality_types()[$id];
    }
}














if (!function_exists('convertToStrList')) {
    function convertToStrList($data)
    {
        $list = '';
        $item_index = 0;
        foreach ($data as $item) {
            $item_index++;
            $list .= '"' . $item . '"';
            if ($item_index < count($data)) {
                $list .= ', ';
            }
        }

        return $list;
    }
}

if (!function_exists('generationCode')) {
    function generationCode()
    {
        //Генерирую случайный код
        $permitted_chars = '0123456789';
        return substr(str_shuffle($permitted_chars), 0, 4);
    }
}

if (!function_exists('generationReferralCode')) {
    function generationReferralCode()
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 20);
    }
}

if (!function_exists('generationPassword')) {
    function generationPassword()
    {
        //Генерирую случайный пароль
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 10);
    }
}

if (!function_exists('random_str')) {
    function random_str($len = 5)
    {
        $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $base = strlen($charset);
        $result = '';

        $now = explode(' ', microtime())[1];
        while ($now >= $base) {
            $i = $now % $base;
            $result = $charset[$i] . $result;
            $now /= $base;
        }
        return substr($result, -$len);
    }
}

if (!function_exists('l2_encrypt_whirlpool')) {
    function l2_encrypt_whirlpool(string $str): string
    {
        $encrypt = base64_encode(hash('whirlpool', $str, TRUE));
        return $encrypt;
    }

}

if (!function_exists('l2_encrypt')) {
    function l2_encrypt(string $str): string
    {
        $key = array();
        $dst = array();
        $i = 0;

        $nBytes = strlen($str);
        while ($i < $nBytes) {
            $i++;
            $key[$i] = ord(substr($str, $i - 1, 1));
            $dst[$i] = $key[$i];
        }

        for ($i = 0; $i < 17; $i++) {
            if (!isset($key[$i])) $key[$i] = NULL;
            if (!isset($dst[$i])) $dst[$i] = NULL;
        }

        $rslt = $key[1] + $key[2] * 256 + $key[3] * 65536 + $key[4] * 16777216;
        $one = $rslt * 213119 + 2529077;
        $one = $one - intval($one / 4294967296) * 4294967296;

        $rslt = $key[5] + $key[6] * 256 + $key[7] * 65536 + $key[8] * 16777216;
        $two = $rslt * 213247 + 2529089;
        $two = $two - intval($two / 4294967296) * 4294967296;

        $rslt = $key[9] + $key[10] * 256 + $key[11] * 65536 + $key[12] * 16777216;
        $three = $rslt * 213203 + 2529589;
        $three = $three - intval($three / 4294967296) * 4294967296;

        $rslt = $key[13] + $key[14] * 256 + $key[15] * 65536 + $key[16] * 16777216;
        $four = $rslt * 213821 + 2529997;
        $four = $four - intval($four / 4294967296) * 4294967296;

        $key[1] = $one & 0xFF;
        $key[2] = ($one >> 8) & 0xFF;
        $key[3] = ($one >> 16) & 0xFF;
        $key[4] = ($one >> 24) & 0xFF;

        $key[5] = $two & 0xFF;
        $key[6] = ($two >> 8) & 0xFF;
        $key[7] = ($two >> 16) & 0xFF;
        $key[8] = ($two >> 24) & 0xFF;

        $key[9] = $three & 0xFF;
        $key[10] = ($three >> 8) & 0xFF;
        $key[11] = ($three >> 16) & 0xFF;
        $key[12] = ($three >> 24) & 0xFF;

        $key[13] = $four & 0xFF;
        $key[14] = ($four >> 8) & 0xFF;
        $key[15] = ($four >> 16) & 0xFF;
        $key[16] = ($four >> 24) & 0xFF;

        $dst[1] = $dst[1] ^ $key[1];

        $i = 1;
        while ($i < 16) {
            $i++;
            $dst[$i] = $dst[$i] ^ $dst[$i - 1] ^ $key[$i];
        }

        $i = 0;
        while ($i < 16) {
            $i++;
            if ($dst[$i] == 0) {
                $dst[$i] = 102;
            }
        }

//        $encrypt = "0x";
        $encrypt = '';
        $i = 0;
        while ($i < 16) {
            $i++;
            if ($dst[$i] < 16) {
                $encrypt = $encrypt . "0" . dechex($dst[$i]);
            } else {
                $encrypt = $encrypt . dechex($dst[$i]);
            }
        }
        return $encrypt;
    }

}



if (!function_exists('server_status')) {
    function server_status($server_id = 1)
    {
        return Cache::get('server' . $server_id . ':status', 0);

        /*
        //Cache::forget('server' . $server_id . ':status');
        return Cache::remember('server' . $server_id . ':status', '60', function () use ($server_id) {

            $status = 'Offline';
            $server = Server::where('id', $server_id)->first();
            $options = json_decode($server->options);

            $ip_port = explode(':', $options->rcon_ip);
            if (!isset($ip_port[1])) return 'Offline';
            $ip = $ip_port[0];
            $port = $ip_port[1];

            $fp = @fsockopen($ip, $port, $errno, $errstr, 1);
            if ($fp) {
                fclose($fp);
                $status = 'Online';
            }
            return $status;
        });
        */
    }
}

if (!function_exists('online_count')) {
    function online_count($server_id='1')
    {
        if (server_status($server_id) === 'Offline') {
            return 0;
        }

        return Cache::get('server' . $server_id . ':online_count', 0);

        /*
                if ($online_count == 0) {
                    Cache::forget('server'.$server_id.':online_count');
                }
                //Cache::forget('server'.$server_id.':online_count');
                return Cache::rememberForever('server' . $server_id . ':online_count', function () use ($server_id) {
                    $online_count = GameServer::online_count($server_id);
                    return $online_count * intval(config('options.server_' . $server_id . '_mul_online', '1'));
                });
        */
    }
}

if (!function_exists('online_max')) {
    function online_max($server_id = '1')
    {
        if (server_status($server_id) === 'Offline') {
            return 0;
        }

        return Cache::get('server' . $server_id . ':online_max', 0);

        /*
                if (Cache::get('server'.$server_id.':online_max') == 0) {
                    Cache::forget('server'.$server_id.':online_max');
                }
                //Cache::forget('server'.$server_id.':online_max');
                return Cache::remember('server' . $server_id . ':online_max', '300', function () use ($server_id) {
                    return GameServer::online_max($server_id);
                });
        */

    }
}

if (!function_exists('online_queued')) {
    function online_queued($server_id = '1')
    {
        if (server_status($server_id) === 'Offline') {
            return 0;
        }

        return Cache::get('server' . $server_id . ':online_queued', 0);

        /*
                if (Cache::get('server'.$server_id.':online_queued') == 0) {
                    Cache::forget('server'.$server_id.':online_queued');
                }
                //Cache::forget('server'.$server_id.':online_queued');
                return Cache::remember('server' . $server_id . ':online_queued', '300', function () use ($server_id) {
                    return GameServer::online_queued($server_id);
                });
        */

    }
}

if (!function_exists('get_wiped_data')) {
    function get_wiped_data($server_id = 0)
    {
        $server = getserver($server_id);
        $wipe_second = wipe_second($server->wipe);
        if ($wipe_second < 60 * 60 * 24) {
            $hours = round($wipe_second / (60 * 60), 0);
            return '<p class="wipe-now">' . __('Вайп сейчас') . ' <span>' . $hours . ' ' . plural_form($hours, [__('час'),__('часа'),__('часов')]) . ' ' . __('назад') . '</span></p>';
        } else {
            $days = round($wipe_second / (60 * 60 * 24), 0);
            if ($days <= 4) {
                return '<p class="wipe-4days">' . __('Вайп') . ' <span>' . $days . ' ' . plural_form($days, [__('день'),__('дня'),__('дней')]) . ' ' . __('назад') . '</span></p>';
            }
            return '<p>' . __('Вайп') . ' <span>' . $days . ' ' . plural_form($days, [__('день'),__('дня'),__('дней')]) . ' ' . __('назад') . '</span></p>';
        }
    }
}

if (!function_exists('getAllOnlineCount')) {
    function getAllOnlineCount()
    {
        $online_count = 0;
        foreach (getservers() as $server) {
            $online_count += online_count($server->id);
        }
        return $online_count;
    }
}

if (!function_exists('format_seconds_from_day')) {
    function format_seconds_from_day($inputSeconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        $days = floor($inputSeconds / $secondsInADay);

        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        $obj = array(
            'ч' => (int)$hours,
            'м' => (int)$minutes,
            'с' => (int)$seconds,
        );

        $str = $hours . __('ч') . ' ';
        $str .= $minutes . __('м') . ' ';
        $str .= $seconds . __('с') . ' ';

        return $str;
    }
}

if (!function_exists('format_seconds')) {
    function format_seconds($inputSeconds)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        $days = floor($inputSeconds / $secondsInADay);

        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        $obj = array(
            'д' => (int)$days,
            'ч' => (int)$hours,
            'м' => (int)$minutes,
            'с' => (int)$seconds,
        );

        $str = '';
        $str .= '<span class="days">' . $days . '</span>' . __('д') . ' ';
        $str .= '<span class="hours">' . $hours . '</span>' . __('ч') . ' ';
        $str .= '<span class="minutes">' . $minutes . '</span>' . __('м') . ' ';
        $str .= '<span class="seconds">' . $seconds . '</span>' . __('с') . ' ';

        return $str;
    }
}

if (!function_exists('plural_form')) {
    function plural_form(int $n, array $forms)
    {
        return is_float($n) ? $forms[1] : ($n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]));
    }
}

if (!function_exists('getmonthname')) {
    function getmonthname($month = 1)
    {
        switch ($month) {
            case 1:
                $month_text = __('Январь');
                break;
            case 2:
                $month_text = __('Февраль');
                break;
            case 3:
                $month_text = __('Март');
                break;
            case 4:
                $month_text = __('Апрель');
                break;
            case 5:
                $month_text = __('Май');
                break;
            case 6:
                $month_text = __('Июнь');
                break;
            case 7:
                $month_text = __('Июль');
                break;
            case 8:
                $month_text = __('Август');
                break;
            case 9:
                $month_text = __('Сентябрь');
                break;
            case 10:
                $month_text = __('Октябрь');
                break;
            case 11:
                $month_text = __('Ноябрь');
                break;
            case 12:
                $month_text = __('Декабрь');
                break;
        }
        return $month_text;
    }
}

if (!function_exists('next_wipe')) {
    function next_wipe($server_id = 0)
    {
        return format_seconds(next_wipe_second($server_id));
    }
}

if (!function_exists('opening_date')) {
    function opening_date($server_id = 0)
    {
        return format_seconds(opening_date_second($server_id));
    }
}

if (!function_exists('next_wipe_second')) {
    function next_wipe_second($server_id = 0)
    {
        $server = getserver($server_id);
        $date = strtotime(date('d-m-Y H:i:s'));
        $wipe = strtotime($server->next_wipe);
        $diff = $wipe - $date;
        if ($diff < 0) {
            return 0;
        }

        return $diff;
    }
}

if (!function_exists('opening_date_second')) {
    function opening_date_second($server_id = 0)
    {
        $date = strtotime(date('d-m-Y H:i:s'));
        $soon = strtotime(config('options.server_' . $server_id . '_opening_date', '0'));
        $diff = $soon - $date;
        if ($diff < 0) {
            return 0;
        }

        return $diff;
    }
}

if (!function_exists('wipe_second')) {
    function wipe_second($wipe)
    {
        $date = strtotime(date('d-m-Y H:i:s'));
        $wipe = strtotime($wipe);
        $diff = $date - $wipe;
        if ($diff < 0) {
            return 0;
        }

        return $diff;
    }
}

if (!function_exists('getRaids')) {
    function getRaids()
    {
        return [
            'шкаф'                                 => __('Шкаф'),
            'МВК потолок'                          => __('МВК потолок'),
            'МВК стена'                            => __('МВК стена'),
            'МВК фундамент'                        => __('МВК фундамент'),
            'гаражная дверь'                       => __('Гаражная дверь'),
            'металлическая дверь'                  => __('Металлическая дверь'),
            'двойная деревянная дверь'             => __('Двойная деревянная дверь'),
            'двойная металлическая дверь'          => __('Двойная металлическая дверь'),
            'Каменный треугольный фундамент'       => __('Каменный треугольный фундамент'),
            'деревянная дверь'                     => __('Деревянная дверь'),
            'Каменный стена'                       => __('Каменная стена'),
            'Каменный треугольный потолок'         => __('Каменный треугольный потолок'),
            'Деревянный фундамент'                 => __('Деревянный фундамент'),
            'Деревянный треугольный фундамент'     => __('Деревянный треугольный фундамент'),
            'Деревянный потолок'                   => __('Деревянный потолок'),
            'Металлический стена'                  => __('Металлическая стена'),
            'стена'                                => __('Стена'),
            'бронированная дверь'                  => __('Бронированная дверь'),
            'двойная бронированная дверь'          => __('Двойная бронированная дверь'),
            'сетчатая дверь'                       => __('Сетчатая дверь'),
            'металлическая витрина'                => __('Металлическая витрина'),
            'тюремная дверь'                       => __('Тюремная дверь'),
            'тюремная решётка'                     => __('Тюремная решётка'),
            'сетчатый забор'                       => __('Сетчатый забор'),
            'деревянная витрина'                   => __('Деревянная витрина'),
            'деревянные решётки'                   => __('Деревянные решётки'),
            'металлические оконные решётки'        => __('Металлические оконные решётки'),
            'металлическая вертикальная бойница'   => __('Металлическая вертикальная бойница'),
            'металлическая горизонтальная бойница' => __('Металлическая горизонтальная бойница'),
            'укреплённые оконные решётки'          => __('Укреплённые оконные решётки'),
            'окно из укреплённого стекла'          => __('Окно из укреплённого стекла'),
            'верстак первого уровня'               => __('Верстак первого уровня'),
            'верстак второго уровня'               => __('Верстак второго уровня'),
            'верстак третьего уровня'              => __('Верстак третьего уровня'),
            'треугольный фундамент'                => __('Треугольный фундамент'),
            'фундамент'                            => __('Фундамент'),
            'лестница'                             => __('Лестница'),
            'крыша'                                => __('Крыша'),
            'лестничный люк'                       => __('Лестничный люк'),
            'потолочный каркас'                    => __('Потолочный каркас'),
            'низкая стена'                         => __('Низкая стена'),
            'полу-стенка'                          => __('Полу-стенка'),
            'настенный каркас'                     => __('Настенный каркас'),
            'солнечная панель'                     => __('Солнечная панель'),
            'автоматическая турель'                => __('Автоматическая турель'),
            'мельница'                             => __('Мельница'),
            'высокие деревянные ворота'            => __('Высокие деревянные ворота'),
            'высокий деревянный забор'             => __('Высокий деревянный забор'),
            'высокий каменный забор'               => __('Высокий каменный забор'),
            'высокие каменные ворота'              => __('Высокие каменные ворота'),
            'настил'                               => __('Настил'),
            'треугольный потолок'                  => __('Треугольный потолок'),
            'потолок'                              => __('Потолок'),
            'металлическая баррикада'              => __('Металлическая баррикада'),
            'деревянный стена'                     => __('Деревянная стена'),
            'деревянные'                           => __('Деревянные'),
            'каменные'                             => __('Каменные'),
            'металлические'                        => __('Металлические'),
            'мвк'                                  => __('МВК'),
            'верстаки'                             => __('Верстаки'),
            'части построек'                       => __('Части построек'),
            'Каменный потолок'                     => __('Каменный потолок'),
            'Металлический потолок'                => __('Металлический потолок'),
            'Металлический полу-стенка'            => __('Металлическяа полу-стенка'),
            'Металлический настенный каркас'       => __('Металлический настенный каркас'),
            'Металлический треугольный потолок'    => __('Металлический треугольный потолок'),
            'Каменный фундамент'                   => __('Каменный фундамент'),
            'Каменный полу-стенка'                 => __('Каменный полу-стенка'),
            'МВК треугольный потолок'              => __('МВК треугольный потолок'),
            'Каменный настенный каркас'            => __('Каменный настенный каркас'),
            'Металлический треугольный фундамент'  => __('Металлический треугольный фундамент'),
            'Деревянный стена'                     => __('Деревянная стена'),
            'Деревянный лестница'                  => __('Деревянная лестница'),
            'Деревянный полу-стенка'               => __('Деревянная полу-стенка'),
            'Деревянный треугольный потолок'       => __('Деревянная треугольный потолок'),
            'Металлический фундамент'              => __('Металлический фундамент'),
            'Деревянный настенный каркас'          => __('Деревянный настенный каркас'),
            'Деревянный крыша'                     => __('Деревянная крыша'),
            'Деревянный низкая стена'              => __('Деревянная низкая стена'),
            'Каменный потолочный каркас'           => __('Каменный потолочный каркас'),
            'Каменный низкая стена'                => __('Каменная низкая стена'),
        ];
    }
}

if (!function_exists('getNameRaid')) {
    function getNameRaid($raid)
    {
        $raids = getRaids();
        return isset($raids[$raid]) ? $raids[$raid] : $raid;
    }
}

if (!function_exists('getResources')) {
    function getResources()
    {
        return [
            'wood'           => __('Дерево'),
            'stones'         => __('Камни'),
            'metal.ore'      => __('Металл. Руда'),
            'sulfur.ore'     => __('Серная Руда'),
            'hq.metal.ore'   => __('Кач. Металл. Руда'),
            'leather'        => __('Кожа'),
            'fat.animal'     => __('Животный Жир'),
            'bone.fragments' => __('Фрагменты Костей'),
            'cloth'          => __('Ткань'),
        ];
    }
}

if (!function_exists('getNameResource')) {
    function getNameResource($resource)
    {
        $resources = getResources();
        return isset($resources[$resource]) ? $resources[$resource] : $resource;
    }
}

if (!function_exists('formatServerIP')) {
    function formatServerIP($ip)
    {
        if (strpos($ip, ':') !== FALSE) {
            $ip_tmp = explode(':', $ip);
            if (isset($ip_tmp[0])) {
                $ip = $ip_tmp[0];
            }
        }
        return $ip;
    }
}

if (!function_exists('getGuideTags')) {
    function getGuideTags($guide_tags)
    {
        if ($guide_tags === NULL || $guide_tags == '' || strpos($guide_tags, ';') === FALSE) {
            return [];
        }

        $tags = explode(';', $guide_tags);
        $i = 0;
        $tags_limit = [];
        foreach ($tags as $tag) {
            $i++;
            if ($i <= 3) {
                $tags_limit[] = $tag;
            }
        }

        return $tags_limit;
    }
}

if (! function_exists('get_random_item')) {
    function get_random_item($items=[])
    {

        /*
        $win_item = [];
        for($i=0;$i<50000;$i++) {
            foreach ($items as $item) {
                $chance = intval($item['context']['chance']) * 100;
                if (rand(1, 10000) < $chance) {
                    $win_item = [
                        'id'     => $item['id'],
                        'name'   => $item['context']['name'],
                        'chance' => $item['context']['chance'],
                        'icon'   => $item['context']['icon'],
                    ];
                    break 2;
                }
            }
        }

        if (!empty($win_item)) {
            return $win_item['id'];
        }

        return rand(0, count($items) - 1);
*/


        $data = [];
        $column = 'chance';
        foreach ($items as $item) {
            $data[] = [
                'id' => $item['id'],
                'chance' => $item['context']['chance'],
            ];
        }

        $rand = mt_rand(1, array_sum(array_column($data, $column)));
        $cur = $prev = 0;
        for ($i = 0, $count = count($data); $i < $count; ++$i) {
            $prev += $i != 0 ? $data[$i-1][$column] : 0;
            $cur += $data[$i][$column];
            if ($rand > $prev && $rand <= $cur) {
                return $i;
            }
        }

        return rand(0, count($items) - 1);

    }
}

if (! function_exists('get_bonus_items_left')) {
    function get_bonus_items_left()
    {
        $items_left = 0;
        for($i=0;$i<200;$i++) {
            if (config('options.bonus_pecul_' . $i . '_title', '') != '') {

                //Получаем общее кол-во сколько осталось доступно предметов-призов
                if (config('options.bonus_pecul_' . $i . '_available', 0) > 0) {
                    $items_left += config('options.bonus_pecul_' . $i . '_available', 0);
                }
            }
        }
        return $items_left;
    }
}

if (! function_exists('get_bonusm_items_left')) {
    function get_bonusm_items_left()
    {
        $items_left = 0;
        for($i=0;$i<200;$i++) {
            if (config('options.bonusm_pecul_' . $i . '_title', '') != '') {

                //Получаем общее кол-во сколько осталось доступно предметов-призов
                if (config('options.bonusm_pecul_' . $i . '_available', 0) > 0) {
                    $items_left += config('options.bonusm_pecul_' . $i . '_available', 0);
                }
            }
        }
        return $items_left;
    }
}

if (! function_exists('get_bonusth_items_left')) {
    function get_bonusth_items_left()
    {
        $items_left = 0;
        for($i=0;$i<200;$i++) {
            if (config('options.bonusth_pecul_' . $i . '_title', '') != '') {

                //Получаем общее кол-во сколько осталось доступно предметов-призов
                if (config('options.bonusth_pecul_' . $i . '_available', 0) > 0) {
                    $items_left += config('options.bonusth_pecul_' . $i . '_available', 0);
                }
            }
        }
        return $items_left;
    }
}

if (! function_exists('getItemsLeftCase')) {
    function getItemsLeftCase($case_id)
    {
        $items_left = 0;
        $case = \App\Models\Cases::find($case_id);
        if ($case) {
            $items = json_decode($case->items);
            foreach ($items as $item) {
                $items_left += $item->available;
            }
        }

        return $items_left;
    }
}

if (! function_exists('get_skin')) {
    function get_skin($skin_id)
    {
        $caseitem = \App\Models\CasesItem::where('item_id', $skin_id)->first();
        if ($caseitem) {
            return (object)[
                'name'  => $caseitem->title,
                'price' => $caseitem->price,
                'price_usd' => $caseitem->price_usd,
                'image' => $caseitem->image,
                'quality_type' => $caseitem->quality_type,
            ];
        }

        //Cache::forget('skin:'.$skin_id);
        $item = Cache::remember('skin:'.$skin_id, '1314000', function () use($skin_id) {
            return FALSE;
            return json_decode(@file_get_contents('https://rust.tm/api/ItemInfo/' . $skin_id . '_0/' . app()->getLocale() . '/?key=5sjz2jXfng5qkbz3MUSZjuoU7S7N1NN'));
        });

        if ($item && !isset($item->error)) {
            return (object)[
                'name'  => $item->name,
                'price_usd' => abs(round($item->min_price / 7000, 2)),
                'price' => abs(round($item->min_price / 7000, 2)) * config('options.exchange_rate_usd', 70),
                'image' => '',
                'quality_type' => '1',
            ];
        }
        return (object)[
            'name'  => '',
            'price' => 0,
            'price_usd' => 0,
            'image' => '',
            'quality_type' => '1',
        ];
    }
}

if (! function_exists('get_coupon_price')) {
    function get_coupon_price($inventory_type, $inventory_var, $shopitem_var=0)
    {
        $price = 0;
        $price_usd = 0;
        $name = '';

        if ($inventory_type == 1) {
            switch ($inventory_var) {
                case 3:
                    $price_usd = 5;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = 'VIP ' . __('3 days');
                    break;
                case 14:
                    $price_usd = 8;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = 'VIP ' . __('14 days');
                    break;
                case 30:
                    $price_usd = 12;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = 'VIP ' . __('30 days');
                    break;
            }
        } else if ($inventory_type == 2) {
            switch ($inventory_var) {
                case 5:
                    $price_usd = 0.5;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Бонус пополнения') . ' ' . '5%';
                    break;
                case 10:
                    $price_usd = 1;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Бонус пополнения') . ' ' . '10%';
                    break;
                case 15:
                    $price_usd = 1.5;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Бонус пополнения') . ' ' . '15%';
                    break;
                case 20:
                    $price_usd = 2;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Бонус пополнения') . ' ' . '20%';
                    break;
                case 40:
                    $price_usd = 4;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Бонус пополнения') . ' ' . '40%';
                    break;
                case 60:
                    $price_usd = 6;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Бонус пополнения') . ' ' . '60%';
                    break;
            }
        } else if ($inventory_type == 3) {
            switch ($inventory_var) {
                case 0.5:
                    $price_usd = 0.5;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$0.50';
                    break;
                case 1:
                    $price_usd = 1;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$1.00';
                    break;
                case 2:
                    $price_usd = 2;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$2.00';
                    break;
                case 3:
                    $price_usd = 3;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$3.00';
                    break;
                case 10:
                    $price_usd = 10;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$10.00';
                    break;
                case 15:
                    $price_usd = 15;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$15.00';
                    break;
                case 20:
                    $price_usd = 20;
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                    $name = __('Баланс на аккаунт') . ' ' . '$20.00';
                    break;
            }
        } else if ($inventory_type == 5) {
            $name = 'name_' . app()->getLocale();
            $shopitem = \App\Models\ShopItem::find($inventory_var);
            if ($shopitem) {

                $price_usd = $shopitem->price_usd;
                $price = $shopitem->price;
                $name = $shopitem->$name;

                if(isset($shopitem->variations)) {
                    $variations = json_decode($shopitem->variations);
                    foreach ($variations as $variation) {
                        if($variation->variation_id == $shopitem_var) {
                            $price_usd = $variation->variation_price_usd;
                            $price = $variation->variation_price;
                            $name = $name . ' - ' . $variation->variation_name;
                        }
                    }
                }
            }
        }

        return [$price, $price_usd, $name];
    }
}

if (!function_exists('getCurrentUserBalance')) {
    function getCurrentUserBalance()
    {
        if (isset(auth()->user()->balance)) {
            $balance = auth()->user()->balance;
        } else {
            $balance = 0;
        }


        if (app()->getLocale() != 'ru') {
            $balance = $balance / config('options.exchange_rate_usd', 70);
        }
        $balance = number_format($balance, 2, '.', ' ');

        if (app()->getLocale() == 'ru') {
            $balance = $balance . ' руб.';
        } else {
            $balance = '$' . $balance;
        }

        return $balance;
    }
}

if (!function_exists('getHoursAmount')) {
    function getHoursAmount($seconds)
    {
        return intval($seconds/3600);
    }
}

if (!function_exists('getOnlineTimeCase')) {
    function getOnlineTimeCase($case_id)
    {
        $online_time = 0;
        if (isset(auth()->user()->id)) {
            $case = \App\Models\Cases::find($case_id);
            if ($case) {
                $servers = json_decode($case->servers);
                $online_time = 0;
                foreach ($servers as $server) {
                    if ($server === NULL) continue;
                    $players_online = \App\Models\PlayersOnline::where('user_id', auth()->user()->id)->where('server', $server)->latest()->first();
                    if ($players_online) {
                        $online_time += $players_online->online_time;
                    }
                }
            }
        }

        return $online_time;
    }
}

if (!function_exists('cmp')) {
    function cmp($a, $b)
    {
        return strcmp($a["sort"], $b["sort"]);
    }
}
if (!function_exists('res_sort')) {
    function res_sort($a, $b)
    {
        return ($b["res_sort"] - $a["res_sort"]) ? ($b["res_sort"] - $a["res_sort"]) / abs($b["res_sort"] - $a["res_sort"]) : 0;
    }
}
if (!function_exists('pvp_sort')) {
    function pvp_sort($a, $b)
    {
        return ($b["pvp_sort"] - $a["pvp_sort"]) ? ($b["pvp_sort"] - $a["pvp_sort"]) / abs($b["pvp_sort"] - $a["pvp_sort"]) : 0;
    }
}
if (!function_exists('raids_sort')) {
    function raids_sort($a, $b)
    {
        return ($b["raids_sort"] - $a["raids_sort"]) ? ($b["raids_sort"] - $a["raids_sort"]) / abs($b["raids_sort"] - $a["raids_sort"]) : 0;
    }
}
if (!function_exists('hits_sort')) {
    function hits_sort($a, $b)
    {
        return ($b["hits_sort"] - $a["hits_sort"]) ? ($b["hits_sort"] - $a["hits_sort"]) / abs($b["hits_sort"] - $a["hits_sort"]) : 0;
    }
}

if (!function_exists('sort_bonus_type')) {
    function sort_bonus_type($a, $b)
    {
        return ($b["quality_type"] - $a["quality_type"]) ? ($b["quality_type"] - $a["quality_type"]) / abs($b["quality_type"] - $a["quality_type"]) : 0;
    }
}


if (!function_exists('sort_items_price')) {
    function sort_items_price($a, $b)
    {
        return ($a["price"] - $b["price"]) ? ($a["price"] - $b["price"]) / abs($a["price"] - $b["price"]) : 0;
    }
}

if (!function_exists('sort_items_price_desc')) {
    function sort_items_price_desc($a, $b)
    {
        return ($b["price"] - $a["price"]) ? ($b["price"] - $a["price"]) / abs($b["price"] - $a["price"]) : 0;
    }
}

if (!function_exists('paginate')) {
    function paginate($items, $perPage = 10, $page = NULL, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

if (!function_exists('translate')) {
    function translate($string)
    {
        $string = str_replace('days', __('дней'), $string);
        $string = str_replace('day', __('день'), $string);

        return $string;
    }
}

if (! function_exists('generationPromoCode')) {
    function generationPromoCode()
    {
        $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for ($i=0;$i<1000;$i++) {
            $code = substr(str_shuffle($permitted_chars), 0, 6);
            $promocode = \App\Models\PromoCode::where('code', $code)->first();
            if (!$promocode) break;
        }

        return $code;
    }
}