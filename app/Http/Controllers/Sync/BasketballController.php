<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/11
 * Time: 11:15
 */

namespace App\Http\Controllers\Sync;


use App\Http\Controllers\Controller;
use App\Models\LgMatch\BasketMatch;
use Illuminate\Http\Request;

class BasketballController extends Controller
{

    /**
     * 同步足球比赛
     * @param Request $request
     */
    public function syncMatch(Request $request) {
        //同步前一天到以后的比赛
        $date = date('Y-m-d H:i:s', strtotime('-1 days'));
        $lgMatches = BasketMatch::query()->where('time', '>=', $date)->get();

        foreach ($lgMatches as $lgMatch) {
            $id = $lgMatch->id;

            $aMatch = \App\Models\Match\BasketMatch::query()->find($id);
            if (!isset($aMatch)) {
                $aMatch = new \App\Models\Match\BasketMatch();
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
        $max = \App\Models\Match\BasketMatch::query()->selectRaw('max(updated_at) as updated_at')->first();
        $lastUpdate = date('Y-m-d H:i:s', strtotime('-2 hours'));
        if (isset($max)) {
            $lastUpdate = $max->updated_at;
        }
        $lgMatches = BasketMatch::query()->where('updated_at', '>', $lastUpdate)->get();

        foreach ($lgMatches as $lgMatch) {
            $id = $lgMatch->id;

            $aMatch = \App\Models\Match\BasketMatch::query()->find($id);
            if (!isset($aMatch)) {
                $aMatch = new \App\Models\Match\BasketMatch();
                $aMatch->id = $id;
            }

            $this->copyMatch($lgMatch, $aMatch);

            $aMatch->save();
        }
        dump("本次更新时间为：" . $lastUpdate . ",更新条数：" . count($lgMatches));
    }

    /**
     *
     * @param BasketMatch $lgMatch
     * @param \App\Models\Match\BasketMatch $aMatch
     */
    public function copyMatch(BasketMatch $lgMatch, \App\Models\Match\BasketMatch &$aMatch) {
        $aMatch->lid = $lgMatch->lid;
        $aMatch->win_lname = $lgMatch->win_lname;
        $aMatch->lname = $lgMatch->lname;
        $aMatch->betting_num = $lgMatch->betting_num;
        $aMatch->season = $lgMatch->season;
        $aMatch->stage = $lgMatch->stage;
        $aMatch->group = $lgMatch->group;
        $aMatch->time = $lgMatch->time;
        $aMatch->timehalf = $lgMatch->timehalf;
        $aMatch->status = $lgMatch->status;
        $aMatch->live_time_str = $lgMatch->live_time_str;
        $aMatch->hid = $lgMatch->hid;
        $aMatch->aid = $lgMatch->aid;
        $aMatch->hname = $lgMatch->hname;
        $aMatch->aname = $lgMatch->aname;
        $aMatch->betting_hname = $lgMatch->betting_hname;
        $aMatch->betting_aname = $lgMatch->betting_aname;
        $aMatch->short_hname = $lgMatch->short_hname;
        $aMatch->short_aname = $lgMatch->short_aname;
        $aMatch->hscore = $lgMatch->hscore;
        $aMatch->ascore = $lgMatch->ascore;
        $aMatch->hscore_1st = $lgMatch->hscore_1st;
        $aMatch->ascore_1st = $lgMatch->ascore_1st;
        $aMatch->hscore_2nd = $lgMatch->hscore_2nd;
        $aMatch->ascore_2nd = $lgMatch->ascore_2nd;
        $aMatch->hscore_3rd = $lgMatch->hscore_3rd;
        $aMatch->ascore_3rd = $lgMatch->ascore_3rd;
        $aMatch->hscore_4th = $lgMatch->hscore_4th;
        $aMatch->ascore_4th = $lgMatch->ascore_4th;
        $aMatch->h_ot = $lgMatch->h_ot;
        $aMatch->a_ot = $lgMatch->a_ot;
        $aMatch->neutral = $lgMatch->neutral;
        $aMatch->is_odd = $lgMatch->is_odd;
        $aMatch->win_id = $lgMatch->win_id;
        $aMatch->created_at = $lgMatch->created_at;
        $aMatch->updated_at = $lgMatch->updated_at;
    }

}