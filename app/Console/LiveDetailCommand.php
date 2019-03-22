<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:24
 */

namespace App\Console;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Live\LiveController;
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Match;
use App\Models\LgMatch\Season;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\Subject\SubjectLeague;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class LiveDetailCommand extends Command
{

    const TIME_OUT = 40;

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

            case 'check404':
                $this->check404();
                break;
            case 'static404':
                $this->static404();
                break;
            case 'staticExLink':
                $this->staticExLink();
                break;
            default :
                $this->staticLeagueLiveDetail($type);
                break;
        }
    }

    protected function staticExLink() {
        $path = 'c:\Users\11247\Desktop\ex_link404.txt';
        $content = file_get_contents($path);
        $ids = explode("\n", $content);
        $host = "http://cms.aikanqiu.com";
        foreach ($ids as $id) {
            $id = trim($id);
            $mc = MatchLiveChannel::query()->find($id);
            if (!isset($mc)) {
                continue;
            }
            $liveId = $mc["live_id"];
            $ml = MatchLive::query()->find($liveId);
            if (!isset($ml)) {
                continue;
            }
            $mid = $ml["match_id"];
            $sport = $ml["sport"];
            $url = $host."/live/cache/match/detail_id/".$mid."/".$sport;
            Controller::execUrl($url, 20);
            echo "静态化直播终端 $url \n";
            sleep(1);
        }
    }

    protected function static404() {
        $path = 'c:\Users\11247\Desktop\404_ex.txt';
        $content = file_get_contents($path);
        $urls = explode("\r", $content);
        $host = "http://cms.aikanqiu.com";
        $index = 1;
        $len = count($urls);
        foreach ($urls as $url) {
            $lenMsg = $len ."/" . $index++;
            $startTime = time();
            $params = explode("404", $url);
            if (!isset($params[1])) {
                echo $lenMsg . " 没有参数 " . $url;
                continue;
            }
            $url = trim($params[1]);
            if (empty($url)) continue;

            preg_match('/live(\d)(\d{4})(\d+).html/', $url, $matches);

            if (count($matches) != 4) {
                echo $lenMsg . " 不匹配 " . $url;
                continue;
            }

            $sport = $matches[1];
            $minId = $matches[2];
            $maxId = $matches[3];

            $key = $sport."".$minId."".$maxId;
            $cache = Redis::get($key);
            if (!empty($cache)) {
                echo $lenMsg . " 已静态化". $cache . "\n";
                continue;
            }

            $minId = intval($minId);
            $maxId = intval($maxId);

            //查询比赛
            if ($sport == 1) {
                //足球
                $query = \App\Models\Match\Match::query();
            } else {
                //篮球
                $query = \App\Models\Match\BasketMatch::query();
            }
            $query->where(function ($or) use ($minId, $maxId) {
                $or->where("aid", $minId);
                $or->where("hid", $maxId);
            });

            $query->orWhere(function ($or) use ($minId, $maxId) {
                $or->where("aid", $maxId);
                $or->where("hid", $minId);
            });
            //echo $sport . "    "  .$minId . "   " . $maxId . " \n";
            $query->where("status", -1)->orderByDesc("time");
            $match = $query->first();
            if (!isset($match)) {
                echo $lenMsg." 不存在 $key \n";
                continue;
            }
            $msg = $match["hname"]." VS " .$match["aname"] . " " . $match["time"];
            $url = "http://cms.aikanqiu.com/live/cache/match/detail_id/".$match["id"]."/" . $sport . "/";
            echo $lenMsg . "  " . $msg . " 耗时： " . (time() - $startTime)  . "  $url  \n";
            Redis::setEx($key, 60*60, $msg);
            sleep(1);
        }
    }


    /**
     * 本机测试代码，检查文件链接是否成功
     */
    protected function check404() {
        $path = 'c:\Users\11247\Desktop\404_new.txt';
        $content = file_get_contents($path);
        $urls = explode("\n", $content);
        $txt = "";
        foreach ($urls as $url) {
            if (preg_match('/live\/ex-link/', $url)) {
                $txt .= $url;
            }
        }
        $outPath = 'c:\Users\11247\Desktop\404_ex.txt';
        file_put_contents($outPath, $txt);
    }

    /**
     * 获取url返回码
     * @param $url
     * @param int $timeout
     * @return mixed
     */
    public static function getUrlCode($url, $timeout = 5) {
        $isHttps = preg_match('/^https:/', $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//8秒超时

        // 返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
        curl_setopt($ch, CURLOPT_HEADER, true);
        // 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要
        //正文
        curl_setopt($ch, CURLOPT_NOBODY, true);
        if ($isHttps) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        return $code;
    }

    /**
     * 静态化专题所有比赛终端
     * @param $type
     */
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

    /**
     * 静态化PC比赛终端
     */
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
        $cache = [];
        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $sport = $match['sport'];
                $mid = $match['mid'];
                $hid = $match["hid"];
                $aid = $match["aid"];
                $time = isset($match['time']) ? $match['time'] : 0;

                $key = $hid > $aid ? ($sport . "_" . $hid . "_" . $aid) : ($sport . "_" . $aid . "_" . $hid);
                if (!isset($cache[$key])) {
                    $cache[$key] = 1;
                } else {
                    echo $match["hname"] . " vs " . $match["aname"] . $time . " 已静态化\n";
                }


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
                        echo $match["hname"] . " vs " . $match["aname"] . $time . $url . "\n";
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


    /**
     * 静态化M站比赛终端
     */
    protected function staticMLiveDetail() {
        $liveCon = new LiveController();
        $request = new Request();
        $intF = new AikanQController();
        $mJson = $intF->livesJsonData('', true);
        $json = $mJson['matches'];
        $cache = [];

        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $sport = $match['sport'];
                $mid = $match['mid'];
                $time = isset($match['time']) ? $match['time'] : 0;
                $hid = $match["hid"];
                $aid = $match["aid"];

                $key = $hid > $aid ? ($sport . "_" . $hid . "_" . $aid) : ($sport . "_" . $aid . "_" . $hid);
                if (!isset($cache[$key])) {
                    $cache[$key] = 1;
                } else {
                    echo $match["hname"] . " vs " . $match["aname"] . $time . " 已静态化\n";
                }

                if ($time == 0 ) {//只静态化赛前4小时内 的比赛终端。
                    continue;
                }

                $now = time();
                $start_time = strtotime($time);//比赛时间
                $flg_1 = true;//$start_time >= $now && $now + 45 * 60 >= $start_time;//开赛前1小时
                $flg_2 = false;//$start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时 开赛后编辑修改，会即时更新。
                if ( $flg_1 || $flg_2 ) {
                    try {
                        $url = env('CMS_URL').'/live/cache/match/detail_id/' . $mid . '/' . $sport . '/';
                        echo $match["hname"] . " vs " . $match["aname"] . $time . $url . "\n";
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

    public static function flushLiveDetailHtml($url, $timeout = 10) {
        $ch = curl_init();
        //$url = asset('/live/cache/match/detail_id/' . $match_id . '/' . $sport) . '?ch_id=' . $ch_id;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }

}