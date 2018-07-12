<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:46
 */
namespace App\Models\Anchor;


use Illuminate\Database\Eloquent\Model;

class Anchor extends Model{
    protected $connection = 'akq';
    static public function getHotAnchor(){
        $anchors = Anchor::where('hot',1)->get();
        return $anchors;
    }

    public function room()
    {
        return $this->hasOne('App\Models\Anchor\AnchorRoom', 'anchor_id', 'id');
    }
}