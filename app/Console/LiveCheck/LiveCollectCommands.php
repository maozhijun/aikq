<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/10
 * Time: 12:45
 */

namespace App\Console\LiveCheck;


use App\Console\Anchor\CheckStreamCommand;
use App\Http\Controllers\Vendor\Weixin\WeixinTampleMessage;
use App\Models\Match\BasketMatch;
use App\Models\Match\LiveChannelLog;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use EasyWeChat\Foundation\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LiveCollectCommands extends Command
{
    protected $app;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'living_collect:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查正在直播的流是否中断';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $footballLives = $this->getLiveChannels(MatchLive::kSportFootball);
        foreach ($footballLives as $football) {
            $this->saveLiveLog($football, MatchLive::kSportFootball);
        }

        $basketLives = $this->getLiveChannels(MatchLive::kSportBasketball);
        foreach ($basketLives as $basketLive) {
            $this->saveLiveLog($basketLive, MatchLive::kSportBasketball);
        }
    }


    protected function saveLiveLog($match, $sport) {
        //以线路id作为参考
        //id,ch_id,match_id,sport,match_status,hname,aname,match_time,ch_name,show,platform,is_private,content,live_status,created_at,updated_at
        //leqiuba.cc
        $content = $match->content;
        if (strstr($content,"leqiuba.cc")) {
            dump("乐球吧的外链 不记录");
            return;
        }

        $log = new LiveChannelLog();
        $log->ch_id = $match->ch_id;
        $log->match_id = $match->match_id;
        $log->sport = $sport;
        $log->match_status = $match->match_status;
        $log->hname = $match->hname;
        $log->aname = $match->aname;
        $log->match_time = $match->match_time;
        $log->ch_name = $match->ch_name;
        $log->show = $match->show;
        $log->platform = $match->platform;
        $log->is_private = $match->isPrivate;
        $log->content = $content;

        //判断线路是否可以播放
        if (strlen($content) < 20) {
            $log->live_status = LiveChannelLog::kLiveStatusInvalid;//
            $this->sendWxTip("手机端直播未填写推流地址", $match);
        } else {
            $flg = CheckStreamCommand::streamCheck($content, 5);
            $log->live_status = $flg ? LiveChannelLog::kLiveStatusValid : LiveChannelLog::kLiveStatusInvalid;
            if (!$flg) {
                $this->sendWxTip("手机端直播推流中断啦", $match);
            }
        }
        try {
            $log->save();
        } catch (\Exception $exception) {
            dump($exception);
        }
    }

    /**
     * 发送微信提醒
     * @param $first
     * @param $match
     */
    protected function sendWxTip($first, $match) {
        $show = $match['show'];
        if ($show != MatchLiveChannel::kShow) {
            return;//不显示的线路不提醒
        }
        $ch_id = $match['ch_id'];
        $key = "sendWxTip_" . $ch_id;
        $cache = Redis::get($key);
        if (empty($cache)) {
            //$first = "手机端直播推流中断啦。";
            $keyword1 = $match['hname']." VS ".$match['aname'];
            $keyword2 = "线路名称《" . $match['ch_name'] ."》";
            WeixinTampleMessage::liveTip($this->getWxApp(),"oxCF5w6OQj5mvpu4hKWqCeoKFqCk", $first, $keyword1, $keyword2);
            Redis::setEx($key, 15 * 60, '1234');
        }
    }

    /**
     * 获取足球直播线路
     * @param $sport
     * @return array
     */
    protected function getLiveChannels($sport) {
        $liveTable = 'match_lives';
        $channelTable = 'match_live_channels';

        if ($sport == MatchLive::kSportBasketball) {
            $query = BasketMatch::query();
            $matchTable = "basket_matches";
        } else {
            $query = Match::query();
            $matchTable = "matches";
        }

        $channelSelect = "$channelTable.id as ch_id, $channelTable.name as ch_name, $channelTable.show,";
        $channelSelect .= "$channelTable.isPrivate, $channelTable.content, $channelTable.platform";

        $matchSelect = "$matchTable.id as match_id, $matchTable.hname, $matchTable.aname, $matchTable.time as match_time, $matchTable.status as match_status";

        $query->join($liveTable, $liveTable .'.match_id', '=', $matchTable.'.id');//有直播线路的比赛
        $query->join($channelTable, $channelTable.'.live_id', '=', $liveTable.'.id');
        $query->where("$liveTable.sport", "=", $sport);
        $query->where($matchTable.".status",  ">", 0);//正在进行的比赛
        $query->where("$matchTable.time", '>', date('Y-m-d H:i', strtotime('-5 hours')));
        $query->where("$channelTable.isPrivate", "=", MatchLive::kIsPrivate);
        $query->where(function ($orQuery) use ($channelTable) {
            $orQuery->where("$channelTable.platform", MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere("$channelTable.platform", MatchLiveChannel::kPlatformWAP);
        });

        $query->select(DB::raw($matchSelect));
        $query->addSelect(DB::raw($channelSelect));

        return $query->get();
    }

    protected function getWxApp() {
        $app = $this->app;
        if (!isset($app)) {
            $app = new Application(config('wechat_lg'));
            $this->app = $app;
        }
        return $app;
    }

}