<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:46
 */
namespace App\Models\Anchor;


use App\Models\Match\Match;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnchorRoom extends Model{
    protected $connection = 'akq';
    const kStatusLiving = 1, kStatusClose = 2, kStatusNormal = 0;
    static public function getLivingRooms(){
        $rooms = AnchorRoom::where('status',AnchorRoom::kStatusLiving)->get();
        return $rooms;
    }

    public function getLivingMatch()
    {
//        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
        $tags = AnchorRoomTag::where('room_id',$this->id)
            ->where('match_time','>',date('-4 hours'))->get();
        $mids = array();
        foreach ($tags as $tag){
            $mids[] = $tag['match_id'];
        }
        $match = Match::whereIn('id',$mids)
        ->where('status' , '>' , 0)
        ->orderby('time','asc')
        ->first();
        return $match;
    }

    public function anchor()
    {
        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
    }

}