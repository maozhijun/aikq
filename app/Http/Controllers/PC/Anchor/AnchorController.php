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
            $match = $hotMatch->getMatch();
            if (isset($match)&& $match['status'] >= 0) {
                $tmp[] = $hotMatch;
//                dump($match);
            }
        }
        $result['hotMatches'] = $tmp;
        $result['check'] = 'anchor';
        return view('pc.anchor.index',$result);
    }

    public function room(Request $request,$room_id)
    {
        $result = array();
        $room = AnchorRoom::find($room_id);
        if (isset($room)) {
            $match = $room->getLivingTag();
            $result['match'] = $match;
            $result['room_tag'] = $match['tag'];
        } else{
            $result['match'] = null;
            $result['room_tag'] = null;
        }
        $result['check'] = 'anchor';
        $result['room_id'] = $room_id;
        $result['room'] = $room;
        return view('pc.anchor.room',$result);
    }

    public function player(Request $request,$room_id){
        $result = array();
        $result['cdn'] = env('CDN_URL');
        $result['room_id'] = $room_id;
        return view('pc.anchor.player',$result);
    }

    public function playerUrl(Request $request,$room_id){
        $room = AnchorRoom::find($room_id);
        $url = (isset($room->live_rtmp)&&strlen($room->live_rtmp) > 0)?$room->live_rtmp:$room->live_flv;
        if (isset($room)) {
            $match = $room->getLivingTag();
            return response()->json(array('code' => 0, 'match'=>$match, 'status' => $room->status, 'title' => $room->title, 'live_url' => $url));
        } else{
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
            $tag = isset($match) ? $match['tag'] : null;

            $showScore = 0; $h_color = null; $a_color = null;
            if (isset($tag)) {
                $showScore = $tag['show_score'];
                $h_color = $tag['h_color'];
                $a_color = $tag['a_color'];
            }
            return response()->json(array('code' => 0, 'show_score'=>$showScore,
                'status' => $room->status,'match'=>$match,
                'h_color'=>$h_color, 'a_color'=>$a_color,
                'title' => $room->title, 'live_url' => $url));
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
            if (isset($match) && $match['match']['status'] >= 0)
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

    public function livingRoom(Request $request){
        //正在直播
        $livingRooms = AnchorRoom::getLivingRooms();
        $tmp = array();
        foreach ($livingRooms as $livingRoom) {
            $model = $livingRoom->appModel(true);
            if ($livingRoom['status'] == AnchorRoom::kStatusLiving){
                $model['statusStr'] = '直播中';
            }
            else{
                $model['statusStr'] = '';
            }
            $model['url'] = '';
            $tmp[] = $model;
        }
        return response()->json(array(
            'code'=>0,
            'data'=>$tmp
        ));
    }
}