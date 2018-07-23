<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:35
 */

namespace App\Http\Controllers\Mobile\Anchor;

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
        return view('mobile.anchor.index',$result);
    }

    public function room(Request $request,$room_id){
        $room = AnchorRoom::find($room_id);
        if (isset($room)) {
            $match = $room->getLivingTag();
            $tag = $match['tag'];
        } else{
            $match = null;
            $tag = null;
        }
        return view('mobile.anchor.room',array('match'=>$match,'room'=>$room,'room_tag'=>$tag, 'room_id'=>$room_id));
    }
}