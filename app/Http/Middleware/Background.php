<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Request;
use App\Models\Banner;

class Background
{

    public function handle($request, Closure $next)
    {
        if ($request->path() != '/' && $request->path() != 'en') {
            $path = '/news';
        } else {
            $path = $request->path();
        }

        // if ($request->path() != '/') {
        //     $path = '/' . $request->path();
        // } else {
        //     $path = $request->path();
        // }

        // $path = str_replace(['ru', 'de', 'fr', 'it', 'es', 'uk'], '', $path);
        // $path = str_replace('//', '/', $path);

        $banner = Banner::where('path', $path)->first();
        $backgrounds = [];

        if ($banner) {
            $banners_item = json_decode($banner->banners);
            foreach ($banners_item as $banner_item) {
                $backgrounds[] = [
                    'image' => $banner_item->image,
                    'sort'  => $banner_item->sort,
                ];
            }

            usort($backgrounds, "cmp");
        }

        $backgrounds_new = [];
        if (count($backgrounds) < 5) {
            for ($i = 0; $i < 5; $i++) {
                foreach ($backgrounds as $background) {
                    $backgrounds_new[] = [
                        'image' => $background['image'],
                        'sort'  => $background['sort'],
                    ];
                }
            }
            $backgrounds = array_slice($backgrounds_new, 0, 5);
        }

        session()->put('backgrounds', $backgrounds);

        return $next($request); //пропускаем дальше - передаем в следующий посредник
    }

}
