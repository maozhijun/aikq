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
use App\Http\Controllers\IntF\MatchController;
use App\Http\Controllers\IntF\SubjectVideoController;
use App\Http\Controllers\Mip\UrlCommonTool;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\Match;
use App\Models\Match\BasketMatch;
use App\Models\Match\Odd;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
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
        $json['canonical'] = UrlCommonTool::getMobileUrl();

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
     * @param $name_en
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function subjectVideoDetail(Request $request, $name_en, $id) {
        $svc = SubjectVideoChannels::query()->find($id);
        if (!isset($svc)) {
            return abort(404);
        }
        $sv = $svc->video;
        if (!isset($sv)) {
            return abort(404);
        }
        return $this->subjectVideoDetailHtml($svc, $sv);
    }

    /**
     * wap专题终端HTML
     * @param $svc
     * @param $sv
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectVideoDetailHtml(SubjectVideoChannels $svc, SubjectVideo $sv) {
        $allChannels = $sv->getAllChannels();
        $moreVideos = SubjectVideo::moreVideos($svc['id']);

        $json['canonical'] = UrlCommonTool::matchVideoUrl($sv['mid'], UrlCommonTool::getMobileUrl());
        $json['svc'] = $svc;
        $json['match'] = $sv;
        $json['allChannels'] = $allChannels;
        $json['moreVideos'] = $moreVideos;

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

        $match = $json['match'];
        $hid = $match['hid'];
        $aid = $match['aid'];
        $mid = $match['mid'];
        $hname = $match['hname'];
        $aname = $match['aname'];

        $passVSMatches = \App\Models\Match\Match::vsMatches($hid, $aid);//过往战绩
        $hNearMatches = \App\Models\Match\Match::nearMatches($hid);//主队近期战绩
        $aNearMatches = \App\Models\Match\Match::nearMatches($aid);//客队近期战绩

        $articles = PcArticle::liveRelationArticle([$hname, $aname], 14);//相关新闻
        $videos = SubjectVideo::relationVideosByTid($hid, $aid, $sport);//相关录像

        $lineup = MatchController::footballLineup($mid);//球队阵容
        $tech = MatchController::tech($sport, $mid);//足球事件
        $events = isset($tech['event']['events']) ? $tech['event']['events'] : [];

        $json['passVSMatches'] = $passVSMatches;
        $json['hNearMatches'] = $hNearMatches;
        $json['aNearMatches'] = $aNearMatches;

        $json['hasArticle'] = isset($articles) && count($articles) > 0;
        $json['hasVideo'] = isset($videos) && count($videos) > 0;
        $json['articles'] = $articles;
        $json['videos'] = $videos;

        $json['lineup'] = $lineup;
        $json['events'] = $events;
        $json['hasEvents'] = count($events) > 0;
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

        $match = $json['match'];
        $hid = $match['hid'];
        $aid = $match['aid'];
        $mid = $match['mid'];
        $hname = $match['hname'];
        $aname = $match['aname'];

        $passVSMatches = BasketMatch::vsMatches($hid, $aid);//过往战绩
        $hNearMatches = BasketMatch::nearMatches($hid);//主队近期战绩
        $aNearMatches = BasketMatch::nearMatches($aid);//客队近期战绩

        $articles = PcArticle::liveRelationArticle([$hname, $aname], 14);//相关新闻
        $videos = SubjectVideo::relationVideosByTid($hid, $aid, $sport);//相关录像

        $lineup = MatchController::basketballLineup($mid);//球队阵容
        $tech = MatchController::tech($sport, $mid);//足球事件
        $events = isset($tech['event']['events']) ? $tech['event']['events'] : [];

        $json['passVSMatches'] = $passVSMatches;
        $json['hNearMatches'] = $hNearMatches;
        $json['aNearMatches'] = $aNearMatches;

        $json['hasArticle'] = isset($articles) && count($articles) > 0;
        $json['hasVideo'] = isset($videos) && count($videos) > 0;
        $json['articles'] = $articles;
        $json['videos'] = $videos;

        $json['lineup'] = $lineup;
        $json['events'] = $events;
        $json['hasEvents'] = count($events) > 0;

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
        return view('mip.live.detail_other', $json);
    }
}