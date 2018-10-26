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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/25
 * Time: 17:32
 */
class TeamDetailCommand extends Command
{
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
            $this->fromLivesData();
        } else if (starts_with($type, "lid")) {
            $sport = substr($type, 3, 1);
            $lid = substr($type, 4);
            $this->fromLeagueData($sport, $lid);
        } else if (starts_with($type, "mid")) {
            $sport = substr($type, 3, 1);
            $mid = substr($type, 4);
            $this->fromMid($sport, $mid);
        }
    }

    protected function fromLivesData()
    {
        $lives = json_decode(Storage::get("/public/static/json/lives.json"), true);
        foreach ($lives['matches'] as $data => $matches) {
            foreach ($matches as $key => $match) {
                list($sport, $mid) = explode("_", $key, 2);
                self::onTeamDetailStaticByMid($sport, $mid);
            }
        }
    }

    protected function fromMid($sport, $mid)
    {
        self::onTeamDetailStaticByMid($sport, $mid);
    }

    protected function fromLeagueData($sport, $lid)
    {
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
        }
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

            $hid = $match->hid;
            self::onTeamDetailStaticByTid($sport, $lid, $hid);
            $aid = $match->aid;
            self::onTeamDetailStaticByTid($sport, $lid, $aid);
        }
    }

    public static function onTeamDetailStaticByTid($sport, $lid, $tid)
    {
        if (isset($tid) && strlen($tid) > 0) {
            $data = AikanQController::teamDetailData($sport, $lid, $tid);
            $path = CommonTool::getTeamDetailPath($sport, $lid, $tid);

            //pc站
            TeamController::detailStatic($data, $path);
            //web站
            \App\Http\Controllers\Mobile\Team\TeamController::detailStatic($data, $path);
            //mip站
            \App\Http\Controllers\Mip\Team\TeamController::detailStatic($data, $path);
        }
    }
}