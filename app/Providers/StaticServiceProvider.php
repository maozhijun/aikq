<?php

namespace App\Providers;

use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Models\Admin\Test;
use App\Models\Anchor\AnchorRoom;
use App\Models\Match\MatchLiveChannel;
use App\Models\Match\MatchLiveChannelLog;
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

        //线路日志记录  开始
        MatchLiveChannel::saving(function ($channel) {
            if ($channel->auto == MatchLiveChannel::kAutoHand) {//只有手动录入的才记录日志
                $channel_id = $channel->id;
                $oldChannel = MatchLiveChannel::query()->find($channel_id);
                $oldJson = isset($oldChannel) ? json_encode($oldChannel) : "";
                //记录未保存前的内容
                Redis::setEx(MatchLiveChannel::SAVE_KEY_PREFIX.$channel->id, 5, $oldJson);
            }
        });

        MatchLiveChannel::saved(function ($channel) {
            if ($channel->auto == MatchLiveChannel::kAutoHand) {//只有手动录入的才记录日志
                $test = new Test();
                $old = Redis::get(MatchLiveChannel::SAVE_KEY_PREFIX.$channel->id);
                $oldArray = json_decode($old, true);
                $oldArray = !isset($oldArray) ? [] :$oldArray;
                MatchLiveChannelLog::saveLog($oldArray, $channel);
                $test->save();
            }
        });
        //线路日志记录 结束

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
