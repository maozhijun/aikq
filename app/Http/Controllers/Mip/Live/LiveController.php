<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:40
 */

namespace App\Http\Controllers\Mip\Live;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\Mip\UrlCommonTool;
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
        $this->onHtmlStatic($html, UrlCommonTool::MIP_STATIC_PATH."/index.html");
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
        $data = $aiCon->livesJson($request, true)->getData();
        $data = json_encode($data);
        $json = json_decode($data, true);
        if (is_null($json)) {
            return;
        }
        $json['type'] = 'live';

        $json = array_merge($this->html_var, $json);

        return view('mip.live.lives', $json);
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

        return view('mip.video.lives', $json);
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
        return view('mip.video.detail', $json);
    }

    /////////////////////////////////////  wap列表 结束   /////////////////////////////////////

    /////////////////////////////////////  wap终端 开始   /////////////////////////////////////
    /**
     * 直播终端
     * @param Request $request
     * @param $id
     * @param bool $immediate 是否即时获取数据
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function footballdetail(Request $request, $id, $immediate = false) {
//        $ch = curl_init();
//        if ($immediate) {
//            $url = env('LIAOGOU_URL')."aik/lives/detailJson/$id?isMobile=1";
//        } else{
//            $url = env('LIAOGOU_URL')."aik/lives/detailJson/mobile/$id" . '.json';
//        }
//        curl_setopt($ch, CURLOPT_URL,$url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_TIMEOUT,6);
//        $server_output = curl_exec ($ch);
//        curl_close ($ch);
//        $json = json_decode($server_output,true);
        $akqCon = new AikanQController();
        $jsonStr = $akqCon->detailJson($request, $id, true)->getData();
        $jsonStr = json_encode($jsonStr);
        $json = json_decode($jsonStr, true);
        return view('mip.live.detail', $json);
    }

    public function footballDetailHtml($json, $id) {
        return view('mip.live.detail', $json);
    }

    /**
     * 直播终端
     * @param Request $request
     * @param $id
     * @param bool $immediate 是否即时获取数据
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketballDetail(Request $request, $id, $immediate = false) {
        $ch = curl_init();
        if ($immediate) {
            $url = env('LIAOGOU_URL')."aik/lives/basketDetailJson/$id?isMobile=1";
        } else {
            $url = env('LIAOGOU_URL')."aik/lives/basketDetailJson/mobile/$id" . '.json';
        }
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,6);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('mip.live.detail', $json);
    }

    public function basketballDetailHtml($json, $id) {
        return view('mip.live.detail', $json);
    }

    /**
     * 自建赛事直播终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function otherDetail(Request $request, $id) {
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."aik/lives/otherDetailJson/$id?isMobile=1";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,6);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($server_output,true);
        return view('mip.live.detail', $json);
    }

    public function otherDetailHtml($json, $id) {
        return view('mip.live.detail', $json);
    }
}