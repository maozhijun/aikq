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
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\Match;
use App\Models\LgMatch\MatchLive;
use App\Models\Match\Team;
use App\Models\Match\BasketTeam;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function rank(Request $request, $sport, $lid) {
        $rankData = AikanQController::leagueRankData($sport, $lid);
        $leagueData = AikanQController::getLeagueDataByLid($sport, $lid);
        if ($sport == 2){
            $tids = array();
            $teams = array();
            if ($lid == 1){
                foreach ($rankData['west'] as $item){
                    $tids[] = $item['tid'];
                }
                foreach ($rankData['east'] as $item){
                    $tids[] = $item['tid'];
                }
            }
            else{
                foreach ($rankData as $item){
                    $tids[] = $item['tid'];
                }
            }
            $o_teams = BasketTeam::whereIn('id',$tids)->get();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
        }
        else{
            $tids = array();
            $teams = array();
            foreach ($rankData as $item){
                $tids[] = $item['tid'];
            }
            $o_teams = Team::whereIn('id',$tids)->get();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
        }
        return view('pc.team.detail_rank_cell', ['teams'=>$teams,'ranks'=>$rankData, 'subject'=>$leagueData]);
    }

    public function detail(Request $request, $name_en, $tid)
    {
        $lid = "";
        $subjectLeague = null;
        if ($name_en != 'other') {
            $subjectLeague = SubjectLeague::getSubjectLeagueByEn($name_en);
            if (isset($subjectLeague)) {
                $lid = $subjectLeague->lid;
            }
        }
        $sport = substr($tid, 0, 1);
        $teamId = substr($tid, 1);

        $data = $this->detailDetail($sport, $lid, $teamId,$name_en,$tid,$subjectLeague);
        return self::detailHtml($data);
    }

    public function detailDetail($sport, $lid, $teamId,$name_en,$tid,$subj){
        $data = AikanQController::teamDetailData($sport, $lid, $teamId);
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data['zhuanti'] = $this->getSujectLeagueData($name_en, $subj);
        $data['tid'] = explode('_',$tid)[0];
        $data['sport'] = $sport;
        $data['show_right'] = false;
        if ($sport == 2){
            $tids = array();
            $teams = array();
            if ($lid == 1){
                foreach ($data['rank']['west'] as $item){
                    $tids[] = $item['tid'];
                }
                foreach ($data['rank']['east'] as $item){
                    $tids[] = $item['tid'];
                }
            }
            else{
                foreach ($data['rank'] as $item){
                    $tids[] = $item['tid'];
                }
            }
            $o_teams = BasketTeam::whereIn('id',$tids)->get();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $data['teams'] = $teams;
        }
        else{
            $tids = array();
            $teams = array();
            if (isset($data['rank'])) {
                foreach ($data['rank'] as $item){
                    if (isset($item['tid'])) {
                        $tids[] = $item['tid'];
                    }
                }
            }
            $o_teams = Team::whereIn('id',$tids)->get();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $data['teams'] = $teams;
        }
        return $data;
    }

    private static function detailHtml($data)
    {
        if ($data == null) return abort(404);
        $data['keywords'] = '';
        $data['description'] = '';
        return view('pc.team.v2.index', $data);
    }

    public function staticIndexHtml(Request $request, $sport, $name_en, $tid, $page){
        TeamController::detailStatic($name_en,$tid,$sport);
    }

    public function staticTeamAll(Request $request, $sport, $name_en, $tid, $page) {
        self::detailStatic($name_en,$tid,$sport);
        $this->staticRecordHtml($request, $sport, $name_en, $tid, $page);
        $this->staticNewsHtml($request, $sport, $name_en, $tid, $page);
        $this->staticVideoHtml($request, $sport, $name_en, $tid, $page);
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
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        $tempTid = $sport.$tempTid;
        $html = $con->detail(new Request(), $name_en, $tempTid);
        if (isset($html) && strlen($html) > 0){
            Storage::disk('public')->put("www/$path", $html);
        }
    }

    /***** 录像 *****/
    public function recordDetail(Request $request, $name_en, $tid, $page)
    {
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = $this->getSujectLeagueData($name_en);
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
        $this->html_var['comboData'] = CommonTool::getComboData($name_en);
        $this->html_var['name_en'] = $name_en;
        //录像
        $records = TagRelation::getRelationsPageByTagId(TagRelation::kTypePlayBack,$sport,3,$tid,$page,20);

        $this->html_var['records'] = $records;

        $this->html_var['page'] = $records->lastPage();
        $this->html_var['pageNo'] = $page;
        $this->html_var['title'] = $rdata['team']['name'].'比赛录像_'.$rdata['team']['name'].'比赛视频大全-爱看球直播';
        $this->html_var['keywords'] = '';
        $this->html_var['description'] = '';
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
        $data = $this->getSujectLeagueData($name_en);
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
        $this->html_var['comboData'] = CommonTool::getComboData($name_en);
        $this->html_var['name_en'] = $name_en;

        //资讯翻页
        $articles = TagRelation::getRelationsPageByTag(TagRelation::kTypeArticle,$sport,3,$rdata['team']['name'],$page,20);
        $this->html_var['articles'] = $articles;
        $this->html_var['page'] = $articles->lastPage();
        $this->html_var['pageNo'] = $page;
        $this->html_var['title'] = $rdata['team']['name'].'最新资讯_'.$rdata['team']['name'].'新闻-爱看球直播';
        $this->html_var['keywords'] = '';
        $this->html_var['description'] = '';
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
    }

    /***** 视频 *****/
    public function videoDetail(Request $request, $name_en, $tid, $page)
    {
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = $this->getSujectLeagueData($name_en);
        if (isset($data)) {
            $data['name_en'] = $name_en;
            $this->html_var['zhuanti'] = $data;
        }
        $this->html_var['tid'] = $tid;
        $lid = $data['lid'];
        $sport = substr($tid, 0, 1);
        $tid = substr($tid, 1);

        $rdata = TeamController::videoData($name_en,$sport, $tid, $lid);
        $this->html_var['team'] = $rdata['team'];
        $this->html_var['title'] = $rdata['title'];
        $this->html_var['league'] = $rdata['league'];
        $this->html_var['comboData'] = CommonTool::getComboData($name_en);
        $this->html_var['name_en'] = $name_en;

        //录像
        $videos = TagRelation::getRelationsPageByTagId(TagRelation::kTypeVideo,$sport,3,$tid,$page,20);

        $this->html_var['videos'] = $videos;

        $this->html_var['page'] = $videos->lastPage();
        $this->html_var['pageNo'] = $page;
        $this->html_var['title'] = $rdata['team']['name'].'精彩集锦_'.$rdata['team']['name'].'视频大全-爱看球直播';
        $this->html_var['keywords'] = '';
        $this->html_var['description'] = '';
        return view('pc.team.v2.video', $this->html_var);
    }

    /**
     * 静态化调用
     * @param Request $request
     * @param $sport
     * @param $name_en
     * @param $tid
     * @param $page
     */
    public function staticVideoHtml(Request $request, $sport, $name_en, $tid, $page)
    {
        $path = CommonTool::getTeamDetailPathWithType($sport,$name_en,$tid,'video',$page);
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        $tempTid = $sport.$tempTid;
        $html = $this->videoDetail(new Request(),$name_en,$tempTid,$page);
        if (isset($html) && strlen($html) > 0){
            Storage::disk('public')->put("www/$path", $html);
        }
        else{
            echo 'TeamController staticvideoHtml ' . $name_en . ' ' . $tid .' ' .$page;
        }
    }

    /**
     * 获取数据
     * @param $name_en
     * @param $sport
     * @param $tid
     * @param $lid
     * @return array|null
     */
    public static function videoData($name_en,$sport, $tid, $lid){
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

    /**
     * 获取专题数据
     */
    private function getSujectLeagueData($name_en, $subj = null) {
        if (isset($subj)) {
            return $subj;
        }
        if ($name_en != 'other') {
            $subj = SubjectLeague::getSubjectLeagueByEn($name_en);
        } else {
            $subj = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        }
        return $subj;
    }

    /******** 接口类 *********/

    public function getRecentMatches(Request $request,$sport,$tid){
        $page = $request->input('page',1);
        if ($sport == MatchLive::kSportFootball) {
            $query = Match::query();
        } else {
            $query = BasketMatch::query();
        }
        $query->where(function ($q) use ($tid){
            $q->where('hid', $tid)
                ->orWhere('aid', $tid);
        });
        $query->selectRaw('*, id as mid');
        $query->where('time', '>=', date('Y-m-d H:i', strtotime('-3 hours')));
        $query->where('status', '>=', 0);
        $query->orderBy('time');
        $matches = $query->paginate(10, ["*"], null, $page);

        $array = array();
        foreach ($matches as $match){
            $array[] = AikanQController::onMatchItemConvert($sport, $match, "", false);
        }
        return response()->json(array('code'=>0,'data'=>$array));
    }

    public function getHistoryMatches(Request $request,$sport,$tid){
        $page = $request->input('page',1);
        if ($sport == MatchLive::kSportFootball) {
            $query = Match::query();
        } else {
            $query = BasketMatch::query();
        }
        $query->where(function ($q) use ($tid){
            $q->where('hid', $tid)
                ->orWhere('aid', $tid);
        });
        $query->selectRaw('*, id as mid');

        $tempQuery = clone $query;

        //历史比赛
        $matches = $tempQuery->where('status', '-1')
            ->where('time','>=',date_create('-1 year'))
            ->orderBy('time', 'desc')
            ->paginate(10, ["*"], null, $page);

        $array = array();
        foreach ($matches as $match){
            if (array_key_exists($sport.'-'.$match['lid'],Controller::MATCH_LEAGUE_IDS)){
                $lname = Controller::MATCH_LEAGUE_IDS[$sport.'-'.$match['lid']]['name_en'];
            }
            else{
                $lname = 'other';
            }
            $array[] = AikanQController::onMatchItemConvert2($sport, $match, $lname, false);
        }
        return response()->json(array('code'=>0,'data'=>$array));
    }
}