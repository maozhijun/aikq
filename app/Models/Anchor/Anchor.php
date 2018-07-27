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
    const kStatusValid = 1, kStatusInvalid = 2;

    static public function getHotAnchor(){
        $anchors = Anchor::where('hot',1)
            ->orderby('sort','desc')
            ->get();
        return $anchors;
    }

    /**
     * 获取有效的主播列表
     * @param null $count
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getValidAnchors($count = null) {
        $query = Anchor::query()->where('status',self::kStatusValid);
        if (is_numeric($count) && $count > 0) {
            $query->take($count);
        }
        $anchors = $query->get();
        return $anchors;
    }

    public function room()
    {
        return $this->hasOne('App\Models\Anchor\AnchorRoom', 'anchor_id', 'id');
    }

    public static function shaPassword($salt, $password)
    {
        return sha1($salt . $password);
    }

    public function appModel(){
        $room = $this->room;
        return $room->appModel();
    }
}