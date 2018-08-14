<?php

namespace App\Providers;

use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Http\Request;
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
            $con = new AnchorController();
            $con->staticRoom(new Request(),$room->id);
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
}
