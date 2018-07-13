<?php

namespace App\Models\Match;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{

//    public $connection = "match";

    public $timestamps = false;

    protected $hidden = ['create_at', "spider_at"];

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }

    public function leagueSeasons()
    {
        return $this->hasMany('App\Models\Match\Season', 'lid', 'id');
    }

    public function leagueSeason()
    {
        $year = date('Y');
        $season = $this->leagueSeasons
            ->where('year', $year)
            ->first();
        if (empty($season)) {
            $season = new Season();
        }
        return $season;
    }

    /**
     * 五大联赛id
     */
    public static function getFiveLids() {
        return [31,26,29,8,11];
    }

    public static function getLeagueNameByMid($mid){
        $match = Match::query()->find($mid);
        $league = $match->league;
        return $league['name'];
    }
}
