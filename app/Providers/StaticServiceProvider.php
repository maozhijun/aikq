<?php

namespace App\Providers;

use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class StaticServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        AnchorRoom::saved(function ($room){
            //终端静态化
            $this->pushStaticUrl('/api/static/anchor/room/'.$room->id);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function pushStaticUrl($url) {
        $cache = Redis::get('akq_service_static_url');
        $roomArray = json_decode($cache, true);
        if (is_null($roomArray) || count($roomArray) == 0) {
            $roomArray = array();
        }
        if (in_array($url,$roomArray)){
            return;
        }
        $roomArray[] = $url;
        Redis::set('akq_service_static_url',json_encode($roomArray));
    }
}
