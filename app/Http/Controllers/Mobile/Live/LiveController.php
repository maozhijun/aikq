<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:40
 */

namespace App\Http\Controllers\Mobile\Live;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\Mobile\UrlCommonTool;
use App\Models\LgMatch\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\Odd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LiveController extends Controller
{
    /////////////////////////////////////  静态化列表 开始   /////////////////////////////////////
    /**
     * 静态化wap全部列表文件
     * @param Request $request
     */
    public function staticIndex(Request $request){
        $this->livesStatic($request);//静态化首页
    }

    /**
     * 静态化 wap 首页
     * @param Request $request
     */
    public function livesStatic(Request $request){
        $html = $this->lives($request);
        $this->onHtmlStatic($html, UrlCommonTool::MOBILE_STATIC_PATH."/index.html");
    }

    /**
     * 静态化wap篮球列表
     * @param Request $request
     */
    public function basketballLivesStatic(Request $request){
        $html = $this->basketballLives(new Request());
        try {
            if (!empty($html)) {
                Storage::disk("public")->put("/static/m/basketball.html",$html);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * 静态化wap足球列表
     * @param Request $request
     */
    public function footballLivesStatic(Request $request){
        $html = $this->footballLives(new Request());
        try {
            if (!empty($html)) {
                Storage::disk("public")->put("/static/m/football.html",$html);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * 静态化wap足球列表
     * @param Request $request
     */
    public function otherLivesStatic(Request $request){
        $html = $this->otherLives(new Request());
        try {
            if (!empty($html)) {
                Storage::disk("public")->put("/static/m/other.html",$html);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /////////////////////////////////////  静态化列表 结束   /////////////////////////////////////

    /////////////////////////////////////  wap列表 开始   /////////////////////////////////////
    /**
     * 足球列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function lives(Request $request) {
        $aiCon = new AikanQController();
        $json = $aiCon->livesJsonData('', true);
        if (is_null($json)) {
            return;
        }
        $json['type'] = 'live';
        return view('mobile.live.lives', $json);
    }

    /**
     * 足球列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function footballLives(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."aik/footballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $server_output = curl_exec ($ch);
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($http_code >= 400) {
            return;
        }
        $json = json_decode($server_output,true);
        if (is_null($json)) {
            return;
        }
        $json['type'] = 'football';
        return view('mobile.live.lives', $json);
    }

    /**
     * 篮球列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function basketballLives(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."aik/basketballLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $server_output = curl_exec ($ch);
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($http_code >= 400) {
            return;
        }
        $json = json_decode($server_output,true);
        if (is_null($json)) {
            return;
        }
        $json['type'] = 'basketball';
        return view('mobile.live.lives', $json);
    }

    /**
     * 篮球列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function otherLives(Request $request) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."aik/otherLivesJson?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $server_output = curl_exec ($ch);
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($http_code >= 400) {
            return;
        }
        $json = json_decode($server_output,true);
        if (is_null($json)) {
            return;
        }
        $json['type'] = 'other';
        return view('mobile.live.lives', $json);
    }

    /**
     * 热门录像 wap 列表
     * @param Request $request
     * @param $type
     * @param $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectVideos(Request $request, $type, $page) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."aik/subjects/league/video/page/" . $type . "?isMobile=1&page=" . $page;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $server_output = curl_exec ($ch);
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($http_code >= 400) {
            return abort(404);
        }
        $json = json_decode($server_output,true);
        if (is_null($json)) {
            return abort(404);
        }
        if (!isset($json['videos'])) {
            return abort(404);
        }
        return $this->subjectVideosHtml($json);
    }

    /**
     * 页面html信息
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectVideosHtml($data) {
        $json = $this->subjectVideoData2Json($data);

        return view('mobile.video.lives', $json);
    }

    /**
     * 转化json数据格式
     * @param $data
     * @return mixed
     */
    public function subjectVideoData2Json($data) {
        $videos = $data['videos'];
        $matches = [];
        foreach ($videos as $video) {
            $time = $video['time'];
            $day = date('Y-m-d', $time);
            $matches[$day][] = $video;
        }
        $json['matches'] = $matches;
        $json['page'] = $data['page'];
        $json['videos'] = [];
        $json['type'] = 'video';
        return $json;
    }

    /**
     * wap专题终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function subjectVideoDetail(Request $request, $first, $second, $id) {
        $ch = curl_init();
        //$url = env('LIAOGOU_URL')."aik/subjects/league/video/detail/" . $id . "?isMobile=1";
        $url = env('LIAOGOU_URL')."aik/subjects/video/" . $id . "?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $server_output = curl_exec ($ch);
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($http_code >= 400) {
            return;
        }
        $json = json_decode($server_output,true);
        if (is_null($json) || count($json) == 0) {
            return;
        }
        return $this->subjectVideoDetailHtml($json);
    }

    /**
     * wap专题终端HTML
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectVideoDetailHtml($data) {
        $json['match'] = $data;
        return view('mobile.video.detail', $json);
    }

    /////////////////////////////////////  wap列表 结束   /////////////////////////////////////

    /////////////////////////////////////  wap终端 开始   /////////////////////////////////////


    public function detail(Request $request, $param) {
        preg_match("/(\d)(\d+)/", $param, $matches);
        if (count($matches) != 3) {
            return abort(404);
        }
        $sport = $matches[1];
        $id = $matches[2];
        if ($sport == MatchLive::kSportFootball) {
            return $this->footballDetail($request, $id, true);
        } else if ($sport == MatchLive::kSportBasketball) {
            return $this->basketballDetail($request, $id, true);
        } else if ($sport == MatchLive::kSportSelfMatch) {
            return $this->otherDetail($request, $id);
        }
        return abort(404);
    }

    public function detailBySL(Request $request, $name_en, $param) {
        return $this->detail($request, $param);
    }

    /**
     * 足球直播终端
     * @param Request $request
     * @param $id
     * @param bool $immediate 是否即时获取数据
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function footballDetail(Request $request, $id, $immediate = false) {
        $akqCon = new AikanQController();
        $jsonStr = $akqCon->detailJson($request, $id, true)->getData();
        $jsonStr = json_encode($jsonStr);
        $json = json_decode($jsonStr, true);

        return $this->footballDetailHtml($json, $id);
    }

    public function footballDetailHtml($json, $id) {
        $colum = 'other';
        $sport = 1;
        if (array_key_exists($json['match']['lid'],Match::path_league_football_arrays)){
            $colum = Match::path_league_football_arrays[$json['match']['lid']];
        }
        $date = substr($id,0,2);
        if ($colum == 'other'){
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        else{
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        return view('mobile.live.detail', $json);
    }

    /**
     * 直播终端
     * @param Request $request
     * @param $id
     * @param bool $immediate 是否即时获取数据
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketballDetail(Request $request, $id, $immediate = false) {
        $intF = new AikanQController();
        $json = $intF->basketDetailJsonData($id, true);
        return $this->basketballDetailHtml($json, $id);
    }


    public function basketballDetailHtml($json, $id) {
        $colum = 'other';
        $sport = 2;
        if (array_key_exists($json['match']['lid'],Match::path_league_basketball_arrays)){
            $colum = Match::path_league_basketball_arrays[$json['match']['lid']];
        }
        $date = substr($id,0,2);
        if ($colum == 'other'){
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        else{
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        return view('mobile.live.detail', $json);
    }

    /**
     * 自建赛事直播终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function otherDetail(Request $request, $id) {
        $intF = new AikanQController();
        $json = $intF->otherDetailJsonData($id, true);

        return $this->otherDetailHtml($json, $id);
    }

    /**
     * @param $json
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function otherDetailHtml($json, $id) {
        $colum = 'other';
        $sport = 3;
        $date = substr($id,0,2);
        $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        return view('mobile.live.detail', $json);
    }

    /////////////////////////////////////  wap终端 结束   /////////////////////////////////////


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
     * 比赛状态数据
     * @param Request $request
     */
    public function matchLiveStatic(Request $request) {
        try {
            $json = $this->match_live($request, true);
            Storage::disk("public")->put("/static/m/lives/data/refresh.json", $json);
            Storage::disk("public")->put("m/lives/data/refresh.json", $json);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }


}