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

    /**
     * 正在直播什么比赛
     * @return Model|null|static
     */
    public function getLivingMatch()
    {
//        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
        $tags = AnchorRoomTag::where('room_id',$this->id)
            ->where('match_time','>',date_create('-4 hours'))
            ->get();
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

    /**
     * 预约的比赛
     * @return \Illuminate\Support\Collection
     */
    public function getTagMatch(){
        $tags = AnchorRoomTag::where('room_id',$this->id)
            ->where('match_time','>',date_create('-4 hours'))
            ->get();
        $mids = array();
        foreach ($tags as $tag){
            $mids[] = $tag['match_id'];
        }
        $matches = Match::whereIn('id',$mids)
            ->where('status' , '>=' , 0)
            ->orderby('time','asc')
            ->get();
        return $matches;
    }

    public function anchor()
    {
        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
    }

    public function appModel(){
        $tmp = array();
        $tmp['id'] = $this->id;
        $tmp['title'] = $this->title;
        $tmp['cover'] = $this->cover;
        return $tmp;
    }
}