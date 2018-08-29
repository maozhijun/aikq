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
use App\Models\Article\PcArticle;
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
//        $articles = PcArticle::query()->where('status', PcArticle::kStatusPublish)->get();
//        foreach ($articles as $article){
//            $ch = curl_init();
//            $url = 'http://leisuzhibo.cc/static/article/'.$article->id;
//            curl_setopt($ch, CURLOPT_URL,$url);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_TIMEOUT, 2);//8秒超时
//            curl_exec ($ch);
//            curl_close ($ch);
//        }
//        return;
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json) || !isset($json['matches'])) {
            echo '获取数据失败';
            return;
        }
        $json = $json['matches'];
        $liveCon = new LiveController();
        $request = new Request();
        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $sport = $match['sport'];
                $mid = $match['mid'];
                $time = isset($match['time']) ? $match['time'] : 0;
                $now = time();
                if ($time == 0 ) {//只静态化赛前4小时内 的比赛终端。
                    continue;
                }
                $start_time = strtotime($time);//比赛时间
                $flg_1 = true;//$start_time >= $now && $now + 5 * 60 * 60 >= $start_time;//开赛前1小时
                $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                if ( $flg_1 || $flg_2 ) {
                    try {
                        $channels = $match['channels'];
                        foreach ($channels as $channel) {
//                            $url = 'http://leisuzhibo.cc/live/cache/match/detail_id/' . $mid . '/' . $sport . '?ch_id=' . $channel['id'];
//                            dump($url);
//                            self::flushLiveDetailHtml($url);
                            $liveCon->staticLiveDetailById($request, $mid, $sport, $channel['id']);
                        }
                        //$liveCon->staticLiveDetailById($request, $mid, $sport);
                    } catch (\Exception $exception) {
                        dump($exception);
                    }
                }
            }
        }
    }

    public static function flushLiveDetailHtml($url) {
        $ch = curl_init();
        //$url = asset('/live/cache/match/detail_id/' . $match_id . '/' . $sport) . '?ch_id=' . $ch_id;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }

}