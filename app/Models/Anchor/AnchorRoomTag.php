<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:46
 */
namespace App\Models\Anchor;


use Illuminate\Database\Eloquent\Model;

class AnchorRoomTag extends Model{
    protected $connection = 'akq';

    const kIsHot = 1, kNotHot = 0;
    static public function getHotMatch(){
        $rooms = AnchorRoomTag::where('hot',AnchorRoomTag::kIsHot)
            ->where('match_time','>=', strtotime('-4 hours'))
            ->get();
        return $rooms;
    }

    public function room()
    {
        return $this->hasOne('App\Models\Anchor\AnchorRoom', 'id', 'room_id');
    }

    public function getMatch(){
        if($this->sport == 1){
            $url = 'http://match.liaogou168.com/static/terminal/1/'.substr($this->match_id,0,2).'/'.substr($this->match_id,2,2).'/'.$this->match_id.'/match.json';
            $match = AnchorRoomTag::curlData($url,5);
            return $match;
        }
        if($this->sport == 2){
            $url = 'http://match.liaogou168.com/static/terminal/2/'.substr($this->match_id,0,2).'/'.substr($this->match_id,2,2).'/'.$this->match_id.'/match.json';
            $match = AnchorRoomTag::curlData($url,5);
            return $match;
        }
        return null;
    }

    /**
     * 请求match接口
     * @param $url
     * @param $timeout
     * @return mixed
     */
    public static function curlData($url,$timeout = 5){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }
}