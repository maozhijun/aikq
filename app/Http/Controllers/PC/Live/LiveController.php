<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:40
 */

namespace App\Http\Controllers\PC\Live;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class LiveController extends Controller
{
    const BET_MATCH = 1;

    /**
     * 首页（首页、竞彩、足球、篮球）缓存
     * @param Request $request
     */
    public function staticIndex(Request $request){
        $this->basketballLivesStatic($request);
        $this->footballLivesStatic($request);
        $this->livesStatic($request);
        $this->betLivesStatic($request);
    }

    /**
     * 竞彩缓存
     * @param Request $request
     */
    public function betLivesStatic(Request $request){
        $html = $this->betLives(new Request());
        try {
            Storage::disk("public")->put("/static/betting.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * 首页缓存
     * @param Request $request
     */
    public function livesStatic(Request $request){
        $html = $this->lives(new Request());
        try {
            Storage::disk("public")->put("/static/index.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * 篮球缓存
     * @param Request $request
     */
    public function basketballLivesStatic(Request $request){
        $html = $this->basketballLives(new Request());
        try {
            Storage::disk("public")->put("/static/basketball.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * 足球缓存
     * 足球缓存
     * @param Request $request
     */
    public function footballLivesStatic(Request $request){
        $html = $this->footballLives(new Request());
        try {
            Storage::disk("public")->put("/static/football.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * PC直播赛事的json
     * @param Request $request
     */
    public function allLiveJsonStatic(Request $request) {
        $this->liveJson();//首页赛事缓存
        $this->liveJson(self::BET_MATCH);//首页竞彩赛事缓存
        $this->footballLiveJson();//首页足球赛事缓存
        $this->basketballLiveJson();//首页篮球赛事缓存
    }

    /**
     * 首页、竞彩赛事
     * @param $bet
     * @return mixed
     */
    protected function liveJson($bet = '') {
        try {
            $ch = curl_init();
            $url = env('LIAOGOU_URL')."/livesJson?bet=" . $bet;
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            if ($bet == self::BET_MATCH) {
                Storage::disk("public")->put("/static/json/bet-lives.json", $server_output);
            } else{
                Storage::disk("public")->put("/static/json/lives.json", $server_output);
            }

        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }

    /**
     * 篮球赛事json缓存
     */
    protected function basketballLiveJson() {
        try {
            $ch = curl_init();
            $url = env('LIAOGOU_URL')."/basketballLivesJson";
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            Storage::disk("public")->put("/static/json/basketball-lives.json", $server_output);
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }

    /**
     * 足球赛事json缓存
     */
    protected function footballLiveJson() {
        try {
            $ch = curl_init();
            $url = env('LIAOGOU_URL')."/footballLivesJson";
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            Storage::disk("public")->put("/static/json/football-lives.json", $server_output);
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
    //===============================================================================//

    /**
     * 播放失败
     * @param Request $request
     * @return json
     */
    public function liveError(Request $request){
        $cid = $request->input('cid',0);
        if ($cid <= 0){
            return;
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/livesError?cid=" . $cid;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        dump($url);
        return $json;
    }

    /**
     * 首页直播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function lives(Request $request) {
        //$json = $this->getLives();
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json)){
            //return abort(404);
        }
        $json['week_array'] = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
        $json['check'] = 'all';
        return view('pc.home', $json);
    }

    /**
     * 竞彩直播比赛列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function betLives(Request $request) {
        //$json = $this->getLives(self::BET_MATCH);
        $cache = Storage::get('/public/static/json/bet-lives.json');
        $json = json_decode($cache, true);
        if (is_null($json)){
            return abort(404);
        }
        $json['check'] = 'bet';
        $json['week_array'] = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
        return view('pc.home', $json);
    }

    /**
     * 获取有直播的比赛。
     * @param string $bet
     * @return mixed
     */
    protected function getLives($bet = '') {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/livesJson?bet=" . $bet;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //$code = curl_getinfo($ch, CURLE_RECV_ERROR);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return $json;//$this->toNewMatchArray($json);
    }

    /**
     * 拼凑新的返回对象
     * @param $json
     * @return mixed
     */
    protected function toNewMatchArray($json) {
        if (is_null($json)) return null;

        $start_matches = [];//比赛中的赛事
        $wait_matches = [];//稍后的比赛

        $matches = $json['matches'];
        foreach ($matches as $time=>$match_array) {
            $w_match = [];
            foreach ($match_array as $index=>$match) {
                if ($match['isMatching']) {
                    $start_matches[] = $match;
                } else {
                    $w_match[] = $match;
                }
            }
            if (count($w_match) > 0) {
                $wait_matches[$time] = $w_match;
            }
        }

        $result['play_matches'] = $start_matches;
        $result['matches'] = $wait_matches;
        return $result;
    }

    /**
     * 首页直播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function basketballLives(Request $request) {
//        $ch = curl_init();
//        $url = env('LIAOGOU_URL')."/basketballLivesJson";
//        curl_setopt($ch, CURLOPT_URL,$url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $server_output = curl_exec ($ch);
//        curl_close ($ch);
//        $json = json_decode($server_output,true);
        $cache = Storage::get('/public/static/json/basketball-lives.json');
        $json = json_decode($cache, true);
        if (is_null($json)){
            return abort(404);
        }
        $json['check'] = 'basket';
        $json['week_array'] = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
        return view('pc.home', $json);
    }

    /**
     * 首页直播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function footballLives(Request $request) {
//        $ch = curl_init();
//        $url = env('LIAOGOU_URL')."/footballLivesJson";
//        curl_setopt($ch, CURLOPT_URL,$url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $server_output = curl_exec ($ch);
//        curl_close ($ch);
//        $json = json_decode($server_output,true);
        $cache = Storage::get('/public/static/json/basketball-lives.json');
        $json = json_decode($cache, true);
        if (is_null($json)){
            return abort(404);
        }
        $json['check'] = 'foot';
        $json['week_array'] = array('星期日','星期一','星期二','星期三','星期四','星期五','星期六');
        return view('pc.home', $json);
    }

    /**
     * 直播终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $id) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/detailJson/$id";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('pc.live.video', $json);
    }

    /**
     * 篮球直播终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketDetail(Request $request, $id) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/basketDetailJson/$id";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('pc.live.video', $json);
    }

    /**
     * 播放器channel
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function matchPlayerChannel(Request $request){
        return view('pc.live.match_channel');
    }

    /**
     * 播放器
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function matchPlayer(Request $request){
        return view('pc.live.match_player');
    }

    /**
     * 播放器
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function player(Request $request){
        return view('pc.live.player');
    }

    public function share(Request $request){
        return view('pc.live.player');
    }

    /**
     * 获取天天直播源js
     * @param Request $request
     * @param $mid
     * @return mixed
     */
    public function getTTZBLiveUrl(Request $request,$mid){
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/match/live/url/channel/$mid".'?sport='.$request->input('sport',1);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
    }

    /**
     * 获取无插件playurl
     * @param Request $request
     * @param $mid
     * @return mixed
     */
    public function getWCJLiveUrl(Request $request,$mid){
        $ch = curl_init();
        $isMobile = \App\Http\Controllers\Controller::isMobile($request)?1:0;
        $url = env('LIAOGOU_URL')."/match/live/url/channel/$mid".'?isMobile='.$isMobile.'&sport='.$request->input('sport',1);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
    }

    /**
     * 获取playurl根据比赛id
     * @param Request $request
     * @param $mid
     * @return mixed
     */
    public function getLiveUrlMatch(Request $request,$mid){
        $ch = curl_init();
        $isMobile = \App\Http\Controllers\Controller::isMobile($request)?1:0;
        $url = env('LIAOGOU_URL')."/match/live/url/match/$mid".'?isMobile='.$isMobile.'&sport='.$request->input('sport',1);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
    }

    /**
     * 获取无插件playurl
     * @param Request $request
     * @param $mid
     * @return mixed
     */
    public function getLiveUrl(Request $request,$mid){
        $ch = curl_init();
        $isMobile = \App\Http\Controllers\Controller::isMobile($request)?1:0;
        $url = env('LIAOGOU_URL')."/match/live/url/channel/$mid".'?isMobile='.$isMobile.'&sport='.$request->input('sport',1);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
    }

    /**
     * 正在直播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLiveMatches(Request $request) {
        $mid = $request->input('mid');//当前页面直播中的id 格式应该是 id1-id2...-idn
        $ch = curl_init();
        $sport = $request->input('sport',0);
        $url = env('LIAOGOU_URL')."/lives/liveMatchesJson?sport=".$sport."&mid=".$mid;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
//        dump($json);
        $json['showRadio'] = $request->input('showRadio',1);
        return view('pc.live.living_list', $json);
    }

    /**
     * 多视频页面
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiLive(Request $request, $param) {
        if (!preg_match('/^\d+(-\d+){0,3}$/', $param)) {
//            return abort(404);
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/multiLiveJson/".$param;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
//        dump($json);
        return view("pc.live.multiScreen",$json);
    }

    /**
     * 多视频页面Div
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiLiveDiv(Request $request, $param) {
        if (!preg_match('/^\d+(-\d+){0,3}$/', $param)) {
//            return abort(404);
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/multiLiveDivJson/f".$param;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view("pc.live.multiScreenItem",$json);
    }
    /**
     * 多视频页面Div
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiBasketLiveDiv(Request $request, $param) {
        if (!preg_match('/^\d+(-\d+){0,3}$/', $param)) {
//            return abort(404);
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/multiLiveDivJson/b".$param;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
//        dump($json);
//        dump($url);
        return view("pc.live.multiScreenItem",$json);
    }

    /**
     * 多视频页面
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiBasketLive(Request $request, $param) {
        if (!preg_match('/^\d+(-\d+){0,3}$/', $param)) {
            return abort(404);
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/multiBasketLiveJson/".$param;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
//        dump($json);
//        dump($url);
        return view("pc.live.multiScreen",$json);
    }

    /**
     * 足球赛事对应推荐
     * @param Request $request
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getArticleOfFMid(Request $request,$mid){
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/football/recommend/$mid";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('pc.live.detail_recommend', $json);
    }

    /**
     * 篮球赛事对应推荐
     * @param Request $request
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getArticleOfBMid(Request $request,$mid){
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/basketball/recommend/$mid";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('pc.live.detail_recommend', $json);
    }

    /**
     * 清除底部推荐缓存
     * @param Request $request
     * @param $mid
     */
    public function deleteStaticHtml(Request $request,$mid){
        $sport = $request->input('sport',1);
        try {
            Storage::disk("public")->delete("/player/recommends/live/".($sport == 1 ? 'football':'basketball')."/recommend/$mid.html");
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param Request $request
     */
    public function staticHtml(Request $request){
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/livesJson?bet=" . 0;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        $json = $json['matches'];
        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $mid = $match['mid'];
                if ($match['sport'] == 1)
                    $html = $this->getArticleOfFMid(new Request(),$mid);
                else
                    $html = $this->getArticleOfBMid(new Request(),$mid);
                try {
                    Storage::disk("public")->put("/live/".($match['sport'] == 1 ? 'football':'basketball')."/recommend/$mid.html", $html);
                    //Storage::disk("public")->put("/player/recommends/live/".($match['sport'] == 1 ? 'football':'basketball')."/recommend/$mid.html", $html);
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                }
            }
        }
    }

    /**
     * 静态化直播终端页
     * @param Request $request
     */
    public function staticLiveDetail(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/livesJson?bet=" . 0;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        $json = $json['matches'];
        foreach ($json as $index=>$datas){
            foreach ($datas as $match){
                $mid = $match['mid'];
                try {
                    if ($match['sport'] == 1) {
                        $html = $this->detail($request, $mid);
                        Storage::disk("public")->put("/live/football/". $mid. ".html", $html);
                    } else {
                        $html = $this->basketDetail($request, $mid);
                        Storage::disk("public")->put("/live/basketball/". $mid. ".html", $html);
                    }
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                }
            }
        }
    }

    /**
     * 静态化播放页面异步请求
     * @param Request $request
     */
    public function staticPlayerJson(Request $request) {
        $json = $this->getLives();
        if (!isset($json) || !isset($json['play_matches'])) {
            return;
        }
        $play_matches = $json['play_matches'];
        foreach ($play_matches as $match) {
            if (!isset($match)) {
                return;
            }
            if (isset($match['channels'])) {
                $channels = $match['channels'];
                foreach ($channels as $channel) {
                    $ch_id = $channel['id'];
                    $this->staticLiveUrl($request, $ch_id);
                }
            }
        }
    }

    /**
     * 静态化直播线路的json
     * @param Request $request
     * @param $id
     */
    public function staticLiveUrl(Request $request, $id) {
        try {
            $json = $this->getLiveUrl($request, $id);
            if (!empty($json)) {
                Storage::disk("public")->put("/match/live/url/channel/". $id . '.json', $json);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * 刷新缓存
     * @param Request $request
     */
    public function flushVideoCache(Request $request) {
        $mid = $request->input('mid');//比赛ID
        $sport = $request->input('sport');//竞技类型
        $ch_id = $request->input('ch_id');//线路id
        try {
            if(!empty($sport) && in_array($sport, [1, 2]) && is_numeric($mid)) {
                if ($sport == 1) {
                    $html = $this->detail($request, $mid);
                    Storage::disk("public")->put("/live/football/". $mid. ".html", $html);
                } else {
                    $html = $this->basketDetail($request, $mid);
                    Storage::disk("public")->put("/live/basketball/". $mid. ".html", $html);
                }
                $mController = new \App\Http\Controllers\Mobile\Live\LiveController();
                $mController->liveDetailStatic($request, $mid, $sport);//刷新移动直播终端。
            }
            if (is_numeric($ch_id)) {
                $this->staticLiveUrl($request, $ch_id);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public static function links() {
        $links = [];
        $key = 'base_link_cache';
        $server_output = Redis::get($key);

        if (empty($server_output)) {
            try {
                $ch = curl_init();
                $url = env('LIAOGOU_URL')."/json/link.json";
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close ($ch);
                if ($http_code >= 400) {
                    $server_output = "";
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
            if (empty($server_output)) {
                return $links;
            }
            Redis::setEx($key, 60 * 10, $server_output);
        }

        if (empty($server_output)) {
            return $links;
        }
        $json = json_decode($server_output);
        if (is_array($json)) {
            foreach ($json as $j) {
                $links[] = ['name'=>$j->name, 'url'=>$j->url];
            }
        }
        return $links;
    }

}