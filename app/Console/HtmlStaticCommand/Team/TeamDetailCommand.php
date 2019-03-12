<?php
namespace App\Console\HtmlStaticCommand\Team;

use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\Team\TeamController;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\Match\MatchLive;
use App\Models\Subject\SubjectLeague;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/25
 * Time: 17:32
 */
class TeamDetailCommand extends Command
{
    const REDIS_KEY_SAVE_LIDS = 'team_detail_save_lids';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'team_detail:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '球队终端';

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
    public function handle()
    {
        $type = $this->argument('type');
        if ($type == "lives") {
            self::fromLivesData();
        } else if ($type == "subject") {
            $sleagues = SubjectLeague::getAllLeagues();
            foreach ($sleagues as $sl) {
                dump($sl->sport. "_". $sl->lid);
                self::fromLeagueData($sl->sport, $sl->lid);
            }
        } else if (starts_with($type, "lid")) {
            $sport = substr($type, 3, 1);
            $lid = substr($type, 4);
            self::fromLeagueData($sport, $lid);
        } else if (starts_with($type, "mid")) {
            $sport = substr($type, 3, 1);
            $mid = substr($type, 4);
            self::fromMid($sport, $mid);
        }
    }

    protected static function fromLivesData()
    {
        $lives = json_decode(Storage::get("/public/static/json/pc/lives.json"), true);
        foreach ($lives['matches'] as $data => $matches) {
            foreach ($matches as $key => $match) {
                list($sport, $mid) = explode("_", $key, 2);
                dump($sport, $mid);
                self::onTeamDetailStaticByMid($sport, $mid);
            }
        }
    }

    protected static function fromMid($sport, $mid)
    {
        self::onTeamDetailStaticByMid($sport, $mid);
    }

    protected static function fromLeagueData($sport, $lid)
    {
        $hasLeagueData = false;
        if ($sport == MatchLive::kSportBasketball) {
            $season = BasketSeason::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $query = BasketScore::query();
        } else {
            $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $query = Score::query();
        }
        if (isset($season)) {
            AikanQController::leagueRankStatic($sport, $lid);
            $scores = $query->select('tid')->where('lid', $lid)->where('season', $season->name)->get()->unique('tid');
            foreach ($scores as $score) {
                self::onTeamDetailStaticByTid($sport, $lid, $score->tid);
            }
            return count($scores) > 0;
        }
        return $hasLeagueData;
    }

    public static function onTeamDetailStaticByMid($sport, $mid)
    {
        if ($sport == MatchLive::kSportBasketball) {
            $match = \App\Models\LgMatch\BasketMatch::query()->find($mid);
        } else {
            $match = \App\Models\LgMatch\Match::query()->find($mid);
        }
        if (isset($match)) {
            $lid = $match->lid;
            AikanQController::leagueRankStatic($sport, $lid);

            $savedLids = Redis::get(self::REDIS_KEY_SAVE_LIDS);
            $tempSportLid = $sport."_".$lid;
            $lids = [];
            if ($savedLids && strlen($savedLids) > 0) {
                $lids = explode(",", $savedLids);
            }
            $hasLeagueData = false;
            if (!in_array($tempSportLid, $lids)) {
                $hasLeagueData = self::fromLeagueData($sport, $lid);
                $lids[] = $tempSportLid;
                Redis::set(self::REDIS_KEY_SAVE_LIDS, implode(",", $lids));
            }
            if (!$hasLeagueData) {
                $hid = $match->hid;
                self::onTeamDetailStaticByTid($sport, $lid, $hid);
                $aid = $match->aid;
                self::onTeamDetailStaticByTid($sport, $lid, $aid);
            }
        }
    }

    public static function onTeamDetailStaticByTid($sport, $lid, $tid)
    {
        if (isset($tid) && strlen($tid) > 0) {
//            $pcData = AikanQController::teamDetailData($sport, $lid, $tid);
            $webData = AikanQController::teamDetailData($sport, $lid, $tid, true);
            $path = CommonTool::getTeamDetailPath($sport, $lid, $tid);

            $sl = SubjectLeague::where('lid',$lid)->first();
            if (isset($sl)){
                $name_en = $sl->name_en;
            }
            else{
                $name_en = 'other';
            }
            //pc站
            TeamController::detailStatic($name_en, $tid, $sport);
            $con = new TeamController();
            $con->staticVideoHtml(new Request(),$sport,$name_en,$tid,1);
            $con->staticRecordHtml(new Request(),$sport,$name_en,$tid,1);
            $con->staticNewsHtml(new Request(),$sport,$name_en,$tid,1);
            //web站
            \App\Http\Controllers\Mobile\Team\TeamController::detailStatic($webData, $path);
            //mip站
            \App\Http\Controllers\Mip\Team\TeamController::detailStatic($webData, $path);
        }
    }
}