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

class SocketScoreCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socket_score_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新比分';

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
        $ch = curl_init();
        $url = asset('http://match.liaogou168.com/static/change/1/score.json');
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $football = curl_exec ($ch);
        curl_close ($ch);
        $football = json_decode($football,true);

        $ch = curl_init();
        $url = asset('http://match.liaogou168.com/static/change/2/score.json');
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $basketball = curl_exec ($ch);
        curl_close ($ch);
        $basketball = json_decode($basketball,true);

        $json = Redis::get('redis_living_room');
        $json = json_decode($json,true);
        $score = array();
        foreach ($json as $item){
            if ($item['sport'] == 1){
                $match = $football[$item['match_id']];
                if (isset($match)){
                    $item['hscore'] = $match['hscore'];
                    $item['ascore'] = $match['ascore'];
                    $item['status'] = $match['status'];
                    $item['time'] = $match['time'];
                    $item['time2'] = $match['time'];
                    $score[] = $item;
                }
            }
            if ($item['sport'] == 2){
                $match = $basketball[$item['match_id']];
                if (isset($match)){
                    $item['hscore'] = $match['hscore'];
                    $item['ascore'] = $match['ascore'];
                    $item['status'] = $match['status'];
                    $item['time'] = SocketScoreCacheCommand::getStatusTextCn($match['status'],$match['system']);
                    $item['time2'] = $match['time'];
                    $score[] = $item;
                }
            }
        }
        Redis::set('redis_refresh_match',json_encode($score));
    }

    public static function getStatusTextCn($status, $isHalfFormat = false)
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return $isHalfFormat ? "上半场" : "第一节";
            case 2:
                return $isHalfFormat ? "" : "第二节";
            case 3:
                return $isHalfFormat ? "下半场" : "第三节";
            case 4:
                return $isHalfFormat ? "" : "第四节";
            case 5:
                return "加时1";
            case 6:
                return "加时2";
            case 7:
                return "加时3";
            case 8:
                return "加时4";
            case 50:
                return "中场";
            case -1:
                return "已结束";
            case -5:
                return "推迟";
            case -2:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }
}