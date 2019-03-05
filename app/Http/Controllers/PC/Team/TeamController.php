<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/25
 * Time: 10:57
 */

namespace App\Http\Controllers\PC\Team;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\StaticController;
use App\Models\Article\PcArticle;
use App\Models\Match\HotVideo;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function rank(Request $request, $sport, $lid) {
        $rankData = AikanQController::leagueRankData($sport, $lid);
        $leagueData = AikanQController::getLeagueDataByLid($sport, $lid);
        return view('pc.team.detail_rank_cell', ['ranks'=>$rankData, 'subject'=>$leagueData]);
    }

    public function detail(Request $request, $name_en, $tid)
    {
        $lid = "";
        if ($name_en != 'other') {
            $subjectLeague = SubjectLeague::getSubjectLeagueByEn($name_en);
            if (isset($subjectLeague)) {
                $lid = $subjectLeague->lid;
            }
        }
        $sport = substr($tid, 0, 1);
        $teamId = substr($tid, 1);

        $data = $this->detailDetail($sport, $lid, $teamId,$name_en,$tid);
        return self::detailHtml($data);
    }

    public function detailDetail($sport, $lid, $teamId,$name_en,$tid){
        $data = AikanQController::teamDetailData($sport, $lid, $teamId);
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $tmp = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        if (isset($tmp)) {
            $tmp['name_en'] = $name_en;
        }
        $data['zhuanti'] = $tmp;
        $data['tid'] = explode('_',$tid)[0];
        return $data;
    }

    private static function detailHtml($data)
    {
        if ($data == null) return abort(404);
        return view('pc.team.v2.index', $data);
    }

    public static function detailStatic($name_en, $tid, $sport)
    {
//        $html = self::detailHtml($data);
//
//        Storage::disk('public')->put("www/$path", $html);
//        echo "pc: $path </br>";
        $path = CommonTool::getTeamDetailPathWithType($sport, $name_en, $tid,'index',1);

        //pc站综合页
        $con = new TeamController();
        $html = $con->detail(new Request(), $name_en, $tid);
        if (isset($html) && strlen($html) > 0){
            Storage::disk('public')->put("www/$path", $html);
        }
    }

    /***** 录像 *****/
    public function recordDetail(Request $request, $name_en, $tid, $page)
    {
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        if (isset($data)) {
            $data['name_en'] = $name_en;
            $this->html_var['zhuanti'] = $data;
        }
        $this->html_var['tid'] = $tid;
        $lid = $data['lid'];
        $sport = substr($tid, 0, 1);
        $tid = substr($tid, 1);

        $rdata = TeamController::recordData($name_en,$sport, $tid, $lid);
        $this->html_var['team'] = $rdata['team'];
        $this->html_var['title'] = $rdata['title'];
        $this->html_var['league'] = $rdata['league'];
        $this->html_var['articles'] = CommonTool::getComboData($name_en)['articles'];
        $this->html_var['name_en'] = $name_en;

        //录像
        $records = TagRelation::getRelationsPageByTagId(TagRelation::kTypePlayBack,$sport,3,$tid,$page,20);

        $this->html_var['records'] = $records;

        $this->html_var['page'] = $records->lastPage();
        $this->html_var['pageNo'] = $page;
        return view('pc.team.v2.record', $this->html_var);
    }

    /**
     * 静态化调用
     * @param Request $request
     * @param $sport
     * @param $name_en
     * @param $tid
     * @param $page
     */
    public function staticRecordHtml(Request $request, $sport, $name_en, $tid, $page)
    {
        $path = CommonTool::getTeamDetailPathWithType($sport,$name_en,$tid,'record',$page);
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        $tempTid = $sport.$tempTid;
        $html = $this->recordDetail(new Request(),$name_en,$tempTid,$page);
        if (isset($html) && strlen($html) > 0){
            Storage::disk('public')->put("www/$path", $html);
        }
        else{
            echo 'TeamController staticRecordHtml ' . $name_en . ' ' . $tid .' ' .$page;
        }
        echo 'success';
    }

    /**
     * 获取数据
     * @param $name_en
     * @param $sport
     * @param $tid
     * @param $lid
     * @return array|null
     */
    public static function recordData($name_en,$sport, $tid, $lid){
        $teamData = AikanQController::teamData($sport, $tid);
        $result = [];
        if (!isset($teamData)) {
            return null;
        }

        $teamName = isset($teamData['shortName']) ? $teamData['shortName'] : $teamData['name'];
        if (!isset($lid) || strlen($lid) <= 0) {
            $lid = AikanQController::getLeagueByTid($sport, $tid);
        }

        $result['team'] = $teamData;
        $leagueData = AikanQController::getLeagueDataByLid($sport, $lid);
        $leagueName = "";
        if (count($leagueData) > 0) {
            $result['league'] = $leagueData;
            $leagueName = isset($leagueData['name']) ? $leagueData['name'] : "";
        }
        $result['title'] = "[".$teamName."]".$leagueName.$teamName."直播_".$teamName."赛程、球员阵容、新闻-爱看球直播";
        $result['h1'] = $teamData['name'].'直播';
        return $result;
    }

    /***** 资讯 ******/
    public function newsDetail(Request $request, $name_en, $tid, $page)
    {
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        if (isset($data)) {
            $data['name_en'] = $name_en;
            $this->html_var['zhuanti'] = $data;
        }
        $this->html_var['tid'] = $tid;
        $lid = $data['lid'];
        $sport = substr($tid, 0, 1);
        $tid = substr($tid, 1);

        $rdata = TeamController::recordData($name_en,$sport, $tid, $lid);
        $this->html_var['team'] = $rdata['team'];
        $this->html_var['title'] = $rdata['title'];
        $this->html_var['league'] = $rdata['league'];
        $cd = CommonTool::getComboData($name_en);
        $this->html_var['videos'] = array_key_exists('videos',$cd) ? $cd['videos'] : array();
        $this->html_var['name_en'] = $name_en;

        //资讯翻页
        $articles = TagRelation::getRelationsPageByTag(TagRelation::kTypeArticle,$sport,3,$rdata['team']['name'],$page,20);
        $this->html_var['articles'] = $articles;
        $this->html_var['page'] = $articles->lastPage();
        $this->html_var['pageNo'] = $page;
        return self::newsHtml($this->html_var);
    }
    private static function newsHtml($data)
    {
        if ($data == null) return abort(404);
        return view('pc.team.v2.news', $data);
    }

    public static function newsData($name_en,$sport, $tid, $lid){
        $teamData = AikanQController::teamData($sport, $tid);
        $result = [];
        if (!isset($teamData)) {
            return null;
        }

        $teamName = isset($teamData['shortName']) ? $teamData['shortName'] : $teamData['name'];
        if (!isset($lid) || strlen($lid) <= 0) {
            $lid = AikanQController::getLeagueByTid($sport, $tid);
        }

        $result['team'] = $teamData;
        $leagueData = AikanQController::getLeagueDataByLid($sport, $lid);
        $leagueName = "";
        if (count($leagueData) > 0) {
            $result['league'] = $leagueData;
            $leagueName = isset($leagueData['name']) ? $leagueData['name'] : "";
        }
        $result['title'] = "[".$teamName."]".$leagueName.$teamName."直播_".$teamName."赛程、球员阵容、新闻-爱看球直播";
        $result['h1'] = $teamData['name'].'直播';
        return $result;
    }

    public function staticNewsHtml(Request $request, $sport, $name_en, $tid, $page)
    {
        $path = CommonTool::getTeamDetailPathWithType($sport,$name_en,$tid,'news',$page);
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        $tempTid = $sport.$tempTid;
        $html = $this->newsDetail(new Request(),$name_en,$tempTid,$page);
        if (isset($html) && strlen($html) > 0){
            Storage::disk('public')->put("www/$path", $html);
        }
        else{
            echo 'TeamController staticNewsHtml ' . $name_en . ' ' . $tid .' ' .$page;
        }
        echo 'success';
    }
}