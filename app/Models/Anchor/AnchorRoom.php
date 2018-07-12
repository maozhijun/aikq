<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:46
 */
namespace App\Models\Anchor;


use Illuminate\Database\Eloquent\Model;

class AnchorRoom extends Model{
    protected $connection = 'akq';
    const kStatusLiving = 1, kStatusClose = 2, kStatusNormal = 0;
    static public function getLivingRooms(){
        $rooms = AnchorRoom::where('status',AnchorRoom::kStatusLiving)->get();
        return $rooms;
    }

    public function anchor()
    {
        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
    }

}