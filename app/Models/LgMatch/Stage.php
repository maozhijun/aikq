<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    public $connection = "match";
    public $timestamps = false;

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }

    public static function getStages($lid, $season) {
        $query = self::query();
        $query->where("lid", $lid);
        $query->where("season", $season);
        $query->orderBy("status")->orderBy("id");
        return $query->get();
    }

}
