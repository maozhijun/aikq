<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    //
    protected $connection = 'match';
    public $timestamps = false;

    /**
     * 获取球队信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne('App\Models\Match\Team', 'id', 'tid');
    }

    public function lastRoundMatches($count = 6) {
        $lid = $this->lid;
        $season = $this->season;
        $kind = $this->kind;
        $tid = $this->tid;

        $query = Match::query()->where('lid', $lid)->where('season', $season);
        if ($kind == 1) {
            $query->where('hid', $tid);
        } elseif ($kind == 2){
            $query->where('aid', $tid);
        } else {
            $query->where(function ($q) use($tid){
                $q->where('hid', $tid)->orWhere('aid', $tid);
            });
        }
        $query->where('status', -1)
            ->orderBy('time', 'desc');
        if ($count > 0) {
            $query->take($count);
        }
        return $query->get();
    }

    /**
     * 足球积分榜
     */
    public static function getFootballScores($lid, $kind = null, $count = -1) {
        $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();

        $scores = array();
        if (isset($season)) {
            $query = Score::query()->leftJoin("teams", "scores.tid", "teams.id")
                ->select("scores.*", "teams.name as tname", "teams.icon as ticon")
                ->addSelect('teams.lg_icon as tlg_icon')
                ->where("lid", $lid)
                ->where("season", $season->name);
            if (isset($kind) && $kind > 0) {
                $query->where("kind", $kind);
            } else {
                $query->whereNull("kind");
            }
            if ($count > 0) {
                $query->take($count);
            }
            $scores = $query->orderBy("rank")->get();
        }
        return $scores;
    }

    /**
     * 获取足球杯赛积分榜
     * @param $lid
     * @return array
     */
    public static function footballCupScores($lid) {
        $array = [];
        $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
        if ($season) {
            $year = $season->name;//获取最后的赛季
            $query = self::query();
            $query->join('teams', 'teams.id', '=', 'scores.tid');
            $query->where('lid', $lid);
            $query->where('season', $year);
            $query->orderBy('group')->orderByDesc('score');
            $query->orderByRaw('(goal - fumble) desc');
            $query->selectRaw('teams.name as tname, teams.icon, scores.*');
            $scores = $query->get();
            foreach ($scores as $score) {
                $g = $score->group;
                $array[$g][] = ['tid' => $score->tid, 'lid' => $score->lid, 'sport' => 1, "icon"=>$score->icon,
                    'group' => $g, 'score' => $score->score, 'win' => $score->win, 'draw' => $score->draw, 'name' => $score->tname,
                    'lose' => $score->lose, 'count' => $score->count, 'goal' => $score->goal, 'fumble' => $score->fumble];
            }
        }
        return $array;
    }

    /**
     * 获取足球杯赛积分榜
     * @param $lid
     * @return array
     */
    public static function footballCupScoresTemp($lid) {
        $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
        $year = $season->name;//获取最后的赛季
        $stage = Stage::query()->where('season', $year)->where('lid', $lid)->whereNotNull('group')->first();
        if (!isset($stage)) {
            return null;
        }
        $group = $stage->group;
        $group_len = strlen($group);
        $groups = [];
        for ($g_index = 0; $g_index < $group_len; $g_index++) {
            $groups[] = substr($group, $g_index, 1);
        }
        foreach ($groups as $group) {
            self::footballCupGroupScoreTemp($lid, $season, $group);
        }
        return [];
    }

    /**
     * 杯赛获取分组排名
     * @param $lid     杯赛ID
     * @param $season  赛季
     * @param $group   分组
     * @return array   数组
     */
    protected static function footballCupGroupScoreTemp($lid, $season, $group) {
        //由于数据混乱 暂时使用 group 获取
        $query = self::query();
        $query->join('teams', 'teams.id', '=', 'scores.tid');
        $query->where('lid', $lid);
        $query->where('season', $season);
        $query->where('group', $group);
        $query->groupBy('scores.tid');
        $query->selectRaw('teams.name, max(count) as count, max(score) as score, max(win) as win, max(lose) as lose ');
        $scores = $query->get()->toArray();
        return $scores;
    }



}
