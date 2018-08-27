<?php

namespace App\Models\Match;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
//    public $connection = "match";
    public $timestamps = false;

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }

    public static function getIcon($icon) {
        return (isset($icon) && strlen($icon) > 0) ? $icon : (env('CDN_URL') . '/img/pc/icon_teamDefault.png');
    }

    public static function getLgIcon($lgIcon) {
        $icon = (isset($lgIcon) && strlen($lgIcon) > 0) ? $lgIcon : '/img/icon_team_default.png';
        return env('CDN_URL') . $icon;
    }

    public static function getIconById($tid) {
        $team = self::query()->find($tid);
        $icon = isset($team) ? $team->icon : "";
        return (isset($icon) && strlen($icon) > 0) ? $icon : (env('CDN_URL') . '/img/pc/icon_teamDefault.png');
    }

    /**
     * 获取球队联赛最后一场比赛
     * @param $id
     * @time 比赛时间
     * @return mixed
     */
    public static function getLeagueMatch($id,$time = null){
        $resultMatch = null;
        $resultLeague = null;
        $query = Match::query();
        $query->where(function ($q) use($id){
            $q->where('hid',$id)
                ->orwhere('aid',$id);
        });

        if(is_null($time)) {
            $time = date("Y-m-d H:i:s");
        }

        $query->where('time','<=',$time);
        $query->groupBy('lid');
        $query->selectRaw('max(id) as id, lid ,max(time) as time');
        $query->orderBy('time','desc');
        $matches = $query->take(5)->get();
        foreach ($matches as $match){
            $league = $match->league;
            if(isset($league) && $league->type == 1){
                $resultMatch = Match::query()->find($match->id);
                $resultLeague = $league;
                break;
            }
        }
        return array('match'=>$resultMatch,'league'=>$resultLeague);
    }
}
