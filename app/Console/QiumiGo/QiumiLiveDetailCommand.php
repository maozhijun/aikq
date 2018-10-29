<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:24
 */

namespace App\Console\QiumiGo;


use App\Http\Controllers\PC\Live\LiveController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QiumiLiveDetailCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qiumi_live_detail_cache:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '直播终端缓存';

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
        $type = $this->argument('type');

        $isLiving = $type == "living";
        $this->staticPcLiveDetail($isLiving);
        $this->staticMLiveDetail($isLiving);
    }

    protected function staticPcLiveDetail($isLiving = false)
    {
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json) || !isset($json['matches'])) {
            echo '获取数据失败';
            return;
        }
        $json = $json['matches'];
        $liveCon = new LiveController();
        $request = new Request();

        foreach ($json as $index => $datas) {
            foreach ($datas as $match) {
                //如果要求是只在只有直播中的才缓存
                if ($isLiving && !$match['isMatching']) continue;

                $sport = $match['sport'];
                $mid = $match['mid'];
                $lid = isset($match['lid']) ? $match['lid'] : 0;
                if ($sport == 2 && $lid == 1) {
                    $time = isset($match['time']) ? $match['time'] : 0;
                    $now = time();
                    if ($time == 0) {//只静态化赛前4小时内 的比赛终端。
                        continue;
                    }
                    $start_time = strtotime($time);//比赛时间
                    $flg_1 = true;//$start_time >= $now && $now + 45 * 60 >= $start_time;//开赛前1小时
                    $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                    if ($flg_1 || $flg_2) {
                        try {
                            $channels = $match['channels'];
                            foreach ($channels as $channel) {
                                $ch_id = $channel['id'];
                                dump($ch_id);
                                if (is_numeric($ch_id)) {
                                    $liveCon->staticLiveUrl($request, $ch_id, true, null, true);
                                }
                            }
                        } catch (\Exception $exception) {
                            dump($exception);
                        }
                    }
                }
            }
        }
    }

    protected function staticMLiveDetail($isLiving = false)
    {
        $cache = Storage::get('/public/static/m/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json) || !isset($json['matches'])) {
            echo '获取数据失败';
            return;
        }
        $json = $json['matches'];
        $liveCon = new LiveController();
        $request = new Request();

        foreach ($json as $index => $datas) {
            foreach ($datas as $match) {
                //如果要求是只在只有直播中的才缓存
                if ($isLiving && !$match['isMatching']) continue;

                $sport = $match['sport'];
                $mid = $match['mid'];
                $lid = isset($match['lid']) ? $match['lid'] : 0;
                if ($sport == 2 && $lid == 1) {
                    $time = isset($match['time']) ? $match['time'] : 0;
                    $now = time();
                    if ($time == 0) {//只静态化赛前4小时内 的比赛终端。
                        continue;
                    }
                    $start_time = strtotime($time);//比赛时间
                    $flg_1 = true;//$start_time >= $now && $now + 45 * 60 >= $start_time;//开赛前1小时
                    $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                    if ($flg_1 || $flg_2) {
                        try {
                            $channels = $match['channels'];
                            foreach ($channels as $channel) {
                                $ch_id = $channel['id'];
                                dump($ch_id);
                                if (is_numeric($ch_id)) {
                                    $liveCon->staticLiveUrl($request, $ch_id, true, null, true);
                                }
                            }
                        } catch (\Exception $exception) {
                            dump($exception);
                        }
                    }
                }
            }
        }
    }
}