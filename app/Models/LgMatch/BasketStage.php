<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/8
 * Time: 16:40
 */

namespace App\Models\LgMatch;


use Illuminate\Database\Eloquent\Model;

class BasketStage extends Model
{
    protected $connection = 'match';

    public static function getFinal($lid, $season) {
        $query = self::query();
        $query->where("lid", $lid);
        $query->where("season", $season);
        $query->where("name", "总决赛");
        return $query->first();
    }

}