<?php

namespace App\Http\Controllers\PC;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Models\Article\PcArticle;
use App\Models\Match\HotVideo;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\Match\MatchLive;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function appConfig(Request $request){
        return response()->json(
            [
                'code'=>0,
                'data'=>[
                    'more'=>'https://shop.liaogou168.com',
                    'icon'=>'http://mp.dlfyb.com/img/pc/image_qr_868.jpg',
                    'weixin'=>'kanqiu8888',
                    'ios_version'=>'1.2.0',
                    'android_version'=>'1.2.0',
                    'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                    'anchor'=>1,
                ]
            ]
        );
    }

    public function appConfigV110(Request $request){
        $array = $this->appConfivV110p();
        return response()->json($array);
    }

    public function appConfigV120(Request $request){
        $array = $this->appConfivV120p();
        return response()->json($array);
    }

    public function appConfivV110p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'2.0.1',
                'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/index.html',
                'anchor'=>0,
                'showMore'=>'0',
            ]
        );
    }

    public function appConfivV120p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'2.0.1',
                'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/index.html',
                'android_install_url'=>'https://static.dlfyb.com/download/android.apk',
                'install_url'=>'itms-services://?action=download-manifest&url=https://static.dlfyb.com/download/test.plist',
                'anchor'=>0,
                'showMore'=>'0',
            ]
        );
    }

    public function appConfivV130p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'2.0.1',
                'update_url'=>'https://www.aikanqiu.com/download/index.html#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/index.html',
                'android_install_url'=>'https://static.dlfyb.com/download/android.apk',
                'install_url'=>'itms-services://?action=download-manifest&url=https://static.dlfyb.com/download/test.plist',
                'anchor'=>0,
                'showMore'=>'0',
                'stream_refers'=>['live.sjmhw.com'=>'https://www.aikanqiu.com'],//添加需要设置refer的播放地址
                'android_system_browser'=>0,//非直播的url跳转方式：0-app，1-系统浏览器
                'android_upgrade_msg'=>'最新版本v2.0.1，更换了播放器内核，看比赛声音更流畅',//非直播的url跳转方式：0-app，1-系统浏览器
            ]
        );
    }

    public function index(Request $request){
        $this->html_var['check'] = 'index';
        $comboData = CommonTool::getComboData();
        $this->html_var['articles'] = $comboData['articles'];
        $records = $comboData['records'];
        $this->html_var['videos'] = $comboData['videos'];

        //比赛
        $cache = Storage::get('/public/static/json/pc/lives.json');
        $json = json_decode($cache, true);
        $this->html_var['matches'] = array();
        $this->html_var['endMatches'] = array();
        if (is_null($json)){
            //return abort(404);
        }
        else{
            $tmp = array();
            $i = 0;
            foreach ($json['matches'] as $key =>$matches) {
                $tmp = array_merge($tmp,$matches);
                if ($i > 1){
                    break;
                }
                $i++;
            }
//            foreach ($records as $record){
//                $key = $record['match']['sport'].'_'.$record['match']['id'];
//                if (isset($tmp[$key])){
//                    $tmp[$key]['recordUrl'] = $record['link'];
//                }
//            }
            foreach ($tmp as $key=>$match){
                //有需要再把录像搞回来
                $this->html_var['matches'][] = $match;
                if (count($this->html_var['matches']) > 20)
                    break;
            }
        }

        //录像
        for ($i = 0 ; $i < min(10,count($records)) ;$i++){
            $record = $records[$i];
            $this->html_var['endMatches'][] = $record;
        }

        //积分
        $url = env('MATCH_URL')."/static/league/allScores.json";
        $score = HomeController::curlData($url,10);
        $this->html_var['scores'] = $score;
        $this->html_var['leagues'] = array(
            "1_46"=>["name_en"=>"zhongchao", "sid"=>1002, 'name'=>'中超'],
            "1_31"=>["name_en"=>"yingchao", "sid"=>1000, 'name'=>'英超'],
            "1_26"=>["name_en"=>"xijia", "sid"=>1003, 'name'=>'西甲'],
            "1_29"=>["name_en"=>"yijia", "sid"=>1004, 'name'=>'意甲'],
            "1_11"=>["name_en"=>"fajia", "sid"=>1005, 'name'=>'法甲']);
        return view('pc.index',$this->html_var);
    }
    
    public function staticIndex(Request $request){
        $html = $this->index(new Request());
        try {
            if (!empty($html)) {
                Storage::disk("public")->put("/www/index.html", $html);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    protected static function curlData($url,$timeout = 5){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }

    public function download(Request $request) {
        return view("pc.download");
    }

    /**
     * 邀请
     * @param Request $request
     * @param $code
     * @return $this
     */
    public function invitation(Request $request,$code){
        if (self::isMobile($request)){
            return response()->redirectTo('/m')->withCookie(cookie('LIAOGOU_INVITATION_CODE', $code));
        }
        else{
            return response()->redirectTo('/')->withCookie(cookie('LIAOGOU_INVITATION_CODE', $code));
        }
    }

    public function updateComboData(Request $request,$name_en = null){
        return HomeController::updateFileComboData($name_en);
    }

    public static function updateFileComboData($name_en){
        //录像
        $records = SubjectVideo::getRecordsByName($name_en);
        //资讯
        $articles = PcArticle::getLastArticle($name_en,20);

        //赛程
        $matches = array();
        try { //防止报file not found异常
            if ($name_en == "all") {
                $cache = Storage::get('/public/static/json/pc/lives.json');
                $json = json_decode($cache, true);
                if (isset($json)) {
                    $json = $json['matches'];
                }
            } else {
                $cache = Storage::get('/public/static/json/pc/lives/' . $name_en . '.json');
                $json = json_decode($cache, true);
            }
            if (isset($json) && count($json) > 0) {
                $matches = collect($json)->collapse()->where("status", ">=", 0)->take(10)->all();
            }
        } catch (\Exception $e) {

        }


        //视频
        $videos = HotVideo::getVideosByName($name_en);

        $result = array('records'=>$records,'articles'=>$articles, 'matches'=>$matches, "videos"=>$videos);

        //保存一次文件
        $appData = json_encode($result);
        if (is_null($name_en)){
            $name_en = 'all';
        }
        Storage::disk("public")->put("/static/json/pc/comboData/". $name_en . '.json', $appData);
        return $result;
    }

    /**
     * 生成所有赛事专题的 对阵url
     */
    public function genAllSubjectMatchVs() {
        $leagues = SubjectLeague::query()->where('status', SubjectLeague::kStatusShow)->where('type', 1)->get();

        $array = [];
        foreach ($leagues as $league) {
            $urls = $this->getSubjectTeamVsDetails($league['name_en'], $league['sport'], $league['lid']);
            $array = array_merge($array, $urls);
        }
        $str = implode("<br>", $array);
        return "$str";
    }
    /**
     * 专题球队比赛对阵
     */
    private function getSubjectTeamVsDetails($name_en, $sport, $lid) {
        if ($sport == MatchLive::kSportBasketball) {
            $season = BasketSeason::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $query = BasketScore::query();
        } else {
            $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $query = Score::query();
        }
        $urls = array();
        if (isset($season)) {
            $scores = $query->select('tid')->where('lid', $lid)->where('season', $season->name)->get()->unique('tid');

            foreach ($scores as $score) {
                $tid = $score['tid'];
                foreach ($scores as $innerScore) {
                    $vs_tid = $innerScore['tid'];
                    if ($vs_tid <= $tid) continue;

                    $matchVs = CommonTool::getMatchVsByTid($tid, $vs_tid);
                    $matchDetailUrl = "https://www.aikanqiu.com/".$name_en."/live".$sport.$matchVs.".html";
                    $urls[] = $matchDetailUrl;
                }
            }
        }
        return $urls;
    }
}
