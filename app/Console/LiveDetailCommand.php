<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:24
 */

namespace App\Console;


use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\IntF\Common\LeagueDataTool;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\Live\LiveController;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Match;
use App\Models\LgMatch\Season;
use App\Models\Subject\SubjectLeague;
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
    protected $signature = 'live_detail_cache:run {type}';

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
//        foreach ($articles as $index=>$article){
//            $ch = curl_init();
//            $url = 'http://cms.aikq.cc/static/article/'.$article->id;
//            dump($url);
//            curl_setopt($ch, CURLOPT_URL,$url);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_TIMEOUT, 2);//8秒超时
//            curl_exec ($ch);
//            curl_close ($ch);
//            if ( ($index + 1) % 20 == 0 ) {
//                sleep(5);
//            }
//        }
//        return;


        $type = $this->argument('type');
        switch ($type) {
            case 'pc':
                $this->staticPcLiveDetail();
                break;
            case 'm':
                $this->staticMLiveDetail();
                break;
            case 'all':
                $this->staticPcLiveDetail();
                $this->staticMLiveDetail();
                break;
            default :
                $this->staticLeagueLiveDetail($type);
                break;
        }
    }



    protected function staticLeagueLiveDetail($type) {
        $start = time();
        //静态化专题 未开始的赛事终端
        $sl = SubjectLeague::getSubjectLeagueByEn($type);
        if (!isset($sl)) {
            echo "没有 $type 赛事专题";
        }
        $sport = $sl["sport"];
        $lid = $sl["lid"];
        echo $type . "专题比赛终端静态化开始 \n";

        //获取当前赛季 未开始 的赛程
        if ($sport == SubjectLeague::kSportBasketball) {//篮球
            $season = BasketTeam::query()->where("lid", $lid)->orderByDesc("year")->first();
            if (!isset($season)) {
                echo "没有赛季 \n";
                return;
            }
            $year = $season["name"];
            $query = BasketMatch::query()->where("lid", $lid)->where("season", $year);
            $matches = $query->where("status", 0)->get();
        } else {//足球
            $season = Season::query()->where("lid", $lid)->orderByDesc("year")->first();
            if (!isset($season)) {
                echo "没有赛季 \n";
                return;
            }
            $year = $season["name"];
            $matches = Match::query()->where("lid", $lid)->where("season", $year)->where("status", 0)->get();
        }

        echo " 总共赛事 " . count($matches) . "场 \n";
        $cache = [];
        foreach ($matches as $index=>$match) {
            $staticTime = time();
            $mid = $match["id"];
            $time = $match["time"];
            $hname = $match["hname"];
            $aname = $match["aname"];

            $hid = $match["hid"];
            $aid = $match["aid"];
            $key = $sport . "_" . $hid . "_" . $aid;
            if (!isset($cache[$key])) {
                $cache[$key] = 1;
                $url = env('CMS_URL').'/live/cache/match/detail_id/' . $mid . '/' . $sport . '/';
                echo $index . "  " . $time . " $hname VS $aname  url = " . $url . "\n";
                self::flushLiveDetailHtml($url);
                echo "静态化耗时 ".(time() - $staticTime)." 秒 \n";
            } else {
                echo $index . "  " . $time . " $hname VS $aname  已静态化过了 \n";
            }
        }

        echo $type . "专题比赛终端静态化结束 使用时间 ".(time() - $start)."秒 \n";
    }

    protected function staticPcLiveDetail() {
        $cache = Storage::get('/public/static/json/pc/lives.json');
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
                $flg_1 = true;//$start_time >= $now && $now + 45 * 60 >= $start_time;//开赛前1小时
                $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                if ( $flg_1 || $flg_2 ) {
                    try {
                        $url = env('CMS_URL').'/live/cache/match/detail_id/' . $mid . '/' . $sport . '?';
                        self::flushLiveDetailHtml($url);
//                        $channels = $match['channels'];
//                        foreach ($channels as $channel) {
//                            $url = env('CMS_URL').'/live/cache/match/detail_id/' . $mid . '/' . $sport . '?ch_id=' . $channel['id'];
//                            dump($url);
//                            self::flushLiveDetailHtml($url);
//                        }
                    } catch (\Exception $exception) {
                        dump($exception);
                    }
                }
            }
        }
    }


    protected function staticMLiveDetail() {
        $liveCon = new LiveController();
        $request = new Request();
        $intF = new AikanQController();
        $mJson = $intF->livesJsonData('', true);
        $json = $mJson['matches'];
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
                $flg_1 = true;//$start_time >= $now && $now + 45 * 60 >= $start_time;//开赛前1小时
                $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                if ( $flg_1 || $flg_2 ) {
                    try {
                        $url = env('CMS_URL').'/live/cache/match/detail_id/' . $mid . '/' . $sport . '/';
                        self::flushLiveDetailHtml($url);
//                        $channels = $match['channels'];
//                        foreach ($channels as $channel) {
//                            $url = env('CMS_URL').'/live/cache/match/detail_id/' . $mid . '/' . $sport . '?ch_id=' . $channel['id'];
//                            dump($url);
//                            self::flushLiveDetailHtml($url);
//                        }
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