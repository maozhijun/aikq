<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class LiaogouAlias extends Model
{
    protected $connection = 'match';
    const kFromQiuTan = 1, kFromBallBar = 2, kFromSportStream365 = 3,kFromTTZB = 4,kFromWCJ = 5, kFromDDK = 6, kFromKBS = 7;
    const kTypeTeam = 1, kTypeLeague = 2;
    const kSportTypeFootball = 1, kSportTypeBasket = 2;
    //

    static public function bindingLotteryTeam($tid,$name,$sport = LiaogouAlias::kSportTypeFootball){
        $alias = self::query()->where('win_id',$tid)
            ->where('type',1)
            ->where('from',1)
            ->where('sport',$sport)
            ->get();
        foreach ($alias as $alia){
            $alia->lg_name = $name;
            $alia->save();
        }
    }

    /**
     * 搜索别名
     * @param $name
     * @param $type
     * @param $from
     * @param int $sport
     * @return mixed
     */
    static public function getAliasByName($name,$type,$from,$sport = LiaogouAlias::kSportTypeFootball){
        $alias = self::query()->where('type',$type)
            ->where('from',$from)
            ->where('target_name',$name)
            ->where('sport',$sport)
            ->first();
        return $alias;
    }
}
