<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/9
 * Time: 15:02
 */

namespace App\Http\Controllers\PC\Live;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\IntF\Common\LeagueDataTool;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\MatchTool;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketStage;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\LgMatch\Stage;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * 爱看球专题
 * Class SubjectController
 * @package App\Http\Controllers\PC\Subject
 */
class SubjectController extends Controller
{

    /**
     *
     * @param Request $request
     * @param $name_en
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $name_en) {
        $aiCon = new AikanQController();
        $subjectLeague = SubjectLeague::getSubjectLeagueByEn($name_en);
        $result = $aiCon->subjectDetailData(false, $subjectLeague);
        if (!isset($result) || !isset($result['subject'])) {
            return abort(404);
        }
        return self::subjectDetailHtml($result, $subjectLeague);
    }

    /**
     * 终端静态化
     * @param $result
     * @param SubjectLeague $sl
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function subjectDetailHtml($result, SubjectLeague $sl) {
        //处理专题内容
        $subject = $result['subject'];
        $content = $subject['content'];
        $content_array = explode("\r", $content);
        $new_content = '';
        foreach ($content_array as $c) {
            $new_content .= '<p>' . $c. '</p>';
        }
        $subject['content'] = $new_content;
        $icon = $subject['icon'];
        if (!preg_match("/^https?:\/\//", $icon)) {
            $icon = env('CDN_URL') . $icon;
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
            foreach ($lives as $day=>$matches) {
                $hasRound = isset($matches[0]['round']);
                break;
            }
        }
        $subjectName = $subject['name'];
        $result['hasRound'] = $hasRound;
        $result['slid'] = $sl['id'];
        $result['ma_url'] = self::getMobileHttpUrl("/".$sl['name_en']."/");
        return view('pc.subject.detail', $result);
    }


    public function detailV2(Request $request, $name_en, $season = "") {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (!isset($sl)) {
            return "";
        }
        $sport = $sl->sport;
        $lid = $sl->lid;
        $type = $sl->type;

        if ($sport == SubjectLeague::kSportBasketball) {
            return $this->basketDetailHtml($sl, $season);//篮球
        }

        //赛季
        if (!empty($season)) {
            $footballSeason = Season::query()->where("lid", $lid)->where("name", $season)->first();
        } else {
            $footballSeason = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $season = $footballSeason["name"];
        }

        //判断是否杯赛
        if ($type == 2) {
            return $this->footballCupDetailHtml($sl, $footballSeason);//无轮次则是 杯赛
        }

        //赛季
        $leagueData = LeagueDataTool::getLeagueDataBySeason($sport, $lid, $season);
        $schedule = isset($leagueData["schedule"]) ? $leagueData["schedule"] : [];//所有赛程
        $leagueSeason = isset($leagueData["season"]) ? $leagueData["season"] : [];//赛程信息
        $seasons = isset($leagueData["seasons"]) ? $leagueData["seasons"] : null;

        //积分
        $scores = isset($leagueData["score"]) ? $leagueData["score"] : [];//Score::getFootballScores($lid, $season);
        //赛程
        if (!isset($leagueSeason["curr_round"])) {
            $fMatch = Match::scheduleNearMatch($lid); //数据库获取当前轮次
            $curr_round = $fMatch["round"];
        } else {
            $curr_round = $leagueSeason["curr_round"];//当前轮次
        }

        //$rounds = Match::getScheduleMatchLive($lid, $round); //数据库获取当前轮的赛程

        //数据榜单
        //右侧内容（球员信息）
        $playerString = CommonTool::getPlayerData($sport, $lid, $season, 0);
        $data = json_decode($playerString, true);

        try {
            $comboData = CommonTool::getComboData($name_en);
            $result["comboData"] = $comboData;
        } catch (\Exception $exception) {
        }

        $result["sl"] = $sl;
        $result["season"] = $footballSeason;
        $result["seasons"] = $seasons;
        $result["curRound"] = $curr_round;
        $result["scores"] = $scores;
        $result["schedule"] = $schedule;
        $result["data"] = $data;
        $result['title'] = '['.$sl->name.'直播]'.$sl->name.'免费在线直播观看_哪里可以看'.$sl->name.'直播网址-爱看球直播';
        return view("pc.subject.v2.football_detail", $result);
    }

    /**
     * 杯赛终端
     * @param SubjectLeague $sl
     * @param Season $season
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function footballCupDetailHtml(SubjectLeague $sl, Season $season) {
        $sport = $sl["sport"];
        $name_en = $sl["name_en"];
        $lid = $sl["lid"];

        $stages = Stage::getStages($lid, $season["name"]);//杯赛阶段
        $curStage = null;
        $knockoutStages = [];
        $groupId = -1;
        foreach ($stages as $stage) {
            $stageId = $stage["id"];
            if ($stage["status"] == 1) {
                $curStage = $stage;
            }
            if ($stage["name"] == "分组赛") {
                $groupId = $stageId;
            }
            if ($groupId > -1 && $stageId > $groupId) {
                $knockoutStages[] = $stage;
            }
        }
        $leagueData = LeagueDataTool::getLeagueDataBySeason($sport, $lid, $season["name"]);
//dump($leagueData);
        $schedules = null;
        $knockouts = [];//淘汰赛
        $ranks = [];
        if (isset($leagueData["stages"])) {//全部赛程 组装成简单的数组
            $leagueStages = $leagueData["stages"];
            foreach ($leagueStages as $leagueStage) {
                $id = $leagueStage["id"];
                $name = trim($leagueStage["name"]);
                $status = $leagueStage["status"];

                $matches = [];
                $groupMatches = null;
                if (isset($leagueStage["matches"])) {
                    $matches = $leagueStage["matches"];
                    if ($this->isKnockout($name)) {//
                        if ($name == "半准决赛") {
                            $kkCount = 4;
                        } else {
                            $kkCount = count($matches);
                        }
                        $teams = [];
                        foreach ($matches as $kMatch) {
                            $hName = $kMatch["hname"];
                            $aName = $kMatch["aname"];
                            $mid = $kMatch["mid"];
                            $key = $hName > $aName ? $hName.$aName : $aName.$hName;
                            $key = md5($key);
                            $teams[$key]["host"] = ["name"=>$hName, "score"=>$kMatch["hscore"], "id"=>$kMatch["hid"], "mid"=>$mid];
                            $teams[$key]["away"] = ["name"=>$aName, "score"=>$kMatch["ascore"], "id"=>$kMatch["aid"], "mid"=>$mid];
                        }
                        $knockouts[$kkCount * 2] = $teams;
                    }
                } else if (isset($leagueStage["combo"])){//淘汰赛
                    $combos = $leagueStage["combo"];
                    $count = count($combos);
                    $teams = [];
                    foreach ($combos as $key=>$combo) {
                        foreach ($combo["matches"] as $m) {
                            $matches[] = $m;
                        }

                        //处理淘汰赛 赛程 开始
                        if (!$this->isKnockout($name)) {
                            continue;
                        }
                        $hscore = isset($combo["hscore"]) ? $combo["hscore"] : "";
                        $ascore = isset($combo["ascore"]) ? $combo["ascore"] : "";
                        $teams[$key]["host"] = ["name"=>$combo["hname"], "score"=>$hscore, "id"=>$combo["hid"], "mid"=>""];
                        $teams[$key]["away"] = ["name"=>$combo["aname"], "score"=>$ascore, "id"=>$combo["aid"], "mid"=>""];
                        //处理淘汰赛 赛程 结束
                    }
                    if (count($teams) > 0) {
                        $knockouts[$count * 2] = $teams;
                    }
                } else if (isset($leagueStage["groupMatch"])) {//分组赛
                    $groupMatches = $leagueStage["groupMatch"];
                    foreach ($groupMatches as $g=>$group) {
                        if (isset($group["scores"])) {
                            $ranks[$g] = $group["scores"];
                        }
                    }
                }
                usort($matches, function ($a, $b) {
                    return $a["time"] - $b["time"];
                });
                $schedules[$id] = ["id"=>$id, "name"=>$name, "status"=>$status, "matches"=>$matches, "groupMatches"=>$groupMatches];
            }
        }
//dump($knockouts);
        if (count($knockouts) > 0) {
            //进入淘汰赛阶段
            //补充没有的淘汰赛阶段
            foreach ([16, 8, 4, 2] as $count) {
                if (!isset($knockouts[$count])) {
                    for ($index = 0; $index <$count / 2; $index++) {
                        $knockouts[$count][$index]["host"] = ["name"=>"", "score"=>"", "id"=>"", "mid"=>""];
                        $knockouts[$count][$index]["away"] = ["name"=>"", "score"=>"", "id"=>"", "mid"=>""];
                    }
                }
            }
        }
//        if (count($knockoutStages) > 0) {//淘汰赛阶段  使用数据库获取淘汰赛程 19-03-14 弃用，改用接口出
//            $knockouts = [];
//            $count = 16;//默认16支球队
//            foreach ($knockoutStages as $index=>$ks) {
//                $kkStage = $ks["id"];
//                $kkName = $ks["name"];
//                if ($kkName == "三十二强") continue;
//                $kkSchedules = \App\Models\LgMatch\Match::getScheduleCup($lid, $kkStage);//淘汰赛 赛程
//                $teams = [];
//                foreach ($kkSchedules as $kMatch) {
//                    $hid = $kMatch["hid"];
//                    $aid = $kMatch["aid"];
//                    $mid = $kMatch["mid"];
//                    if ($hid > $aid) {
//                        $tKey = $hid . "_" . $aid;
//                    } else {
//                        $tKey = $aid . "_" . $hid;
//                    }
//                    if (!isset($teams[$tKey])) {
//                        $teams[$tKey]["host"] = ["name"=>$kMatch["hname"], "score"=>$kMatch["hscore"], "id"=>$hid, "mid"=>$mid];
//                        $teams[$tKey]["away"] = ["name"=>$kMatch["aname"], "score"=>$kMatch["ascore"], "id"=>$aid, "mid"=>$mid];
//                    } else {
//                        if ($teams[$tKey]["host"]["id"] == $hid) {
//                            $teams[$tKey]["host"]["score"] = $teams[$tKey]["host"]["score"] + $kMatch["hscore"];
//                            $teams[$tKey]["away"]["score"] = $teams[$tKey]["away"]["score"] + $kMatch["ascore"];
//                        } else {
//                            $teams[$tKey]["host"]["score"] = $teams[$tKey]["host"]["score"] + $kMatch["ascore"];
//                            $teams[$tKey]["away"]["score"] = $teams[$tKey]["away"]["score"] + $kMatch["hscore"];
//                        }
//                    }
//                }
//                $knockouts[$count] = $teams;
//                $count = $count / 2;
//            }
//            //补充没有的淘汰赛阶段
//            foreach ([8, 4, 2] as $count) {
//                if (!isset($knockouts[$count])) {
//                    for ($index = 0; $index <$count / 2; $index++) {
//                        $knockouts[$count][$index]["host"] = ["name"=>"", "score"=>"", "id"=>"", "mid"=>""];
//                        $knockouts[$count][$index]["away"] = ["name"=>"", "score"=>"", "id"=>"", "mid"=>""];
//                    }
//                }
//            }
//        }

//        $schedules = null;
//        if (isset($curStage)) {
//            $group = null;
//            if ($curStage["name"] == "分组赛") {
//                $group = substr($curStage["group"], 0, 1);
//            }
//            $schedules = Match::getScheduleCup($lid, $curStage["id"], $group);//赛程
//        }

        $playerString = CommonTool::getPlayerData($sport, $lid, $season["name"], 0);
        $data = json_decode($playerString, true);

        try {
            $comboData = CommonTool::getComboData($name_en);
            $result["comboData"] = $comboData;
        } catch (\Exception $exception) {}

        $result['ranks'] = $ranks;//Score::footballCupScores($lid, $season["name"]);//杯赛小组排名
        $result["sl"] = $sl;
        $result["season"] = $season;
        $result["seasons"] = isset($leagueData["seasons"]) ? $leagueData["seasons"] : null;
        $result["stages"] = $stages;
        $result["schedules"] = $schedules;
        $result["data"] = $data;
        $result["knockouts"] = $knockouts;
        $result['title'] = '['.$sl["name"].'直播]'.$sl["name"].'免费在线直播观看_哪里可以看'.$sl["name"].'直播网址-爱看球直播';
        return view("pc.subject.v2.football_detail_cup", $result);
    }

    /**
     * 篮球专题终端页
     * @param SubjectLeague $sl
     * @param $season = ""  赛季 例：18-19
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketDetailHtml(SubjectLeague $sl, $season = "") {
        $sport = $sl["sport"];
        $lid = $sl["lid"];
        $name_en = $sl["name_en"];

        if (!empty($season)) {
            $basketSeason = BasketSeason::query()->where('lid', $lid)->where('name', $season)->first();
        }
        if (!isset($basketSeason)){
            $basketSeason = BasketSeason::query()->where('lid', $lid)->orderby('name','desc')->first();
        }

        $kind = 0;$year = '';
        if (isset($basketSeason)){
            $kind = $basketSeason['kind'];
            $season = $basketSeason['name'];
        }

        $leagueData = LeagueDataTool::getLeagueDataBySeasonNew($sl["sport"], $sl["lid"], $season);

        $westRanks = isset($leagueData["scores"]["west"]) ? $leagueData["scores"]["west"] : [];//BasketScore::getScoresByLid($lid, BasketScore::kZoneWest, $season);
        $eastRanks = isset($leagueData["scores"]["east"]) ? $leagueData["scores"]["east"] : [];//BasketScore::getScoresByLid($lid, BasketScore::kZoneEast, $season);

        //三天 赛程
        $schedule = $leagueData["schedule"];//从接口获取赛程
        $scheduleMatches = [];

        $curYear = substr(date('Y'), 2, 2);
        $startTime = strtotime(date('Y-m-d 00:00'));
        $endTime = date('Y-m-d 23:59', strtotime('+2 day', $startTime));
        $endTime = strtotime($endTime);
        if (preg_match("#".$curYear."#", $season)) {//当前赛季
            $scheduleMatches = BasketMatch::scheduleMatchesByLidAndTime($lid, $season);//从数据库获取 赛程
        } else {//非当前赛季
            //获取总决赛比赛场次
            $bs = BasketStage::getFinal($lid, $season);
            if (isset($bs)) {
                $scheduleMatches = BasketMatch::scheduleMatchesByStage($lid, $bs->id);
                $index = 0;
                foreach ($scheduleMatches as $date=>$sm) {
                    if ($index++ == 0) {
                        $startTime = strtotime($date . " 00:00");
                    }
                    $endTime = strtotime($date . " 23:59");
                }
            }
        }

//        foreach ($schedule as $match) {
//            $time = $match["time"];
//            if ($time >= $startTime && $time <= $endTime) {
//                $date = date('Y-m-d', $time);
//                $scheduleMatches[$date][] = $match;
//            }
//        }
        //$scheduleMatches = BasketMatch::scheduleMatchesByLidAndTime($lid);//从数据库获取 赛程

        try {
            $comboData = CommonTool::getComboData($name_en);
            $result["comboData"] = $comboData;
        } catch (\Exception $exception) {}

        $playerString = CommonTool::getPlayerData($sport, $lid, $season, $kind == 2 ? 1 : $kind);
        $data = json_decode($playerString, true);

        $result["sl"] = $sl;
        $result["data"] = $data;
        $result["westRanks"] = $westRanks;
        $result["eastRanks"] = $eastRanks;
        $result["season"] = $basketSeason;
        $result["seasons"] = $leagueData["seasons"];
        if (isset($leagueData["playoff"])) {
            $result["playoff"] = $leagueData["playoff"];
        }
        $result["scheduleMatches"] = $scheduleMatches;
        $result["start"] = $startTime;
        $result["end"] = $endTime;
        $result['title'] = '['.$sl->name.'直播]'.$sl->name.'免费在线直播观看_哪里可以看'.$sl->name.'直播网址-爱看球直播';
        return view("pc.subject.v2.basketball_detail", $result);
    }


    //=========================== 专题赛程 逻辑 开始 ===========================//


    /**
     * 篮球赛程接口
     * @param Request $request
     * @param $name_en
     * @param $param
     * @return \Illuminate\Http\JsonResponse
     */
    public function basketballSchedule(Request $request, $name_en, $param) {
        $callback = $request->input("callback");
        $params = explode("_", $param);
        $season = $params[0];
        $start = isset($params[1]) ? $params[1] : date("Y-m-d 00:00");
        $end = isset($params[2]) ? $params[2] : date("Y-m-d 23:59", strtotime("+2 days"));
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (!isset($sl) || $sl["sport"] != SubjectLeague::kSportBasketball) {
            return response()->json(["code"=>403]);
        }

        $startTime = strtotime($start);
        $endTime = strtotime($end);

        //判断接口天数是否大于N天
        $day = 7;
        if ($endTime - $startTime > (24 * 60 * 60 * $day) ) {
            $end = date("Y-m-d 23:59", strtotime("+$day days", $startTime));
        }

        $schedule = BasketMatch::scheduleMatchesByLidAndTime($sl["lid"], $season, $start, $end);

        $startTime = strtotime($start);
        $endTime = strtotime($end) + 60;

        $start = date("m-d", $startTime);
        $end = date("m-d", $endTime);
        if (!empty($callback)) {
            return response()->jsonp($callback, ["code"=>200, "schedule"=>$schedule, "start"=>$startTime, "end"=>$endTime, "startCn"=>$start, "endCn"=>$end]);
        } else {
            return response()->json(["code"=>200, "schedule"=>$schedule, "start"=>$startTime, "end"=>$endTime, "startCn"=>$start, "endCn"=>$end]);
        }
    }

    //=========================== 专题赛程 逻辑 结束 ===========================//





    //=====================================================//

    /**
     * 通过接口获取专题列表内容
     */
    public static function getSubjects() {
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
    public function getSubjectDetail($id) {
        $aiCon = new AikanQController();
        $data = $aiCon->subjectDetail(new Request(), $id)->getData();
        $data = json_encode($data);
        $subjects = json_decode($data, true);
        return $subjects;
    }

    //================================================静态化================================================//

    /**
     * 静态化专题列表json
     * @param Request $request
     */
    public function staticSubjectLeagues(Request $request) {
        $aiCon = new AikanQController();
        $data = $aiCon->subjectsData();
        $server_output = json_encode($data);
        if (!empty($server_output)) {
            Storage::disk("public")->put("static/json/pc/subject/leagues.json", $server_output);
        }
    }

    /**
     * 静态化专题终端json数据
     * @param Request $request
     * @param $slid
     */
    public function staticSubjectDetailJson(Request $request, $slid) {
        $akqCon = new AikanQController();
        $json = $akqCon->subjectDetail($request, $slid)->getData();
        $server_output = json_encode($json);
        if (!empty($server_output)) {
            Storage::disk("public")->put("/static/json/subject/" . $slid . ".json", $server_output);
        }
    }

    /**
     * 静态化专题终端页
     * @param Request $request
     * @param $name_en
     * @param $season
     */
    public function staticSubjectHtml(Request $request, $name_en, $season = "") {
        if ($season == "all") { //全部赛季
            $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
            $lid = $sl->lid;
            $sport = $sl->sport;
            if ($sport == 2) {
                $query = BasketSeason::query();
            } else {
                $query = Season::query();
            }
            $seasons = $query->where('lid', $lid)->orderBy('year', 'desc')->get();
            foreach ($seasons as $index=>$seasonItem) {
                $seasonName = $seasonItem->name;
                dump($seasonName);
                if ($index == 0) $seasonName = "";
                $this->onStaticSubjectHtml($request, $name_en, $seasonName);
            }
        } else {
            $this->onStaticSubjectHtml($request, $name_en, $season);
        }
    }

    private function onStaticSubjectHtml(Request $request, $name_en, $season = "") {
        $html = $this->detailV2($request, $name_en, $season);
        if (!empty($html)) {
            $mobile = new \App\Http\Controllers\Mobile\Subject\SubjectController();
            $mobile_html = $mobile->detailV2($request, $name_en, $season);
            if (empty($season)) {
                Storage::disk("public")->put("/www/$name_en/index.html", $html);
                Storage::disk("public")->put("/m/$name_en/index.html", $mobile_html);
            } else {
                Storage::disk("public")->put("/www/$name_en/".$season."/index.html", $html);
                Storage::disk("public")->put("/m/$name_en/".$season."/index.html", $mobile_html);
            }
        }
    }

    public function staticSubjectVideoDetailPc(SubjectVideoChannels $ch) {
        $video = $ch->video;
        $sl = SubjectLeague::query()->find($video['s_lid']);

        $html = $this->subjectVideoHtml($video, $ch, $sl);

        if (!empty($html)) {//静态化录像终端
            $patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $ch['id']);
            Storage::disk("public")->put('www/'.$patch, $html);
        }
    }

    public function staticSubjectVideoDetailM(SubjectVideoChannels $ch) {
        $video = $ch->video;
        $mCon = new \App\Http\Controllers\Mobile\Live\LiveController();
        $html = $mCon->subjectVideoDetailHtml($ch, $video);

        if (!empty($html)) {//静态化录像终端
            $patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $ch['id']);
            Storage::disk("public")->put('m/'.$patch, $html);
        }
    }

    public function staticSubjectVideoDetailMip(SubjectVideoChannels $ch) {
        $video = $ch->video;
        $mCon = new \App\Http\Controllers\Mip\Live\LiveController();
        $html = $mCon->subjectVideoDetailHtml($ch, $video);

        if (!empty($html)) {//静态化录像终端
            $patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $ch['id']);
            Storage::disk("public")->put('mip/'.$patch, $html);
        }
    }

    /**
     * 是否淘汰赛
     * @param $stageName
     * @return bool
     */
    protected function isKnockout($stageName) {
        $array = ["16强", "十六强", "半准决赛", "准决赛", "决赛"];
        return in_array($stageName, $array);
    }

}