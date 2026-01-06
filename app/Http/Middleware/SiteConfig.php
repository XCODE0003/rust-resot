<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Option;

class SiteConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {

        //Задаем конфиг recaptcha_v3 из настроек сайта
        $recaptcha_sitekey = Option::select('value')->where('key', 'recaptcha_sitekey')->first();
        $recaptcha_secret = Option::select('value')->where('key', 'recaptcha_secret')->first();
        if ($recaptcha_sitekey && $recaptcha_secret) {
            config(['recaptchav3.sitekey' => $recaptcha_sitekey->value]);
            config(['recaptchav3.secret' => $recaptcha_secret->value]);
        }

        //Задаем конфиг smtp из настроек сайта
        $smtp_host = Option::select('value')->where('key', 'smtp_host')->first();
        $smtp_port = Option::select('value')->where('key', 'smtp_port')->first();
        $smtp_user = Option::select('value')->where('key', 'smtp_user')->first();
        $smtp_password = Option::select('value')->where('key', 'smtp_password')->first();
        $smtp_from = Option::select('value')->where('key', 'smtp_from')->first();
        $smtp_name = Option::select('value')->where('key', 'smtp_name')->first();
        if ($smtp_host && $smtp_port && $smtp_user && $smtp_password) {
            config(['mail.mailers.smtp.host' => $smtp_host->value]);
            config(['mail.mailers.smtp.port' => $smtp_port->value]);
            config(['mail.mailers.smtp.username' => $smtp_user->value]);
            config(['mail.mailers.smtp.password' => $smtp_password->value]);
            config(['mail.from.address' => $smtp_from->value]);
            config(['mail.from.name' => $smtp_name->value]);
        }

        //Задаем конфиг форума
        $forum_link = Option::select('value')->where('key', 'forum_link')->first();
        $forum_type = Option::select('value')->where('key', 'forum_type')->first();
        $forum_host = Option::select('value')->where('key', 'forum_host')->first();
        $forum_port = Option::select('value')->where('key', 'forum_port')->first();
        $forum_database = Option::select('value')->where('key', 'forum_database')->first();
        $forum_username = Option::select('value')->where('key', 'forum_username')->first();
        $forum_password = Option::select('value')->where('key', 'forum_password')->first();


        if ($forum_link && $forum_type && $forum_host) {
            config(['database.connections.xenforo.url' => $forum_link->value]);
            config(['database.connections.xenforo.url' => $forum_type->value]);
            config(['database.connections.xenforo.host' => $forum_host->value]);
            config(['database.connections.xenforo.port' => $forum_port->value]);
            config(['database.connections.xenforo.database' => $forum_database->value]);
            config(['database.connections.xenforo.username' => $forum_username->value]);
            config(['database.connections.xenforo.password' => $forum_password->value]);
        }

        //Задаем конфиг платежных систем
        $paypal_client_id = Option::select('value')->where('key', 'paypal_client_id')->first();
        $paypal_secret = Option::select('value')->where('key', 'paypal_secret')->first();
        if ($paypal_client_id && $paypal_secret) {
            config(['paypal.client_id' => $paypal_client_id->value]);
            config(['paypal.secret' => $paypal_secret->value]);
        }

        return $next($request);
    }
}
