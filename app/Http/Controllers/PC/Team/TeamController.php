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
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
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
        $tid = substr($tid, 1);

        $data = AikanQController::teamDetailData($sport, $lid, $tid);
        return self::detailHtml($data);
    }

    private static function detailHtml($data)
    {
        if ($data == null) return abort(404);
        return view('pc.team.v2.detail', $data);
    }

    public static function detailStatic($data, $path)
    {
        $html = self::detailHtml($data);

        Storage::disk('public')->put("www/$path", $html);
//        echo "pc: $path </br>";
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
        $query = SubjectVideo::query();
        $query->where('s_lid',$data['id']);
        $query->orderby('time','desc');
        $records = $query->paginate(20, ['*'], '', $page);

        $this->html_var['records'] = $records;

        $this->html_var['page'] = $records->lastPage();
        $this->html_var['pageNo'] = $page;

        return self::recordHtml($this->html_var);
    }

    /**
     * 静态化调用
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    private static function recordHtml($data)
    {
        if ($data == null) return abort(404);
        return view('pc.team.v2.record', $data);
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
}