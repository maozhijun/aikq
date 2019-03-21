<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/9
 * Time: 15:02
 */

namespace App\Http\Controllers\Mobile\Subject;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\IntF\Common\LeagueDataTool;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\Live\SubjectVideoController;
use App\Http\Controllers\PC\MatchTool;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketStage;
use App\Models\LgMatch\Season;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Subject\SubjectLeague;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * 爱看球专题
 * Class SubjectController
 * @package App\Http\Controllers\PC\Subject
 */
class SubjectController extends Controller
{

    protected $akqCon;

    function __construct()
    {
        $this->akqCon = new AikanQController();
    }

    /**
     *
     * @param Request $request
     * @param $name 名字
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $name = null)
    {
        if (is_null($name)) {
            $path = $request->path();
            $name = str_replace("m/", "", $path);
        }
        if (!array_key_exists($name, self::SUBJECT_NAME_IDS)) {
            return abort(404);
        }
        $s_lid = self::SUBJECT_NAME_IDS[$name]['id'];

        $result = $this->getSubjectDetail($s_lid);
        if (!isset($result) || !isset($result['subject'])) {
            return abort(404);
        }

        //处理专题内容
        $subject = $result['subject'];
        $content = $subject['content'];
        $content_array = explode("\r", $content);
        $new_content = '';
        foreach ($content_array as $c) {
            $new_content .= '<p>' . $c . '</p>';
        }
        $subject['content'] = $new_content;
        $icon = $subject['icon'];
        $icon = str_replace('https://www.liaogou168.com', '', $icon);
        $icon = str_replace('http://www.liaogou168.com', '', $icon);
        if (!starts_with('http', $icon)) {
            $icon = env('CDN_URL') . '/live/subject' . $icon;
        }
        $subject['icon'] = $icon;
        $result['subject'] = $subject;
        //处理专题内容

        $result['subjects'] = self::getSubjects();//所有专题
        $result['weekCnArray'] = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        $result['hasLeft'] = (isset($result['articles']) && count($result['articles']) > 0) || (isset($result['ranks']) && count($result['ranks']) > 0);
        $hasRound = false;//是否有轮次
        if (isset($result['lives']) && count($result['lives']) > 0) {
            $lives = $result['lives'];
            foreach ($lives as $day => $matches) {
                $hasRound = isset($matches[0]['round']);
                break;
            }
        }
        $subjectName = $subject['name'];
        $result['hasRound'] = $hasRound;
        $result['slid'] = $s_lid;
        $result['lid'] = self::SUBJECT_NAME_IDS[$name]['lid'];

        return view('mobile.subject.detail', $result);
    }

    /**
     * 录像播放终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $vid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectVideo(Request $request, $first, $second, $vid)
    {
        //录像 播放页面
        $video = $this->getSubjectVideo($vid);
        if (isset($video['error'])) {
            return "";
        }
        $result['match'] = $video;
        $result['type'] = 'video';

        $lname = $video['lname'];
        $hname = $video['hname'];
        $aname = $video['aname'];

        $match_title = $hname . "VS" . $aname;
        $result['title'] = $match_title . "全场回放_" . $match_title . "高清录像_" . $lname . "录像_爱看球";
        $result['keywords'] = '爱看球,' . $lname . ',' . $match_title . ',' . $hname . ',' . $aname;
        return view('pc.subject.video', $result);
    }

    /**
     * 专题录像终端HTML
     * @param $video
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function subjectVideoHtml($video)
    {
        $result['match'] = $video;
        $result['type'] = 'video';

        $lname = $video['lname'];
        $hname = $video['hname'];
        $aname = $video['aname'];

        $match_title = $hname . "VS" . $aname;
        $result['title'] = $match_title . "全场回放_" . $match_title . "高清录像_" . $lname . "录像_爱看球";
        $result['keywords'] = '爱看球,' . $lname . ',' . $match_title . ',' . $hname . ',' . $aname;
        return view('pc.subject.video', $result);
    }

    /**
     *
     * @param Request $request
     * @param $first
     * @param $second
     * @param $sid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectSpecimen(Request $request, $first, $second, $sid)
    {
        $specimen = $this->getSubjectSpecimen($sid);
        if (isset($specimen['error'])) {
            return "";
        }
        $result['match'] = $specimen;
        $result['type'] = 'specimen';
        return view('pc.subject.video', $result);
    }

    /**
     * 线路播放json
     * @param Request $request
     * @param $first
     * @param $second
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideoChannelJson(Request $request, $first, $second, $id)
    {
        $json = $this->getSubjectVideoChannel($id);
        return response()->json($json);
    }

    /**
     * 线路播放json
     * @param Request $request
     * @param $first
     * @param $second
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectSpecimenChannelJson(Request $request, $first, $second, $id)
    {
        $json = $this->getSubjectSpecimenChannel($id);
        return response()->json($json);
    }

    /**
     * 播放 线路的页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectPlayer(Request $request)
    {
        return view('pc.subject.player', ['cdn' => env('CDN_URL')]);
    }
    //=====================================================//

    //===============aikq2.0===================================//

    public function detailV2(Request $request, $name_en, $season = "")
    {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (!isset($sl)) {
            return abort(404);
        }
        $sport = $sl->sport;
        $lid = $sl->lid;

        $result = array();
        try {
            $comboData = CommonTool::getComboData($name_en);
            $result["comboData"] = $comboData;
            //专题资讯 开始
            $result['articles'] = isset($comboData['articles']) ? $comboData['articles'] : array();
            //专题视频 开始
            $result['videos'] = isset($comboData['videos']) ? $comboData['videos'] : array();
        } catch (\Exception $exception) {
        }

        if ($sport == SubjectLeague::kSportBasketball) {
            return $this->basketDetailHtml($sl, $season, $result);//篮球
        }

        //赛季
        if (!empty($season)) {
            $footballSeason = Season::query()->where("lid", $lid)->where("name", $season)->first();
        } else {
            $footballSeason = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $season = $footballSeason["name"];
        }

        //数据榜单（球员榜单）
        $playerString = CommonTool::getPlayerData($sport, $lid, $season, 0);
        $players = json_decode($playerString, true);
        $result["players"] = $players;

        //判断是否杯赛
        $total_round = $footballSeason["total_round"];
        if (is_null($total_round)) {
            return $this->footballCupDetailHtml($sl, $footballSeason, $result);//无轮次则是 杯赛
        }

        //赛季
        $leagueData = LeagueDataTool::getLeagueDataBySeason($sport, $lid, $season);
        $schedule = isset($leagueData["schedule"]) ? $leagueData["schedule"] : [];//所有赛程
        $leagueSeason = isset($leagueData["season"]) ? $leagueData["season"] : [];//赛程信息
        $seasons = isset($leagueData["seasons"]) ? $leagueData["seasons"] : null;

        //积分
        $scores = $leagueData['score'];
        //赛程
        if (!isset($leagueSeason["curr_round"])) {
            $fMatch = Match::scheduleNearMatch($lid); //数据库获取当前轮次
            $curr_round = $fMatch["round"];
        } else {
            $curr_round = $leagueSeason["curr_round"];//当前轮次
        }

        $result["sl"] = $sl;
        $result["season"] = $footballSeason;
        $result["seasons"] = $seasons;
        $result["curRound"] = $curr_round;
        $result["ranks"] = [$scores];
        $result["schedules"] = $schedule;
        $result['title'] = '[' . $sl->name . '直播]' . $sl->name . '免费在线直播观看_哪里可以看' . $sl->name . '直播网址-爱看球直播';

        return view("mobile.subject.v2.football_detail", $result);
    }

    /**
     * 杯赛终端
     */
    public function footballCupDetailHtml(SubjectLeague $sl, Season $season, $result = array())
    {
        $sport = $sl["sport"];
        $lid = $sl["lid"];

        $leagueData = LeagueDataTool::getLeagueDataBySeason($sport, $lid, $season["name"]);
        $stageDatas = $leagueData['stages'];

        $ranks = array();
        foreach ($stageDatas as $stageData) {
            if ($stageData['name'] == "分组赛") {
                $ranks = collect($stageData['groupMatch'])->map(function ($item) {
                    return $item['scores'];
                })->all();
                break;
            }
        }

        $result['ranks'] = $ranks;//杯赛小组排名
        $result["sl"] = $sl;
        $result["season"] = $season;
        $result["seasons"] = isset($leagueData["seasons"]) ? $leagueData["seasons"] : null;
        $result["schedules"] = $stageDatas;
        $result["knockout"] = $stageDatas;
        $result['title'] = '[' . $sl["name"] . '直播]' . $sl["name"] . '免费在线直播观看_哪里可以看' . $sl["name"] . '直播网址-爱看球直播';
        return view("mobile.subject.v2.football_cup_detail", $result);
    }

    /**
     * 篮球专题终端页
     * @param SubjectLeague $sl
     * @param $season = ""  赛季 例：18-19
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketDetailHtml(SubjectLeague $sl, $season = "", $result = array())
    {
        $sport = $sl["sport"];
        $lid = $sl["lid"];

        if (!empty($season)) {
            $basketSeason = BasketSeason::query()->where('lid', $lid)->where('name', $season)->first();
        }
        if (!isset($basketSeason)) {
            $basketSeason = BasketSeason::query()->where('lid', $lid)->orderby('name', 'desc')->first();
        }

        $kind = 0;
        if (isset($basketSeason)) {
            $kind = $basketSeason['kind'];
            $season = $basketSeason['name'];
        }

        $leagueData = LeagueDataTool::getLeagueDataBySeason($sl["sport"], $sl["lid"], $season);

        $westRanks = isset($leagueData['scores']['west']) ? $leagueData['scores']['west'] : array();
        $eastRanks = isset($leagueData['scores']['east']) ? $leagueData['scores']['east'] : array();

        //三天 赛程
//        $curYear = substr(date('Y'), 2, 2);
        $scheduleMatches = $leagueData['schedule'];
//        if (preg_match("#" . $curYear . "#", $season)) {//当前赛季
//            $scheduleMatches = BasketMatch::scheduleMatchesByLidAndTime($lid, $season);//从数据库获取 赛程
//        } else {//非当前赛季
//            //获取总决赛比赛场次
//            $bs = BasketStage::getFinal($lid, $season);
//            if (isset($bs)) {
//                $scheduleMatches = BasketMatch::scheduleMatchesByStage($lid, $bs->id);
//            }
//        }

        $playerString = CommonTool::getPlayerData($sport, $lid, $season, $kind == 2 ? 1 : $kind);
        $data = json_decode($playerString, true);
        $result["players"] = $data;

        $result["sl"] = $sl;

        if (count($westRanks) > 0 && count($eastRanks) > 0) {
            $result["ranks"] = ['西部' => $westRanks, '东部' => $eastRanks];
        } else if (count($westRanks) > 0) {
            $result["ranks"] = [$westRanks];
        } else if (count($eastRanks) > 0) {
            $result["ranks"] = [$eastRanks];
        }

        $result["season"] = $basketSeason;
        $result["seasons"] = $leagueData["seasons"];
        if (isset($leagueData["playoff"])) {
            $result["playoff"] = $leagueData["playoff"];
        }
        $result["schedules"] = [$scheduleMatches];
        $result['title'] = '[' . $sl->name . '直播]' . $sl->name . '免费在线直播观看_哪里可以看' . $sl->name . '直播网址-爱看球直播';

        return view("mobile.subject.v2.basketball_detail", $result);
    }

    //==================================================//

    /**
     * 通过接口获取专题列表内容
     */
    public static function getSubjects()
    {
        //先从文件获取内容
        try {
            $server_output = Storage::get('public/static/json/pc/subject/leagues.json');
        } catch (\Exception $exception) {
            $server_output = "";
        }
        $subjects = json_decode($server_output, true);
        $subjects = isset($subjects) ? $subjects : [];
        return $subjects;
    }

    /**
     * 通过接口获取专题终端内容
     * @param $id
     * @return array|mixed|void
     */
    public function getSubjectDetail($id)
    {
        $aiCon = new AikanQController();
        $data = $aiCon->subjectDetail(new Request(), $id)->getData();
        $data = json_encode($data);
        $subjects = json_decode($data, true);
        return $subjects;
    }

    /**
     * 通过接口获取录像信息
     * @param $id
     * @return array|mixed
     */
    public function getSubjectVideo($id)
    {
        $video = $this->akqCon->subjectVideo($id);
        $video = isset($video) ? $video : [];
        return $video;
    }

    /**
     * 通过接口获取集锦信息
     * @param $id
     * @return array|mixed
     */
    public function getSubjectSpecimen($id)
    {
        $specimen = $this->akqCon->subjectSpecimen($id);
        $specimen = isset($specimen) ? $specimen : [];
        return $specimen;
    }

    /**
     * 获取录像线路信息
     * @param $cid
     * @return array|mixed
     */
    public function getSubjectVideoChannel($cid)
    {
        $channel = $this->akqCon->subjectVideoChannelJson(new Request(), $cid);
        $channel = isset($channel) ? $channel : ['code' => -1];
        return $channel;
    }

    /**
     * 获取录像线路信息
     * @param $cid
     * @return array|mixed
     */
    public function getSubjectSpecimenChannel($cid)
    {
        $specimen = $this->akqCon->subjectSpecimenChannelJson(new Request(), $cid)->getData();
        $specimen = isset($specimen) ? $specimen : ['code' => -1];
        return $specimen;
    }
    //================================================静态化================================================//

    /**
     * 静态化专题列表json
     * @param Request $request
     */
    public function staticSubjectLeagues(Request $request)
    {
        $aiCon = new AikanQController();
        $data = $aiCon->subjects(new Request())->getData();
        $server_output = json_encode($data);
        if (!empty($server_output)) {
            Storage::disk("public")->put("/static/json/pc/subject/leagues.json", $server_output);
        }
    }

    /**
     * 静态化专题终端json数据
     * @param Request $request
     * @param $slid
     */
    public function staticSubjectDetailJson(Request $request, $slid)
    {
        $server_output = $this->akqCon->subjectDetail($request, $slid)->getData();
        $server_output = json_encode($server_output);
        if (!empty($server_output)) {
            Storage::disk("public")->put("/static/json/pc/subject/" . $slid . ".json", $server_output);
        }
    }

    /**
     * 静态化专题终端页
     * @param Request $request
     * @param $str
     */
    public function staticSubjectHtml(Request $request, $str)
    {
        $html = $this->detail($request, $str);
        if (!empty($html)) {
            Storage::disk("public")->put("/m/" . $str . "/index.html", $html);
        }
    }

    /**
     * 静态化录像html
     * @param Request $request
     * @param $vid
     */
    public function staticSubjectVideoHtml(Request $request, $vid)
    {
        $html = $this->subjectVideo($request, '', '', $vid);
        if (!empty($html)) {//静态化录像终端
            $patch = MatchTool::subjectLink($vid, 'video');
            Storage::disk("public")->put($patch, $html);
        }
    }

    /**
     * 获取列表中所有录像并静态化其终端
     * @param Request $request
     * @param $type
     * @param $page
     */
    public function staticSubjectVideoHtmlFromVideos(Request $request, $type, $page)
    {
        $videoIntF = new SubjectVideoController();
        $data = $videoIntF->getSubjectVideos($type, $page);
        if (!isset($data['videos'])) {
            echo "专题录像终端静态化无数据，执行完毕。\n";
            return;
        }
        $videos = $data['videos'];
        foreach ($videos as $video) {
            $html = $this->subjectVideoHtml($video);
            if (!empty($html)) {//静态化录像终端
                $vid = $video['id'];
                $patch = MatchTool::subjectLink($vid, 'video');
                Storage::disk("public")->put($patch, $html);
            }
        }
    }

    /**
     * 静态化录像线路json
     * @param Request $request
     * @param $ch_id
     */
    public function staticSubjectVideoChannelJson(Request $request, $ch_id)
    {
        $json = $this->getSubjectVideoChannel($ch_id);//$this->getSubjectVideo($ch_id);//
        $jsonStr = json_encode($json);
        if (!empty($jsonStr)) {
            $patch = MatchTool::subjectChannelLink($ch_id, 'video');
            Storage::disk("public")->put($patch, $jsonStr);
            $patch = MatchTool::subjectChannelLink($ch_id, 'video', true);
            Storage::disk("public")->put($patch, $jsonStr);
        }
    }

    /**
     * 静态化集锦 html 线路json
     * @param Request $request
     * @param $sid
     */
    public function staticSubjectSpecimenHtml(Request $request, $sid)
    {
        $html = $this->subjectSpecimen($request, '', '', $sid);
        if (!empty($html)) {
            $patch = MatchTool::subjectLink($sid, 'specimen');
            Storage::disk("public")->put($patch, $html);
        }
        $json = $this->getSubjectSpecimenChannel($sid);
        $jsonStr = json_encode($json);
        if (!empty($jsonStr)) {
            $patch = MatchTool::subjectChannelLink($sid, 'specimen');
            Storage::disk("public")->put($patch, $jsonStr);
        }
    }

    /**
     * 静态化player页面
     * @param Request $request
     */
    public function staticPlayer(Request $request)
    {
        $html = $this->subjectPlayer($request);
        if (!empty($html)) {
            Storage::disk("public")->put("/live/subject/player.html", $html);
        }
    }
}