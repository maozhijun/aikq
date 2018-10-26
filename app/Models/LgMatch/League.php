<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{

    public $connection = "match";

    public $timestamps = false;

    protected $hidden = ['create_at', "spider_at"];

    public function leagueSeasons()
    {
        return $this->hasMany('App\Models\LgMatch\Season', 'lid', 'id');
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
