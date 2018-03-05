<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:40
 */

namespace App\Console;


use App\Http\Controllers\PC\Live\LiveController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

/**
 * 未开赛的比赛线路静态化
 * Class NoStartPlayerJsonCommand
 * @package App\Console
 */
class NoStartPlayerJsonCommand extends Command
{
    const NoStartPlayerJsonCommandCacheKey = "NoStartPlayerJsonCommandCacheKey";
    const Once_total = 20;//一次更新线路个数

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ns_player_json_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '未开始比赛的线路json静态化';

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
        //每十分钟一次，一次执行 50条线路
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);

        $cache = Redis::get(self::NoStartPlayerJsonCommandCacheKey);
        $ch_array = json_decode($cache, true);
        $ch_array = is_null($ch_array) ? [] : $ch_array;

        if (!isset($json['matches'])) return;
        $matches = $json['matches'];
        $start = time();
        $index = 0;
        $con = new LiveController();
        foreach ($matches as $time=>$match_array) {
            foreach ($match_array as $match) {
                if (!isset($match) || !isset($match['time'])) {
                    continue;
                }
                $m_time = strtotime($match['time']);
                $status = $match['status'];
                $now = time();
                $flg_1 = $m_time >= $now && $now + 60 * 60 < $m_time;//距离开赛大于1小时的比赛
                if ($status == 0 || $flg_1) {//1小时内的比赛静态化接口、天天源不做静态化。
                    if (isset($match['channels'])) {
                        $channels = $match['channels'];
                        foreach ($channels as $channel) {
                            $ch_id = $channel['id'];
                            if (!in_array($ch_id, $ch_array)) {
                                if ($index > self::Once_total) break;
                                $ch_array[] = $ch_id;
                                $con->staticLiveUrl(new Request(), $ch_id, true);
                                $index++;
                            }
                        }
                    }
                }
            }
        }
        echo 'exec time : ' . ( time() - $start ) . '\n';
        //echo 'ch_ids = ' . implode(',', $ch_array);
        Redis::setEx(self::NoStartPlayerJsonCommandCacheKey, 60 * 60, json_encode($ch_array));
    }

}