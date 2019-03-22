<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/3/18
 * Time: 18:02
 */

namespace App\Console\Subject;


use App\Console\HtmlStaticCommand\BaseCommand;
use App\Console\LiveDetailCommand;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\Common\LeagueDataTool;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\Team\TeamController;
use App\Models\LgMatch\BasketLeague;
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\Match;
use App\Models\LgMatch\Season;
use App\Models\Subject\SubjectLeague;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class SubjectAllCommand extends BaseCommand
{

    protected  function command_name()
    {
        return "subject_all_static";
    }

    protected  function description()
    {
        return "专题所有页面静态化";
    }

    public function handle()
    {
        $param = $this->argument('type');
        $params = explode("_", $param);

        $type = $params[0];
        if (!isset($params[1])) {
            echo "type 错误";
            return;
        }
        $name_en = $params[1];
        $static = isset($params[2]) && $params[2] == 1 ? true : false;

        switch ($type) {
            case "pc":
                $this->staticPc($name_en, $static);
                break;

            case "mobile":

                break;

            case "all":

                break;

            case "football":
                $this->staticFinalFootballMatchDetail($name_en);
                break;

            case "basketball":
                $this->staticFinalBasketballMatchDetail($name_en);
                break;
            case "index":
                $cache = Storage::get('/public/static/json/pc/lives.json');
                $json = json_decode($cache, true);
                if (!isset($json["matches"])) {
                    echo "没有数据";
                    return;
                }
                $matches = $json["matches"];
                $sls = SubjectLeague::query()->get();
                $slArray = [];
                foreach ($sls as $sl) {
                    $sport = $sl["sport"];
                    $lid = $sl["lid"];
                    $slArray[$sport."_".$lid] = $sl["name_en"];
                }
                $wwwUrl = "http://www.aikanqiu.com";
                $host = "http://cms.aikanqiu.com";

                $teamCache = [];
                foreach ($matches as $date=>$array) {
                    foreach ($array as $key=>$item) {
                        $sport = $item["sport"];
                        $lid = $item["lid"];
                        $hid = $item["hid"];
                        $aid = $item["aid"];
                        $mid = $item["mid"];
                        $hname = $item["hname"];
                        $aname = $item["aname"];
                        $msg = $hname . " VS " . $aname . " " . $item["time"];

                        $slKey = $sport."_".$lid;
                        $name_en = isset($slArray[$slKey]) ? $slArray[$slKey] : "other";

                        $liveKey = $hid > $aid ? ($sport."_".$lid."_".$hid."_".$aid) : ($sport."_".$lid."_".$aid."_".$hid);
                        $liveCache = Redis::get($liveKey);
                        if (empty($liveCache)) {

                            //$liveDetailUrl = $wwwUrl . CommonTool::getLiveDetailUrlByParam($name_en, $sport, $hid, $aid, $mid);
                            $code = 0;//LiveDetailCommand::getUrlCode($liveDetailUrl);
                            if ($code == 200) {
                                echo "已存在 直播终端  " . $msg . " \n";
                            } else {
                                //不存在则静态化
                                $url = $host."/live/cache/match/detail_id/".$mid."/".$sport;
                                Controller::execUrl($url, 20);
                                echo "静态化直播终端 $msg $url \n";
                                sleep(1);
                            }
                            Redis::setEx($liveKey, 60 * 60, 1);
                        }

                        if ($name_en != "other" && $static) {
                            $this->indexStaticTeam($sport, $name_en, $hid, $hname);
                            $this->indexStaticTeam($sport, $name_en, $aid, $aname);
                        }
                    }
                }
                break;
        }
    }

    protected function indexStaticTeam($sport, $name_en, $tid, $tName) {
        $key = $this->getTeamKey($name_en, $tid);
        $cache = Redis::get($key);
        if (isset($cache)) {
            return;
        }
        $start = time();
        $host = "http://cms.aikanqiu.com";
        if ($name_en == "other") {
            $url = $host . "/static/team_index/". $sport . "/" . $name_en . "/" . $tid . "/1";
        } else {
            $url = $host . "/static/team_all/". $sport . "/" . $name_en . "/" . $tid . "/1";
        }
        Controller::execUrl($url, 15);
        echo "  静态化球队 $tName 耗时：". (time() - $start) ." " . $url . "\n";
        Redis::setEx($key, 60 * 60, 1);
        sleep(1);
    }

    public function staticPc($name_en, $staticTeam = false) {
        $start = time();
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (!isset($sl)) {
            echo "没有专题";
            return;
        }
        $sport = $sl["sport"];
        $lid = $sl["lid"];
        $host = "http://cms.aikanqiu.com";
        if ($sport == SubjectLeague::kSportBasketball) {
            //篮球
            $seasons = BasketSeason::query()->where("lid", $lid)->orderByDesc("year")->get();
            $league = BasketLeague::query()->find($lid);
        } else {
            //足球
            $seasons = Season::query()->where("lid", $lid)->orderByDesc("year")->get();
            $league = \App\Models\LgMatch\League::query()->find($lid);
        }

        foreach ($seasons as $index=>$season) {
            $name = $season["name"];
            $type = $league["type"];

            $liveStart = time();
            $url = $host."/static/subject/detail/".$name_en.($index > 0 ? ("/".$name."/") : "/");
            Controller::execUrl($url);//静态化 专题
            echo "静态化专题 $name_en $name ：" . $url . " 耗时 ".(time() - $liveStart)." \n ";

            if (!$staticTeam) {
                echo "不静态化 球队终端 \n";
                continue;
            }
            if ($type == 1) {
                $matches = $this->getMatchesFromSchedule($sport, $lid, $name);
                echo "一共 " . count($matches) . " 场比赛 \n";
            } else {
                $matches = $this->getMatchesFromCup($sport, $lid, $name);
                echo "一共 " . count($matches) . " 场比赛 \n";
            }
            $this->staticMatchesTeam($matches, $sport, $name_en);
        }
        echo "静态化完成 耗时：" . (time() - $start) . "秒";
    }


    public function staticMatchesTeam($matches, $sport, $name_en) {
        foreach ($matches as $match) {
            $hid = isset($match["hid"]) ?$match["hid"] : "";
            $aid = isset($match["aid"]) ?$match["aid"] : "";
            $hname = $match["hname"];
            $aname = $match["aname"];

            echo "静态化 " . $match["hname"] . " vs " . $match["aname"] . " " . date("Y-m-d H:i", $match["time"]) . "\n";
            $this->staticTeam($sport, $name_en, $hid, $hname);
            $this->staticTeam($sport, $name_en, $aid, $aname);
            echo "\n";
        }
    }

    public function staticTeam($sport, $name_en, $tid, $tName) {
        if (empty($tid)) return;
        $start = time();

        $key = $this->getTeamKey($name_en, $tid);
        $cache = Redis::get($key);
        if (isset($cache)) {
            echo $tName . "球队已经静态化过了  耗时：" . (time() - $start) . " \n";
            return;
        }
        //判断是否有静态化页面

        //$wwwUrl = "http://www.aikanqiu.com".CommonTool::getTeamDetailUrlByNameEn($name_en, $sport, $tid);
        //$code = LiveDetailCommand::getUrlCode($wwwUrl);
        //if ($code == 200) {
            //echo "已存在静态化页面 $wwwUrl \n";
            //return;
        //}

        $host = "http://cms.aikanqiu.com";
        if ($name_en == "other") {
            $url = $host . "/static/team_index/". $sport . "/" . $name_en . "/" . $tid . "/1";
        } else {
            $url = $host . "/static/team_all/". $sport . "/" . $name_en . "/" . $tid . "/1";
        }
        Controller::execUrl($url, 15);
        echo "  静态化球队 $tName 耗时：". (time() - $start) ." " . $url . "\n";
        Redis::setEx($key, 60 * 60, "1");
        sleep(1);
    }


    public function getMatchesFromSchedule($sport, $lid, $season) {
        $leagueData = LeagueDataTool::getLeagueDataBySeasonNew($sport, $lid, $season);
        $matchArray = [];

        if (isset($leagueData["schedule"])) {//全部赛程 组装成简单的数组
            foreach ($leagueData["schedule"] as $round=>$matches) {
                foreach ($matches as $match) {
                    $matchArray[] = $match;
                }
            }
        }
        return $matchArray;
    }

    public function getMatchesFromCup($sport, $lid, $season) {
        $leagueData = LeagueDataTool::getLeagueDataBySeasonNew($sport, $lid, $season);
        $matchArray = [];

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
                    $matchArray = array_merge($matchArray, $matches);
                } else if (isset($leagueStage["combo"])){//淘汰赛
                    $combos = $leagueStage["combo"];
                    foreach ($combos as $key=>$combo) {
                        foreach ($combo["matches"] as $m) {
                            $matchArray[] = $m;
                        }
                    }
                } else if (isset($leagueStage["groupMatch"])) {//分组赛
                    $groupMatches = $leagueStage["groupMatch"];
                    foreach ($groupMatches as $g=>$groupMatch) {
                        foreach ($groupMatch["matches"] as $match) {
                            $matchArray[] = $match;
                        }
                    }
                }
            }
        }

        return $matchArray;
    }


    protected function staticFinalFootballMatchDetail($lid) {
        $query = Match::query();
        $query->join("stages", "stages.id", "=", "matches.stage");
        $query->where("stages.name", "=", "决赛");
        $query->where("stages.lid", "=", $lid);
        $query->select("matches.*");
        $matches = $query->get();
        $cache = [];
        foreach ($matches as $match) {

            $hid = $match["hid"];
            $aid = $match["aid"];
            if ($hid > $aid) {
                $key = $hid . "_" . $aid;
            } else {
                $key = $aid . "_" . $hid;
            }
            if (isset($cache[$key])) {
                echo $key . " \n";
                continue;
            }
            $cache[$key] = 1;

            $url = "http://cms.aikanqiu.com/live/cache/match/detail_id/".$match["id"]."/1";
            echo "静态化终端 " . $url . " \n";
            Controller::execUrl($url, 10);
        }

    }

    protected function staticFinalBasketballMatchDetail($lid) {
        $query = BasketMatch::query();
        $query->join("basket_stages", "basket_stages.id", "=", "basket_matches.stage");
        $query->where("basket_stages.name", "=", "总决赛");
        $query->where("basket_stages.lid", "=", $lid);
        $query->select("basket_matches.*");
        $matches = $query->get();
        $cache = [];
        foreach ($matches as $match) {
            $hid = $match["hid"];
            $aid = $match["aid"];
            if ($hid > $aid) {
                $key = $hid . "_" . $aid;
            } else {
                $key = $aid . "_" . $hid;
            }
            if (isset($cache[$key])) {
                echo $key . " \n";
                continue;
            }
            $cache[$key] = 1;
            $url = "http://cms.aikanqiu.com/live/cache/match/detail_id/".$match["id"]."/2";
            dump($url);
            Controller::execUrl($url, 10);
        }
    }

    protected function getTeamKey($name_en, $tid) {
        return "team".$name_en."_".$tid;
    }

}