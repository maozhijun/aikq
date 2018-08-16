<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class LiveAlias extends Model
{
    protected $connection = 'match';
    const kTypeLeague = 1, kTypeTeam = 2;
    const kFromBallbar = 1, kFromSportStream365 = 2, kFromTTZB = 3,kFromWCJ = 4, kFromDDK = 5, kFromKBS = 6;

    /**
     * 保存别名
     * @param $lid
     * @param $type
     * @param $from
     * @param int $sport
     * @throws \Exception
     */
    public function syncAlias($lid,$type,$from,$sport = LiaogouAlias::kSportTypeFootball){
        if ($type == 1){
            $team = Team::query()->find($lid);
            if (isset($team)){
                $alias = new LiaogouAlias();
                $alias->type = $type;
                $alias->from = $from;
                $alias->target_name = $this->name;
                $alias->lg_name = $team->name;
                $alias->lg_id = $lid;
                $alias->sport = $sport;
                $alias->save();
                $this->delete();
            }
        }
    }

    /**
     * 搜索别名
     * @param $name
     * @param $type
     * @param $from
     * @param $sport
     * @return mixed
     */
    static public function getAliasByName($name,$type,$from,$sport = LiaogouAlias::kSportTypeFootball){
        $alias = LiveAlias::query()->where('type',$type)
            ->where('from',$from)
            ->where('sport',$sport)
            ->where('name',$name)
            ->first();
        return $alias;
    }
}
