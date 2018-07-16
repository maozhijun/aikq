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
use Illuminate\Support\Facades\Storage;

class AnchorController extends Controller
{
    public function index(Request $request){
        $result = array();
        $result['hotAnchors'] = Anchor::getHotAnchor();
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
        $hotMatches = AnchorRoomTag::getHotMatch();
        $result['hotMatches'] = $hotMatches;
        $result['check'] = 'anchor';
        return view('pc.anchor.index',$result);
    }

    public function room(Request $request,$room_id){
        $tag = AnchorRoomTag::find($room_id);
        $match = $tag->getMatch();
        $result = array();
        $result['match'] = $match;
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
        return response()->json(array('code'=>0,'live_url'=>$room->url));
    }

    /*** app 接口 ****/
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
            $tmp[] = $hotMatch->appModel();
        }
        $result['hotMatches'] = $tmp;
        //正在直播
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
        $tmp = array();
        foreach ($result['livingRooms'] as $livingRoom) {
            $data = $livingRoom->appModel();
            $data['anchor'] = $livingRoom->anchor->appModel();
            $data['match'] = $livingRoom->getLivingMatch();
            $data['cover'] = env('APP_URL').$livingRoom['cover'];
            $tmp[] = $data;
        }
        $result['livingRooms'] = $tmp;
        return response()->json(array(
            'code'=>0,
            'data'=>$result
        ));
    }
}