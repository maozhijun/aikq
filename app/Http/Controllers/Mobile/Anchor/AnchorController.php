<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:35
 */

namespace App\Http\Controllers\Mobile\Anchor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\UrlCommonTool;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Anchor\AnchorRoomTag;
use Illuminate\Http\Request;

class AnchorController extends Controller
{
    public function index(Request $request){
        $result = array();
        $result['hotAnchors'] = Anchor::getHotAnchor();
        $result['livingRooms'] = AnchorRoom::getLivingRooms();
        $hotMatches = AnchorRoomTag::getHotMatch();
        $result['hotMatches'] = $hotMatches;
        $result['title'] = '美女主播球赛讲解_主播频道-爱看球直播';
        $result['h1'] = '美女主播球赛讲解';
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
        $result = array('match'=>$match,'room'=>$room,'room_tag'=>$tag, 'room_id'=>$room_id);
        $result['anchor'] = $room->anchor;
        $result['books'] = $room->getTagMatch();
        $result['title'] = '美女主播球赛讲解_主播频道-爱看球直播';
        $result['h1'] = '美女主播球赛讲解';
        return view('mobile.anchor.room',$result);
    }

    /////////////////////////////////////  静态化列表 开始   /////////////////////////////////////

    /**
     * 静态化 wap 首页
     * @param Request $request
     */
    public function indexStatic(Request $request){
        $html = $this->index($request);
        $this->onHtmlStatic($html, UrlCommonTool::MOBILE_STATIC_PATH."/anchor/index.html");
    }

    /**
     * 静态化直播房间页
     * @param Request $request
     */
    public function roomStatic(Request $request){
        $room_id = $request->input('room_id');
        $html = $this->room($request, $room_id);
        $this->onHtmlStatic($html, UrlCommonTool::MOBILE_STATIC_PATH.'/anchor/room/' . $room_id . '.html');
    }
}