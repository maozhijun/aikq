<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:24
 */

namespace App\Console;


use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Live\LiveController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveDetailCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live_detail_cache:run';

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
    public function handle() {
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json) || !isset($json['matches'])) {
            echo '获取数据失败';
            return;
        }
        $json = $json['matches'];
        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $mid = $match['mid'];
                $time = isset($match['time']) ? $match['time'] : 0;
                $now = time();
                if ($time == 0 ) {//只静态化赛前4小时内 的比赛终端。
                    continue;
                }
                $start_time = strtotime($time);//比赛时间
                $flg_1 = $start_time >= $now && $now + 5 * 60 * 60 >= $start_time;//开赛前1小时
                $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                if ( $flg_1 || $flg_2 ) {
                    try {
                        $this->staticDetail($match);
                    } catch (\Exception $exception) {
                        dump($exception);
                    }
                }
            }
        }
    }


    public function staticDetail($match) {
        $mid = $match['mid'];
        $sport = $match['sport'];
        try {
            $mCon = new \App\Http\Controllers\Mobile\Live\LiveController();
            $con = new LiveController();
            $request = new Request();
            $json = AikanQController::matchDetailArray($mid, $sport, false);
            $mJson = AikanQController::matchDetailArray($mid, $sport, true);
            $mCon->liveDetailStaticObj($mJson, $sport, $mid);//wap 页面静态化

            $mjson = $con->getLiveUrlMatchFromDb(new Request(), $mid, $sport, true);
            $pjson = $con->getLiveUrlMatchFromDb(new Request(),$mid, $sport, false);
            $phtml = $con->dbMatchPlayerChannel($request,$mid,$sport);

            if ($sport == 1) {
                $html = $con->detailHtml($json, $mid);
                if (!empty($html)) {
                    Storage::disk("public")->put("/live/football/". $mid. ".html", $html);
                }
                //每一个比赛的player页面生成
                if (!empty($phtml)) {
                    Storage::disk("public")->put("/live/spPlayer/player-" . $mid . '-' . $sport . ".html", $phtml);
                    //暂时兼容旧链接
                    Storage::disk("public")->put("/live/spPlayer/match_channel-" . $mid . '-' . $sport . ".html", $phtml);
                }
                //match.json

                if (!empty($mjson)) {
                    Storage::disk("public")->put("/match/live/url/match/m/" . $mid . "_" . $sport .".json", $mjson);
                }
                if (!empty($pjson)) {
                    Storage::disk("public")->put("/match/live/url/match/pc/" . $mid . "_" . $sport .".json", $pjson);
                }
            } else if($sport == 2){
                $html = $con->basketDetailHtml($json, $mid);
                if (!empty($html)) {
                    Storage::disk("public")->put("/live/basketball/". $mid. ".html", $html);
                }
                //每一个比赛的player页面生成
                if (!empty($phtml)) {
                    Storage::disk("public")->put("/live/spPlayer/player-" . $mid . '-' . $sport . ".html", $phtml);
                    //暂时兼容旧链接
                    Storage::disk("public")->put("/live/spPlayer/match_channel-" . $mid . '-' . $sport . ".html", $phtml);
                }
                //match.json
                if (!empty($mjson)) {
                    Storage::disk("public")->put("/match/live/url/match/m/" . $mid . "_" . $sport .".json", $mjson);
                }
                if (!empty($pjson)) {
                    Storage::disk("public")->put("/match/live/url/match/pc/" . $mid . "_" . $sport .".json", $pjson);
                }
            } else if ($sport == 3) {
                $html = $con->otherDetailHtml($json, $mid);
                if (!empty($html)) {
                    Storage::disk("public")->put("/live/other/". $mid. ".html", $html);
                }
                //每一个比赛的player页面生成
                if (!empty($phtml)) {
                    Storage::disk("public")->put("/live/spPlayer/player-" . $mid . '-' . $sport . ".html", $phtml);
                }
                //match.json
                if (!empty($mjson)) {
                    Storage::disk("public")->put("/match/live/url/match/m/" . $mid . "_" . $sport .".json", $mjson);
                }
                if (!empty($pjson)) {
                    Storage::disk("public")->put("/match/live/url/match/pc/" . $mid . "_" . $sport .".json", $pjson);
                }
            }
        } catch (\Exception $exception) {
            dump($exception);
        }
    }

    public static function flushLiveDetailHtml($match_id, $sport, $ch_id = '') {
        $ch = curl_init();
        $url = asset('/live/cache/match/detail_id/' . $match_id . '/' . $sport) . '?ch_id=' . $ch_id;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }

}