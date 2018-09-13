<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/11
 * Time: 11:15
 */

namespace App\Http\Controllers\Sync;


use App\Http\Controllers\Controller;
use App\Models\LgMatch\Match;
use Illuminate\Http\Request;

class FootballController extends Controller
{

    /**
     * 同步足球比赛
     * @param Request $request
     */
    public function syncMatch(Request $request) {
        //同步前一天到以后的比赛
        $date = date('Y-m-d', strtotime('-1 days'));
        //$date2 = date('Y-m-d', strtotime('-8 days'));
        $query = Match::query()->where('time', '>=', $date);
        //$query->where('time', '<=', $date);
        $lgMatches = $query->get();
        foreach ($lgMatches as $lgMatch) {
            $id = $lgMatch->id;

            $aMatch = \App\Models\Match\Match::query()->find($id);
            if (!isset($aMatch)) {
                $aMatch = new \App\Models\Match\Match();
                $aMatch->id = $id;
            }

            $this->copyMatch($lgMatch, $aMatch);

            $aMatch->save();
        }
    }

    /**
     * 更新足球比赛信息
     * @param Request $request
     */
    public function updateMatch(Request $request) {
        $max = \App\Models\Match\Match::query()->selectRaw('max(updated_at) as updated_at')->first();
        $lastUpdate = date('Y-m-d H:i:s', strtotime('-10 hours'));
        if (isset($max)) {
            $lastUpdate = $max->updated_at;
        }
        $lgMatches = Match::query()->where('updated_at', '>=', $lastUpdate)->get();

        foreach ($lgMatches as $lgMatch) {
            $id = $lgMatch->id;

            $aMatch = \App\Models\Match\Match::query()->find($id);
            if (!isset($aMatch)) {
                $aMatch = new \App\Models\Match\Match();
                $aMatch->id = $id;
            }

            $this->copyMatch($lgMatch, $aMatch);

            $aMatch->save();
        }
        dump("本次更新时间为：" . $lastUpdate . ",更新条数：" . count($lgMatches));
    }


    /**
     * 复制字段
     * @param Match $lgMatch
     * @param \App\Models\Match\Match $aMatch
     */
    public function copyMatch(Match $lgMatch, \App\Models\Match\Match &$aMatch) {
        $aMatch->lid = $lgMatch->lid;
        $aMatch->lsid = $lgMatch->lsid;
        $aMatch->season = $lgMatch->season;
        $aMatch->stage = $lgMatch->stage;
        $aMatch->round = $lgMatch->round;
        $aMatch->group = $lgMatch->group;
        $aMatch->time = $lgMatch->time;
        $aMatch->timehalf = $lgMatch->timehalf;
        $aMatch->status = $lgMatch->status;
        $aMatch->hid = $lgMatch->hid;
        $aMatch->aid = $lgMatch->aid;
        $aMatch->hname = $lgMatch->hname;
        $aMatch->aname = $lgMatch->aname;
        $aMatch->hscore = $lgMatch->hscore;
        $aMatch->ascore = $lgMatch->ascore;
        $aMatch->hscorehalf = $lgMatch->hscorehalf;
        $aMatch->ascorehalf = $lgMatch->ascorehalf;
        $aMatch->hrank = $lgMatch->hrank;
        $aMatch->arank = $lgMatch->arank;
        $aMatch->neutral = $lgMatch->neutral;
        $aMatch->genre = $lgMatch->genre;
        $aMatch->is_odd = $lgMatch->is_odd;
        $aMatch->has_lineup = $lgMatch->has_lineup;
        $aMatch->inflexion = $lgMatch->inflexion;
        $aMatch->same_odd = $lgMatch->same_odd;
        $aMatch->win_id = $lgMatch->win_id;
        $aMatch->stage_win_id = $lgMatch->stage_win_id;
        $aMatch->win_hname = $lgMatch->win_hname;
        $aMatch->win_aname = $lgMatch->win_aname;
        $aMatch->win_lname = $lgMatch->win_lname;
        $aMatch->lname = $lgMatch->lname;
        $aMatch->betting_num = $lgMatch->betting_num;
        $aMatch->filter_analyse = $lgMatch->filter_analyse;
        $aMatch->live_url = $lgMatch->live_url;
        $aMatch->pre_hscore = $lgMatch->pre_hscore;
        $aMatch->pre_ascore = $lgMatch->pre_ascore;
        $aMatch->created_at = $lgMatch->created_at;
        $aMatch->updated_at = $lgMatch->updated_at;
    }
}