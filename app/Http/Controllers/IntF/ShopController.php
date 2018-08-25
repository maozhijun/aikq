<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/26
 * Time: 0:01
 */

namespace App\Http\Controllers\IntF;


use App\Models\Match\MatchLive;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShopController extends Controller
{

    public function shopLives(Request $request) {

        $endDate = date('Y-m-d H:i', strtotime('+1 day'));
        $tempDate = date('Y-m-d', strtotime('+1 day'))." 10:00";
        if (strtotime($endDate) < strtotime($endDate)) {
            $endDate = $tempDate;
        }
        $startDate = date('Y-m-d H:i', strtotime('-3 hour'));

        $footballMatches = MatchLive::query()
            ->selectRaw('match_id as id, sport, count(channel.id) as count, max(mf.time) as time')
            ->leftJoin('match_live_channels as channel', 'match_lives.id', 'channel.live_id')
            ->leftJoin('matches as mf', 'mf.id', 'match_lives.match_id')
            ->where('match_lives.sport', MatchLive::kSportFootball)
            ->where('mf.time', '<=', $endDate)
            ->where('mf.time', '>=', $startDate)
            ->where('mf.status', '>=', 0)
            //去掉世界杯
            ->where('mf.lid', '<>', 57)
            ->whereIn('channel.platform', [1, 3])
            ->groupBy('match_id', 'sport')
            ->orderBy('time', 'asc')->take(5)->get();

        $basketMatches = MatchLive::query()
            ->selectRaw('match_id as id, sport, count(channel.id) as count, max(mf.time) as time')
            ->leftJoin('match_live_channels as channel', 'match_lives.id', 'channel.live_id')
            ->leftJoin('basket_matches as mf', 'mf.id', 'match_lives.match_id')
            ->where('match_lives.sport', MatchLive::kSportBasketball)
            ->where('mf.time', '<=', $endDate)
            ->where('mf.time', '>=', $startDate)
            ->where('mf.status', '>=', 0)
            ->whereIn('channel.platform', [1, 3])
            ->groupBy('match_id', 'sport')
            ->orderBy('time', 'asc')->take(5)->get();

        $matchLives = $footballMatches->merge($basketMatches)->sortBy('time')->take(5);

        $array = [];
        foreach ($matchLives as $ml) {
            $array[] = ['sport'=>$ml->sport, 'id'=>$ml->id, 'count'=>$ml->count, 'time'=>$ml->time];
        }
        return $array;
    }

}