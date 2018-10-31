<?php

namespace App\Providers;

use App\Console\HtmlStaticCommand\Team\TeamDetailCommand;
use App\Http\Controllers\Admin\Match\MatchController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\Match\MatchLiveChannelLog;
use App\Models\Subject\SubjectSpecimen;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
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
            $url = env('CMS_URL') . '/api/static/anchor/room/'.$room->id;
            $this->pushStaticUrl($url);
        });

        Anchor::updated(function ($anchor){
            //终端静态化
            $url = env('CMS_URL') . '/api/static/anchor/room/'.$anchor->room->id;
            $this->pushStaticUrl($url);
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
            $old = Redis::get(MatchLiveChannel::SAVE_KEY_PREFIX.$channel->id);
            $oldArray = json_decode($old, true);
            $oldArray = !isset($oldArray) ? [] :$oldArray;

            if ($channel->auto == MatchLiveChannel::kAutoHand) {//只有手动录入的才记录日志
                MatchLiveChannelLog::saveLog($oldArray, $channel);
            }

            $ch_id = $channel->id;
            $live_id = $channel->live_id;
            $matchLive = MatchLive::query()->find($live_id);
            $sport = $matchLive->sport;
            $match_id = $matchLive->match_id;
            if (count($oldArray) == 0) {//新建的线路
                $show = $channel->show;
                if ($show == MatchLiveChannel::kShow) {
                    $private = $channel->isPrivate;
                    if ($private == MatchLiveChannel::kPrivate) {
                        //有版权
                        MatchController::flushAikqLive($match_id, $sport, $ch_id);//刷新终端、线路json
                    } else {
                        //无版权
                        MatchController::flush310Live($match_id, $sport, $ch_id);//刷新终端、线路json
                    }
                }
            } else {//修改的线路
                MatchController::flush310Live($match_id, $sport, $ch_id);//刷新终端、线路json
                MatchController::flushAikqLive($match_id, $sport, $ch_id);//刷新终端、线路json
            }
            //静态化cms接口文件
            $liveCon = new \App\Http\Controllers\PC\Live\LiveController();
            $liveCon->staticLiveChannelsJson(new Request(), $match_id, $sport);
        });
        //线路日志记录 结束

        //录像终端静态化
        SubjectVideoChannels::saved(function($videoChannel) {
            $subCon = new SubjectController();
            $subCon->staticSubjectVideoNew($videoChannel);
        });

        //集锦终端静态化
        SubjectSpecimen::saved(function ($specimen) {
            $subCon = new SubjectController();
            $subCon->staticSubjectSpecimenNew($specimen, false);//静态化pc终端
        });

        //赛事排名json、球队终端静态化
        MatchLive::saved(function ($matchLive) {
            $sport = $matchLive->sport;
            $mid = $matchLive->match_id;
            TeamDetailCommand::onTeamDetailStaticByMid($sport, $mid);
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
        Controller::execUrl($url, 2, false);

//        $cache = Redis::get('akq_service_static_url');
//        $roomArray = json_decode($cache, true);
//        if (is_null($roomArray) || count($roomArray) == 0) {
//            $roomArray = array();
//        }
//        if (in_array($url,$roomArray)){
//            return;
//        }
//        $roomArray[] = $url;
//        Redis::set('akq_service_static_url',json_encode($roomArray));
    }
}
