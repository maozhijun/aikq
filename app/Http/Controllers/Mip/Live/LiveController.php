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
use App\Http\Controllers\IntF\SubjectVideoController;
use App\Http\Controllers\Mip\UrlCommonTool;
use App\Models\LgMatch\Match;
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
        $json['canonical'] = UrlCommonTool::homeLivesUrl(UrlCommonTool::getMobileUrl());

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
        $subCon = new SubjectVideoController();
        $json = $subCon->subjectVideos(new Request(), $type, $page, true)->getData();
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
        $json['canonical'] = UrlCommonTool::homeVideosUrl(UrlCommonTool::getMobileUrl());
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
        $aikCon = new AikanQController();
        $json = $aikCon->subjectVideo($id, true);
        if (is_null($json) || count($json) == 0) {
            return abort(404);
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
        $json['canonical'] = UrlCommonTool::matchVideoUrl($data['mid'], UrlCommonTool::getMobileUrl());
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
        $akqCon = new AikanQController();
        $jsonStr = $akqCon->detailJson($request, $id, true)->getData();
        $jsonStr = json_encode($jsonStr);
        $json = json_decode($jsonStr, true);

        return $this->footballDetailHtml($json, $id);
    }

    public function footballDetailHtml($json, $id) {
        $colum = 'other';
        $sport = 1;
        $lid = 0;
        if (array_key_exists($json['match']['lid'],Match::path_league_football_arrays)){
            $lid = $json['match']['lid'];
            $colum = Match::path_league_football_arrays[$lid];
        }
        $date = substr($id,0,2);
        if ($colum == 'other'){
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        else{
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        $json['canonical'] = UrlCommonTool::matchLiveUrl($lid, $sport, $id, UrlCommonTool::getMobileUrl());
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
        $intF = new AikanQController();
        $json = $intF->basketDetailJsonData($id, true);
        return $this->basketballDetailHtml($json, $id);
    }

    public function basketballDetailHtml($json, $id) {
        $colum = 'other';
        $sport = 2;
        $lid = 0;
        if (array_key_exists($json['match']['lid'],Match::path_league_basketball_arrays)){
            $lid = $json['match']['lid'];
            $colum = Match::path_league_basketball_arrays[$lid];
        }
        $date = substr($id,0,2);
        if ($colum == 'other'){
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }
        else{
            $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        }

        $json['canonical'] = UrlCommonTool::matchLiveUrl($lid, $sport, $id, UrlCommonTool::getMobileUrl());
        return view('mip.live.detail', $json);
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

    public function otherDetailHtml($json, $id) {
        $colum = 'other';
        $sport = 3;
        $date = substr($id,0,2);
        $json['detail_url'] = '/'.$colum.'/live'.$date.$sport. $id . '.html';
        $json['canonical'] = UrlCommonTool::matchLiveUrl(0, $sport, $id, UrlCommonTool::getMobileUrl());
        return view('mip.live.detail', $json);
    }
}