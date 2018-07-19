<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:35
 */

namespace App\Http\Controllers\PC\Anchor;

use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Anchor\AnchorRoomTag;
use App\Models\Match\Odd;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AnchorController extends Controller
{
    public function index(Request $request){
        $result = array();
        $result['hotAnchors'] = Anchor::getHotAnchor();
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
        $hotMatches = AnchorRoomTag::getHotMatch();

        $tmp = array();
        foreach ($hotMatches as $hotMatch) {
            $match = $hotMatch->appModel();
            if (isset($match['match']) && $match['match']['status'] >= 0)
                $tmp[] = $hotMatch;
        }
        $result['hotMatches'] = $tmp;

        $result['hotMatches'] = $hotMatches;
        $result['check'] = 'anchor';
        return view('pc.anchor.index',$result);
    }

    public function room(Request $request,$room_id)
    {
        $result = array();
        $tag = AnchorRoomTag::find($room_id);
        if (isset($tag)) {
            $match = $tag->getMatch();
            $result['match'] = $match;
        }
        else{
            $result['match'] = null;
        }
        $result['check'] = 'anchor';
        $result['room_id'] = $room_id;
        $result['room'] = AnchorRoom::find($room_id);
        return view('pc.anchor.room',$result);
    }

    public function player(Request $request,$room_id){
        $result = array();
        $result['cdn'] = env('CDN_URL');
        return view('pc.anchor.player',$result);
    }

    public function playerUrl(Request $request,$room_id){
        $room = AnchorRoom::find($room_id);
        $url = (isset($room->live_rtmp)&&strlen($room->live_rtmp) > 0)?$room->live_rtmp:$room->live_flv;
        if (isset($room))
            return response()->json(array('code'=>0,'status'=>$room->status,'title'=>$room->title,'live_url'=>$url));
        else{
            return response()->json(array('code'=>-1,'live_url'=>''));
        }
    }

    /*** app 接口 ****/
    public function playerUrlApp(Request $request,$room_id){
        $room = AnchorRoom::find($room_id);
        if (isset($room)) {
            $key = env('APP_DES_KEY');
            $iv = env('APP_DES_IV');
            $url = (isset($room->live_rtmp)&&strlen($room->live_rtmp) > 0)?$room->live_rtmp:$room->live_flv;
            $url = openssl_encrypt($url, "DES", $key, 0, $iv);
            $match = $room->getLivingTag();
            //比赛时间
            if ($match['tag']['sport'] == 1){
                $match['current_time'] = AnchorController::getMatchCurrentTimeByTimestamp($match['time'],$match['timehalf'],$match['status']);
            }
            else if ($match['tag']['sport'] == 2){
                $match['current_time'] = AnchorController::getStatusTextCn($match['status'],$match['system']);
            }
            $tag = $match['tag'];
            return response()->json(array('code' => 0, 'show_score'=>$tag['show_score'],'status' => $room->status,'match'=>$match, 'title' => $room->title, 'live_url' => $url));
        }
        else{
            return response()->json(array('code'=>-1,'live_url'=>''));
        }
    }

    public function appV110(Request $request){
        $result = array();
        //热门主播
        $result['hotAnchors'] = Anchor::getHotAnchor();
        $tmp = array();
        foreach ($result['hotAnchors'] as $anchor) {
            $tmp[] = $anchor->appModel();
        }
        $result['hotAnchors'] = $tmp;
        //热门比赛
        $hotMatches = AnchorRoomTag::getHotMatch();
        $tmp = array();
        foreach ($hotMatches as $hotMatch) {
            $match = $hotMatch->appModel();
            if (isset($match['match']) && $match['match']['status'] >= 0)
                $tmp[] = $match;
        }
        $result['hotMatches'] = $tmp;
        //正在直播
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
        $tmp = array();
        foreach ($result['livingRooms'] as $livingRoom) {
            $tmp[] = $livingRoom->appModel(true);
        }
        $result['livingRooms'] = $tmp;
        return response()->json(array(
            'code'=>0,
            'data'=>$result
        ));
    }

    public static function getStatusTextCn($status, $isHalfFormat = false)
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return $isHalfFormat ? "上半场" : "第一节";
            case 2:
                return $isHalfFormat ? "" : "第二节";
            case 3:
                return $isHalfFormat ? "下半场" : "第三节";
            case 4:
                return $isHalfFormat ? "" : "第四节";
            case 5:
                return "加时1";
            case 6:
                return "加时2";
            case 7:
                return "加时3";
            case 8:
                return "加时4";
            case 50:
                return "中场";
            case -1:
                return "已结束";
            case -5:
                return "推迟";
            case -2:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }

    public static function getMatchCurrentTimeByTimestamp($time, $timehalf, $status) {
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($status < 0 || $status == 2 || $status == 4) {
            $matchTime = self::getStatusTextCn($status);
        }elseif ($status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45\'+') : ((floor(($diff) % 86400 / 60)) . '\'');
        } elseif ($status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90\'+') : ((floor(($diff) % 86400 / 60) + 45) . '\'');
        } else {
//            $matchTime = substr($match->time, 11, 5);
            $matchTime = '';
        }
        return $matchTime;
    }
}