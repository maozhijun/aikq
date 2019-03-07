<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/14
 * Time: 10:30
 */

namespace App\Models\LgMatch;


use Illuminate\Database\Eloquent\Model;

/**
 * 篮球积分排名
 * Class BasketScore
 * @package App\Models\Match
 */
class BasketScore extends Model
{
    const kZoneWest = 0, kZoneEast = 1;
    public $connection = "match";
    public $timestamps = false;

    /**
     * 获取篮球排名数组
     * @param $lid
     * @param int $zone
     * @return array
     */
    public static function getScoresByLid($lid, $zone = 0) {
        $season = BasketSeason::query()->where("lid", $lid)->orderBy("year", "desc")->first();

        $array = [];
        if(isset($season)) {
            $query = self::query();
            $query->join('basket_teams', 'basket_teams.id', '=', 'basket_scores.tid');
            $query->where('basket_scores.lid', $lid);
            $query->where('basket_scores.zone', $zone);
            $query->where('basket_scores.season', $season->name);
            $query->orderBy('basket_scores.rank');
            $query->selectRaw("basket_teams.name_china, basket_teams.icon, basket_scores.*");
            $scores = $query->get();

            foreach ($scores as $score) {
                $win = $score->win;
                $lose = $score->lose;
                $total = $win + $lose;
                $win_p = $total > 0 ? round($win / $total, 2) * 100 : 0;
                $array[] = ['tid'=>$score->tid, 'lid'=>$score->lid, 'sport'=>2, "icon"=>BasketTeam::getIcon($score->icon), "win_diff"=>$score["win_diff"],
                    "home_bat_w"=>$score->home_bat_w, "home_bat_l"=>$score->home_bat_l, "away_bat_w"=>$score->away_bat_w, "away_bat_l"=>$score->away_bat_l,
                    "goal"=>$score->goal, "fumble"=>$score->fumble, "win_status"=>$score->win_status,
                    'name' => $score->name_china, 'win' => $win, 'lose' => $lose, 'rank' => $score->rank, 'win_p' => $win_p];
            }
        }
        return $array;
    }

}