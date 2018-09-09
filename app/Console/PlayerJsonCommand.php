<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:40
 */

namespace App\Console;


use App\Http\Controllers\PC\Live\LiveController;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlayerJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'player_json_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '终端页json异步请求';

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
    public function handle()
    {
        $this->staticPlayerJson(new Request());
    }

    /**
     * 静态化播放页面异步请求
     * @param Request $request
     */
    public function staticPlayerJson(Request $request) {
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);

        if (!isset($json['matches'])) return;
        $matches = $json['matches'];
        $con = new LiveController();
        foreach ($matches as $time=>$match_array) {
            foreach ($match_array as $match) {
                if (!isset($match) || !isset($match['time'])) {
                    continue;
                }
                $m_time = strtotime($match['time']);
                $status = $match['status'];
                $now = time();

                $flg_1 = $m_time >= $now && $now + 30 * 60 >= $m_time;//开赛前1小时
                $flg_2 = false;//$m_time <= $now && $m_time + 3 * 60 * 60  >= $now;//开赛后3小时
                if ($status > 0 || $flg_1 || $flg_2 ) {//1小时内的比赛静态化接口、天天源不做静态化。
                    if (isset($match['channels'])) {
                        $channels = $match['channels'];
                        foreach ($channels as $channel) {
                            $ch_id = $channel['id'];
                            if ($channel['type'] != MatchLiveChannel::kTypeTTZB) {
                                $con->staticLiveUrl($request, $ch_id, true);
                                usleep(100);
                            }
                        }
                    }
                }
            }
        }
    }

}