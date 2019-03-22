<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/3/15
 * Time: 16:53
 */

namespace App\Console;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\Score;
use App\Models\Subject\SubjectLeague;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

/**
 * 用户本机操作线上球队静态化
 * Class LocalhostTeamCommand
 * @package App\Console
 */
class SubjectTeamCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self_team:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题球队静态化';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $start = time();
        $type = $this->argument('type');
        echo "静态化 $type 球队终端开始 \n";

        $params = explode("_", $type);
        $name_en = $params[0];
        $year = isset($params[1]) ? $params[1] : "";

        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (!isset($sl)) {
            echo "没有专题 $name_en \n";
            return;
        }

        $sport = $sl["sport"];
        $lid = $sl["lid"];
        $leagueType = $sl["type"];

        if ($sport == SubjectLeague::kSportFootball) {
            $scores = [];
            if ($leagueType == 1) {
                $data = Score::getFootballScores($lid, $year);
                foreach ($data as $score) {
                    $tid = $score["tid"];
                    $name = $score["tname"];
                    $scores[] = ["tid"=>$tid, "name"=>$name];
                }
            } else {
                $data = Score::footballCupScores($lid, $year);
                foreach ($data as $group=>$rank) {
                    foreach ($rank as $score) {
                        $tid = $score["tid"];
                        $name = $score["name"];
                        $scores[] = ["tid"=>$tid, "name"=>$name];
                    }
                }
            }

        } else {
            $westRanks = BasketScore::getScoresByLid($lid, BasketScore::kZoneWest, $year);
            $eastRanks = BasketScore::getScoresByLid($lid, BasketScore::kZoneEast, $year);
            $scores = [];
            $scores = array_merge($scores, $westRanks, $eastRanks);
        }
        echo "总共有" . count($scores) . "支球队 \n";
        foreach ($scores as $index=>$score) {
            $tid = $score["tid"];
            $name = $score["name"];
            $key = $sport."_".$lid."_".$tid;
            $cache = Redis::get($key);
            if (!empty($cache)) {
                echo "$index  $type $name 已静态化 \n";
                continue;
            }
            Redis::setEx($key, 60 * 60 * 6, "1");
            $url = env('CMS_URL') . "/static/team_index/". $sport . "/" . $name_en . "/" . $tid . "/1";
            $teamStart = time();
            Controller::execUrl($url, 10);
            echo "$index  静态化 $type $name  耗时：". (time() - $teamStart) . " $url \n";
        }
        //http://cms.aikanqiu.com/static/team_index/ sport / 专题name_en / 球队id /1
        echo "静态化结束 耗时 ". (time() - $start) . " 秒";
    }



}