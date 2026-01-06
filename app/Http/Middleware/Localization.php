<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Request;

class Localization
{
    public static $mainLanguage = 'ru'; //основной язык, который не должен отображаться в URl
    public static $languages = ['en', 'ru', 'de', 'fr', 'it', 'es', 'uk']; // Указываем, какие языки будем использовать в приложении.

    /*
     * Проверяет наличие корректной метки языка в текущем URL
     * Возвращает метку или значеие null, если нет метки
     */
    public static function getLocale()
    {
        $uri = Request::path(); //получаем URI

        $segmentsURI = explode('/',$uri); //делим на части по разделителю "/"
        //Проверяем метку языка  - есть ли она среди доступных языков
        if (!empty($segmentsURI[0]) && in_array($segmentsURI[0], self::$languages)) {
            if ($segmentsURI[0] != self::$mainLanguage) return $segmentsURI[0];
        }
        return null;
    }

    /*
    * Устанавливает язык приложения в зависимости от метки языка из URL
    */
    public function handle($request, Closure $next)
    {
        if (session()->has('locale')) {
            $locale = session()->get('locale');
        } else {
            // получаем язык
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            } else {
                $lang = self::$mainLanguage;
            }
            // проверяем язык
            if (in_array($lang, self::$languages)){
                $locale = $lang;
            } else {
                $locale = self::getLocale();
            }
        }

        if ($locale) {
            if (in_array($locale, ['de', 'fr', 'it', 'es', 'uk'])){
                $locale = 'en';
            }
            App::setLocale($locale);
        } else {
            //если метки нет - устанавливаем основной язык $mainLanguage
            App::setLocale(self::$mainLanguage);
        }

        return $next($request); //пропускаем дальше - передаем в следующий посредник
    }

}
