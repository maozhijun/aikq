<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:40
 */

namespace App\Http\Controllers\Mobile\Live;

use App\Models\Match\Odd;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LiveController extends Controller
{
    public function staticIndex(Request $request){
        $this->basketballLivesStatic($request);
        $this->footballLivesStatic($request);
        $this->livesStatic($request);
    }

    public function livesStatic(Request $request){
        $html = $this->lives(new Request());
        try {
            Storage::disk("public")->put("/static/m/lives.html",$html);
            Storage::disk("public")->put("/static/m/index.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function basketballLivesStatic(Request $request){
        $html = $this->basketballLives(new Request());
        try {
            Storage::disk("public")->put("/static/m/basketball.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function footballLivesStatic(Request $request){
        $html = $this->footballLives(new Request());
        try {
            Storage::disk("public")->put("/static/m/football.html",$html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function lives(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/footballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        $json['type'] = 'football';
        return view('mobile.live.lives', $json);
    }

    public function footballLives(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/footballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        $json['type'] = 'football';
        return view('mobile.live.lives', $json);
    }

    public function basketballLives(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/basketballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        $json['type'] = 'basketball';
        return view('mobile.live.lives', $json);
    }

    /**
     * 直播终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function footballdetail(Request $request, $id) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/detailJson/$id?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('mobile.live.detail', $json);
    }

    /**
     * 直播终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketballDetail(Request $request, $id) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/basketDetailJson/$id?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('mobile.live.detail', $json);
    }

    /**
     * 获取滚球赔率
     * @param Request $request
     * @param $time 格式：20171207
     * @param $id   比赛id
     * @param $isJson
     * @return \Illuminate\Http\JsonResponse
     */
    public function roll(Request $request, $time, $id, $isJson = false) {
        $url = 'http://match.liaogou168.com/roll/' . $time . '/' . $id . '.json?time=' . time();
        $ch = curl_init();
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $lines_string = curl_exec($ch);
        curl_close($ch);
        if (empty($lines_string)) {
            if ($isJson) {
                return null;
            }
            return response()->json(["code"=>403, "msg"=>"数据为空"]);
        }
        $json = json_decode($lines_string);
        $asia = 1;
        if ($json && $json->all->$asia) {
            $json->all->$asia->middle = Odd::panKouText($json->all->$asia->middle);
        }
        if ($json && $json->half->$asia) {
            $json->half->$asia->middle = Odd::panKouText($json->half->$asia->middle);
        }
        if ($isJson) {
            return $json;
        }
        return response()->json($json);
    }

    /**
     * event
    type：
    1 进球,
    2 红牌,
    3 黄牌,
    7 点球,
    9 两黄一红,
    11 换人

    statistic
    type:
    3 射门,
    4 射正,
    5 犯规,
    6 角球,
    9 越位,
    11 黄牌,
    13 红牌,
    14 控球率,
    15 头球,
    16 救球,
    34 射门不中,
    35 中柱,
    36 头球成功,
    37 射门被档,
    38 铲球,
    39 过人,
    40 界外球,
    41 传球,
    42 传球成功率,
    43 进攻,
    44 危险进攻
     * 获取比赛数据
     * @param Request $request
     * @param $time  格式：20171207
     * @param $id    赛事id
     * @param $returnHtml
     * @return \Illuminate\Http\JsonResponse
     */
    public function match_data(Request $request, $time, $id, $returnHtml = false) {
        $url = 'http://match.liaogou168.com/live-event/' . $time . '/' . $id . '.json?time=' . time();
        $ch = curl_init();
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $lines_string = curl_exec($ch);
        curl_close($ch);
        if ($returnHtml) {
            return $lines_string;
        }
        if (empty($lines_string)) {
            return response()->json(["code"=>403, "msg"=>"数据为空"]);
        }
        $json = json_decode($lines_string);
        if (!isset($json->event) && !isset($json->statistic)) {
            return response()->json(["code"=>403, "msg"=>"数据为空"]);
        }
        return response()->json($json);
    }

    /**
     * 比赛数据
     * @param Request $request
     * @param $isHtml
     * @return \Illuminate\Http\JsonResponse
     */
    public function match_live(Request $request, $isHtml = false) {
        $url = 'http://www.liaogou168.com/change/live.json?time=' . time();
        $ch = curl_init();
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $lines_string = curl_exec($ch);
        curl_close($ch);
        if ($isHtml) {
            return $lines_string;
        }
        if (empty($lines_string)) {
            return response()->json(["code"=>403, "msg"=>"数据为空"]);
        }
        $json = json_decode($lines_string);
        return response()->json($json);
    }

    /**
     * @param Request $request
     * @param $time    比赛时间 格式：20170102
     * @param $id      比赛ID
     * @param $isJson
     * @return \Illuminate\Http\JsonResponse
     */
    public function matchTip(Request $request, $time, $id, $isJson = false) {
        $url = 'http://match.liaogou168.com/live-analyse/' . $time . '/' . $id . '.json?time=' . time();
        $ch = curl_init();
        $timeout = 2;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $lines_string = curl_exec($ch);
        curl_close($ch);
        if (empty($lines_string)) {
            return response()->json(["code"=>403, "msg"=>"数据为空"]);
        }
        $json = json_decode($lines_string);

        //得出裁判结论 开始
        if (isset($json->referee)) {
            $referee = $json->referee;
            //黄牌预测 开始
            if (isset($referee->yellow)) {
                $yellow = $referee->yellow;
                $ref_yellow_avg = $yellow->ref_yellow_avg;
                $h_yellow_avg = $yellow->h_yellow_avg;
                $a_yellow_avg = $yellow->a_yellow_avg;
                if ($ref_yellow_avg > 5 && $h_yellow_avg > 5 && $a_yellow_avg > 5) {
                    $result = "本场主裁判【" . $yellow->referee . "】执法尺度偏严，【" . $yellow->h_name . "】/【" . $yellow->a_name . "】/两队 踢法比较粗暴。综合分析，本场比赛黄牌数可能会比较多。";
                    $referee->yellow->result = $result;
                } else if ($ref_yellow_avg < 4 && $h_yellow_avg < 4 && $a_yellow_avg < 4) {
                    $result = "本场主裁判【" . $yellow->referee . "】执法尺度偏松，【" . $yellow->h_name . "】/【" . $yellow->a_name . "】/两队 踢法比较温和。综合分析，本场比赛黄牌数可能会比较少。";
                    $referee->yellow->result = $result;
                }
            }
            //黄牌预测 结束
            //胜平负预计 开始
            if (isset($referee->wdl)) {
                $wdl = $referee->wdl;
                $ref_h_good = $wdl->win_p > 50 || $wdl->lose_p < 30;//裁判利好主队
                $ref_a_good = $wdl->lose_p > 50 || $wdl->win_p < 30;//裁判利好客队

                $h_win_p = $wdl->h_win_count / ($wdl->h_win_count + $wdl->a_win_count);
                $a_win_p = $wdl->a_win_count / ($wdl->h_win_count + $wdl->a_win_count);

                $h_good = $h_win_p > 60;//利好主队
                $h_bad = $h_win_p < 40;//利空主队
                $a_good = $a_win_p > 60;//利好客队
                $a_bad = $a_win_p < 40;//利空客队

                if ($ref_h_good && ($h_good || $a_bad) ) {
                    $result = "纵观本场主裁判【" . $wdl->referee . "】近期的执法数据，该裁判比较偏向主队。";
                    if ($h_good) {
                        $result .= "而他过去执法【" . $wdl->h_name . "】的比赛，";
                        $result .= "【" . $wdl->h_name . "】胜率超过60%。";
                    } else {
                        $result .= "而他过去执法【" . $wdl->a_name . "】的比赛，";
                        $result .= "【" . $wdl->a_name . "】胜率低于40%。";
                    }
                    $result .= "综合分析，本场比赛主队可能会得到主裁判更多“照顾”。";
                    $referee->wdl->result = $result;
                } else if ($ref_a_good && ($a_good || $h_bad) ) {
                    $result = "纵观本场主裁判【" . $wdl->referee . "】近期的执法数据，该裁判比较偏向客队。";
                    if ($a_good) {
                        $result .= "而他过去执法【" . $wdl->a_name . "】的比赛，";
                        $result .= "【" . $wdl->a_name . "】胜率超过60%。";
                    } else {
                        $result .= "而他过去执法【" . $wdl->h_name . "】的比赛，";
                        $result .= "【" . $wdl->h_name . "】胜率低于40%。";
                    }
                    $result .= "综合分析，本场比赛客队可能会得到主裁判更多“照顾”。";
                    $referee->wdl->result = $result;
                }
            }
            //胜平负预计 结束
        }
        //得出裁判结论 结束
        if ($isJson) {
            return $json;
        }
        return response()->json($json);
    }

    /**
     * 获取天天直播源js
     * @param Request $request
     * @param $mid
     * @return mixed
     */
    public function getTTZBLiveUrl(Request $request,$mid){
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
     * 获取天天直播源js
     * @param Request $request
     * @param $mid
     * @return mixed
     */
    public function getLiveUrl(Request $request,$mid){
        $ch = curl_init();
        $isMobile = \App\Http\Controllers\Controller::isMobile($request)?1:0;
        $isMobile = 1;
        $url = env('LIAOGOU_URL')."/match/live/url/channel/$mid".'?isMobile='.$isMobile.'&sport='.$request->input('sport',1);
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
     * 足球赛事对应推荐
     * @param Request $request
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getArticleOfFMid(Request $request,$mid){
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/football/recommend/$mid?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('mobile.live.detail_recommend', $json);
    }

    /**
     * 篮球赛事对应推荐
     * @param Request $request
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getArticleOfBMid(Request $request,$mid){
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/lives/basketball/recommend/$mid?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('mobile.live.detail_recommend', $json);
    }

    /**
     * 获取今天的足球比赛
     * @return mixed 无比赛则返回 空数组
     */
    protected function getFootballMatches() {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/footballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($code >= 400 || empty($server_output)) {
            return [];
        }
        $json = json_decode($server_output,true);
        return $json;
    }

    /**
     * 获取今天的足球比赛
     * @return mixed 无比赛则返回 空数组
     */
    protected function getBasketballMatches() {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/basketballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($code >= 400 || empty($server_output)) {
            return [];
        }
        $json = json_decode($server_output,true);
        return $json;
    }

    /**
     * 生成当前直播的所有比赛直播终端静态
     * @param Request $request
     */
    public function liveDetailsStatic(Request $request) {
        //获取今天所有足球赛事
        $json = $this->getFootballMatches();
        if (isset($json['matches'])) {
            $matches = $json['matches'];
            foreach ($matches as $time=>$match_array) {
                foreach ($match_array as $match) {
                    $html = $this->footballdetail($request, $match['mid']);
                    Storage::disk("public")->put("/static/m/live/football/" . $match['mid'] . ".html", $html);
                }
            }
        }

        //获取今天所有篮球赛事
        $json = $this->getBasketballMatches();
        if (isset($json['matches'])) {
            $matches = $json['matches'];
            foreach ($matches as $time=>$match_array) {
                foreach ($match_array as $match) {
                    $html = $this->basketballDetail($request, $match['mid']);
                    Storage::disk("public")->put("/static/m/live/basketball/" . $match['mid'] . ".html", $html);
                }
            }
        }
    }

    /**
     * 单个移动直播终端静态化
     * @param Request $request
     * @param $mid
     * @param $sport
     */
    public function liveDetailStatic(Request $request, $mid, $sport) {
        if (is_numeric($mid) && is_numeric($sport) && in_array($sport, [1, 2])) {
            if ($sport == 1) {
                $html = $this->footballdetail($request, $mid);
                Storage::disk("public")->put("/static/m/live/football/" . $mid . ".html", $html);
            } else {
                $html = $this->basketballDetail($request, $mid);
                Storage::disk("public")->put("/static/m/live/basketball/" . $mid . ".html", $html);
            }
        }
    }

    /**
     * 滚球静态 足球
     * @param Request $request
     */
    public function rollStatic(Request $request) {
        $json = $this->roll($request, '20180123', '1020644', true);
        if (isset($json)) {
            Storage::disk("public")->put("/static/m/lives/roll/20180123/1020644.html", json_encode($json));
        }

        $json = $this->getFootballMatches();
        if (isset($json['matches'])) {
            $matches = $json['matches'];
            foreach ($matches as $time=>$match_array) {
                foreach ($match_array as $match) {
                    $mid = $match['mid'];
                    $date = date('Ymd', strtotime($match['time']));
                    $json = $this->roll($request, $date, $mid, true);
                    if (isset($json)) {
                        Storage::disk("public")->put("/static/m/lives/roll/" . $date . "/" . $match['mid'] . ".html", json_encode($json));
                    }
                }
            }
        }
    }

    /**
     * 比赛数据(控球、射门等)
     * @param Request $request
     */
    public function matchDataStatic(Request $request) {
        $json = $this->getFootballMatches();
        if (isset($json['matches'])) {
            $matches = $json['matches'];
            foreach ($matches as $time=>$match_array) {
                foreach ($match_array as $match) {
                    $mid = $match['mid'];
                    $date = date('Ymd', strtotime($match['time']));
                    $html = $this->match_data($request, $date, $mid, true);
                    Storage::disk("public")->put("/static/m/data/" . $date . "/" . $match['mid'] . ".html", $html);
                }
            }
        }
    }

    /**
     * 比赛状态数据
     * @param Request $request
     */
    public function matchLiveStatic(Request $request) {
        try {
            $json = $this->match_live($request, true);
            Storage::disk("public")->put("/static/m/lives/data/refresh.json", $json);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * 足球比赛提点
     * @param Request $request
     */
    public function matchTipStatic(Request $request) {
        $json = $this->getFootballMatches();
        if (isset($json['matches'])) {
            $matches = $json['matches'];
            foreach ($matches as $time=>$match_array) {
                foreach ($match_array as $match) {
                    $mid = $match['mid'];
                    $date = date('Ymd', strtotime($match['time']));
                    $html = $this->matchTip($request, $date, $mid, true);
                    Storage::disk("public")->put("/static/m/lives/tip/" . $date . "/" . $match['mid'] . ".html", json_encode($html));
                }
            }
        }
    }

}