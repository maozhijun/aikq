<?php

namespace App\Http\Controllers\Backstage;


use App\Http\Controllers\Admin\UploadTrait;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Anchor\AnchorRoomTag;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BsController extends Controller
{
    const BS_LOGIN_SESSION = 'AKQ_BS_LOGIN_SESSION';
    /**
     * 足球
        大平台：英超、西甲、中超、欧冠杯、亚冠
        中平台：德甲、意甲、法甲、欧罗巴杯、澳洲甲、中甲
        小平台：除了上述的其他


        篮球
        大平台：NBA
        中平台：CBA
        小平台：除了上述的其他
     */
    const FOOTBALL_BIG_LEAGUE = [31, 26, 46, 73, 139];
    const FOOTBALL_MIDDLE_LEAGUE = [8, 29, 11, 77, 187, 47];
    const BASKETBALL_BIG_LEAGUE = [1, 3];
    const BASKETBALL_MIDDLE_LEAGUE = [4, 5];

    use UploadTrait;

    public function __construct()
    {
        $this->middleware('backstage_auth')->except(['login', 'logout']);
    }

    /**
     * 登录页面、登录逻辑
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function login(Request $request)
    {
        $method = $request->getMethod();
        if (strtolower($method) == "get") {//跳转到登录页面
            return view('backstage.index');
        }

        $target = $request->input("target");
        $phone = $request->input("phone", '');
        $password = $request->input("password");
        $remember = $request->input("remember", 0);

        $anchor = Anchor::query()->where("phone", $phone)->first();
        if (!isset($anchor)) {
            return back()->with(["error" => "账户或密码错误", 'phone'=>$phone]);
        }

        $salt = $anchor->salt;
        $pw = $anchor->passport;
        //判断是否登录
        if ($pw != Anchor::shaPassword($salt, $password)) {
            return back()->with(["error" => "账户或密码错误", 'phone'=>$phone]);
        }
        $target = empty($target) ? '/bs/info' : $target;
        session([self::BS_LOGIN_SESSION => $anchor->id]);//登录信息保存在session
        if ($remember == 1) {
            //$c = cookie(self::BS_LOGIN_SESSION, $token, 60 * 24 * 7, '/', 'aikq.cc', false, true);
            return response()->redirectTo($target);//->withCookies([$c]);
        } else {
            return response()->redirectTo($target);
        }
        return back()->with(["error" => "账户或密码错误"]);
    }

    /**
     * 主播信息页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info(Request $request) {
        $anchor = $request->admin_user;
        $room = $anchor->room;
        $result['anchor'] = $anchor;
        $result['room'] = $room;
        $result['iLive'] = isset($room) && $room->status == AnchorRoom::kStatusLiving;
        return view('backstage.info', $result);
    }

    /**
     * 修改主播房间状态为 直播中
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startLive(Request $request) {
        $anchor = $request->admin_user;
        $room = $anchor->room;
        $refresh = $request->input("refresh");
        if (!isset($room)) {
            $room = new AnchorRoom();
            $room->anchor_id = $anchor->id;
        }
        try {
            if ($refresh != 1 && $room->status == AnchorRoom::kStatusLiving) {
                return response()->json(['code'=>302, 'message'=>'直播间正在直播，如果断流了，请使用重新推流。']);
            }

            //获取推流地址 开始
            $liveMatch = $room->getLivingMatch();
            $liveLevel = $this->getLiveLevel($liveMatch['lid'], $liveMatch['sport']);
            $json = $this->getPushLive($room->id, $liveLevel, false, $refresh == 1);
            if (is_null($json) || !isset($json['data']['push_rtmp']) || !isset($json['data']['push_key'])) {
                return response()->json(['code'=>302, 'message'=>'获取推流地址失败']);
            }
            //获取推流地址 结束

            $jsonData = $json['data'];
            $room->url = $jsonData['push_rtmp'];
            $room->url_key = $jsonData['push_key'];
            $room->live_flv = $jsonData['live_flv'];
            $room->live_rtmp = $jsonData['live_rtmp'];
            $room->live_m3u8 = $jsonData['live_m3u8'];
            $room->expiration = $jsonData['expiration'];//流到期时间
            $room->status = AnchorRoom::kStatusLiving;
            $room->save();
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'message'=>'获取推流地址失败']);
        }
        $data = ['url_key'=>$jsonData['push_key'], 'url'=>$jsonData['push_rtmp'] ];
        return response()->json(['code'=>200, 'message'=>'获取推流地址成功', 'data'=>$data]);
    }

    /**
     * 停止直播
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function endLive(Request $request) {
        $anchor = $request->admin_user;
        $room = $anchor->room;
        if (!isset($room)) {
            $room = new AnchorRoom();
            $room->anchor_id = $anchor->id;
        }
        try {
            if ($room->status == AnchorRoom::kStatusLiving) {
                $room->url = null;
                $room->url_key = null;
                $room->live_flv = null;
                $room->live_rtmp = null;
                $room->live_m3u8 = null;
                $room->status = AnchorRoom::kStatusNormal;
                $room->save();
            }
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'message'=>'停止直播失败']);
        }
        return response()->json(['code'=>200, 'message'=>'停止直播成功']);
    }

    /**
     * 保存主播信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveInfo(Request $request) {
        $room_title = $request->input('room_title');//房间名称
//        if (empty($room_title)) {
//            return back()->with(['error'=>'房间标题不能为空']);
//        }
        //$anchor_icon; $room_cover;
        try {
            $anchor = $request->admin_user;
            if ($request->hasFile("anchor_icon")) {
                $icon = $this->saveUploadedFile($request->file("anchor_icon"), 'cover');
                $anchor->icon = $icon->getUrl();
                $anchor->save();
            }
            $room = $anchor->room;
            if (!isset($room)) {
                $room = new AnchorRoom();
                $room->anchor_id = $anchor->id;
            }
            $isEdit = false;
            if (empty($room_title)) {
                $room_title = $anchor->name . "的直播间";
            }
            if ($room_title != $room->title) {
                $room->title = $room_title;
                $isEdit = true;
            }
            if ($request->hasFile("room_cover")) {
                $cover = $this->saveUploadedFile($request->file("room_cover"), 'cover');
                $room->cover = $cover->getUrl();
                $isEdit = true;
            }
            if ($isEdit) {
                $room->save();
            }
        } catch (\Exception $exception) {
            return back()->with(['error'=>'保存失败']);
        }
        return back()->with(['success'=>'保存成功']);
    }

    /**
     * 修改密码页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function passwordEdit(Request $request) {
        $method = $request->getMethod();
        if (strtolower($method) == "get") {//跳转到登录页面
            return view('backstage.password', []);
        }

        $target = $request->input("target", '/bs/login');
        $old = $request->input("old", '');
        $new = $request->input("new");
        $copy = $request->input("copy", 0);

        if (empty($old)) {
            return back()->with(["error" => "当前密码不能为空"]);
        }
        if (empty($new)) {
            return back()->with(["error" => "新密码不能为空"]);
        }
        if ($new != $copy) {
            return back()->with(["error" => "两次输入的新密码不一致"]);
        }

        $anchor = $request->admin_user;
        $salt = $anchor->salt;
        $pw = $anchor->passport;
        //判断是否登录
        if ($pw != Anchor::shaPassword($salt, $old)) {
            return back()->with(["error" => "账户的原密码密码错误"]);
        }

        try {
            $anchor->passport = Anchor::shaPassword($salt, $new);
            $anchor->save();
        } catch (\Exception $exception) {
            return back()->with(["error" => "修改失败。"]);
        }

        session([self::BS_LOGIN_SESSION => null]);//清除登录信息
        setcookie(self::BS_LOGIN_SESSION, '', time() - 3600, '/', 'aikq.cc');
        $request->admin_user = null;//清除登录信息

        return response()->redirectTo($target);
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request) {
        $request->admin_user = null;
        session()->forget(self::BS_LOGIN_SESSION);
        setcookie(self::BS_LOGIN_SESSION, '', time() - 3600, '/', 'aikq.cc');
        return response()->redirectTo('/bs/login');
    }

    /**
     * 赛事预约
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function matches(Request $request) {
        $anchor = $request->admin_user;
        $room = $anchor->room;
        $result = [];
        if (isset($room)) {
            $query = AnchorRoomTag::query()->where('room_id', $room->id);
            $query->where('match_time', '>=', date('Y-m-d H:i', strtotime('-4 hours')));
            $tags = $query->orderBy('match_time')->get();
            $result['tags'] = $tags;
        }
        return view('backstage.match', $result);
    }


    /**
     * 主播预约赛事
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookMatch(Request $request) {
        $mid = $request->input('mid');//主播预约的比赛ID
        $sport = $request->input('sport');//比赛类型

        if (!is_numeric($mid)) {
            return response()->json(['code'=>401, 'message'=>'请选择比赛']);
        }
        if (!in_array($sport, [1, 2])) {
            return response()->json(['code'=>401, 'message'=>'比赛类型错误']);
        }
        if ($sport == 1) {
            $match = Match::query()->find($mid);
        } else {
            $match = BasketMatch::query()->find($mid);
        }
        if (!isset($match)) {
            return response()->json(['code'=>'403', 'message'=>'比赛不存在']);
        }
        try {
            $matchTime = $match->time;
            $anchor = $request->admin_user;
            $room = $anchor->room;
            if (!isset($room)) {
                $room = new AnchorRoom();
                $room->anchor_id = $anchor->id;
                $room->save();
            }
            $count = AnchorRoomTag::query()->where('room_id', $room->id)->where('match_id', $mid)->count();
            if ($count > 0) {
                //return response()->json(['code'=>403, 'message'=>'您已预约过本场比赛']);
            }
            //判断2小时内是不是有其他赛事预约。
            $start = date('Y-m-d H:i', strtotime($matchTime . ' -2 hours'));
            $end = date('Y-m-d H:i', strtotime($matchTime . ' +2 hours'));
            $count = AnchorRoomTag::query()->where('room_id', $room->id)->whereBetween('match_time', [$start, $end])->count();
            if ($count > 0) {
                return response()->json(['code'=>403, 'message'=>'同一比赛时段，只能预约一场比赛，请重新预约。']);
            }
            $art = new AnchorRoomTag();
            $art->room_id = $room->id;
            $art->match_id = $mid;
            $art->sport = $sport;
            $art->match_time = $matchTime;
            $art->save();
        } catch (\Exception $exception) {
            dump($exception);
            return response()->json(['code'=>500, 'message'=>'预约比赛失败']);
        }
        return response()->json(['code'=>200, 'message'=>'预约比赛成功']);
    }

    /**
     * 取消预约比赛
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelBookMatch(Request $request) {
        $id = $request->input('id');
        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'message'=>'参错错误']);
        }

        $anchor = $request->admin_user;
        $room = $anchor->room;
        if (!isset($room)) {
            return response()->json(['code'=>401, 'message'=>'您还没有预约比赛']);
        }
        $tag = AnchorRoomTag::query()->find($id);
        if (!isset($tag)) {
            return response()->json(['code'=>401, 'message'=>'您没有预约本场比赛']);
        }
        if ($tag->room_id != $room->id) {
            return response()->json(['code'=>401, 'message'=>'没有权限']);
        }
        try {
            $tag->delete();
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'message'=>'取消预约失败']);
        }
        return response()->json(['code'=>200, 'message'=>'取消预约成功']);
    }

    /**
     * 查询比赛
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findMatches(Request $request) {
        $sport = $request->input('sport');
        $search = $request->input('search');
        if (!in_array($sport, [1, 2])) {
            return response()->json(['code'=>401, 'message'=>'比赛类型错误。']);
        }
        if (empty($search)) {
            return response()->json(['code'=>401, 'message'=>'请输入球队名称或者联赛名称。']);
        }
        if ($sport == 1) {
            $matches = $this->findFootballMatches($search);
        } else {
            $matches = $this->findBasketballMatches($search);
        }

        return response()->json(['code'=>200, 'matches'=>$matches]);
    }

    /**
     * 设置主客队球衣颜色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setTeamColor(Request $request) {
        $id = $request->input('id');//预约ID
        $color = $request->input('color');//颜色
        $home = $request->input('home');//是否主队，1：主队，其他：客队

        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'message'=>'参数错误']);
        }

        $roomTag = AnchorRoomTag::query()->find($id);
        if (!isset($roomTag)) {
            return response()->json(['code'=>403, 'message'=>'预约不存在']);
        }
        $anchor = $request->admin_user;
        $room = $anchor->room;
        if (!isset($room) || $roomTag->room_id != $room->id) {
            return response()->json(['code'=>403, 'message'=>'没有权限操作']);
        }

        $color = empty($color) ? null : $color;

        //判断主客颜色是否一样
        if ($home == 1) {
            $a_color = $roomTag->a_color;
            $sameColor = !is_null($color) && $color == $a_color;
        } else {
            $h_color = $roomTag->h_color;
            $sameColor = !is_null($color) && $color == $h_color;
        }
        if ($sameColor) {
            return response()->json(['code'=>403, 'message'=>'主客球衣不能设置成同样的颜色']);
        }

        try {
            if ($home == 1) {
                $roomTag->h_color = $color;
            } else {
                $roomTag->a_color = $color;
            }
            $roomTag->save();
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'message'=>'设置球衣颜色失败']);
        }
        return response()->json(['code'=>200, 'message'=>'设置球衣颜色成功']);
    }

    /**
     * 设置预约是否隐藏显示对阵信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function setShowScore(Request $request) {
        $id = $request->input('id');
        $type = $request->input('type');

        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'message'=>'参数错误']);
        }
        if (!in_array($type, ['show', 'hide'])) {
            return response()->json(['code'=>401, 'message'=>'类型参数错误']);
        }

        $roomTag = AnchorRoomTag::query()->find($id);
        if (!isset($roomTag)) {
            return response()->json(['code'=>403, 'message'=>'预约不存在']);
        }
        $anchor = $request->admin_user;
        $room = $anchor->room;
        if (!isset($room) || $roomTag->room_id != $room->id) {
            return response()->json(['code'=>403, 'message'=>'没有权限操作']);
        }
        $msg = $type == "show" ? "显示" : "隐藏";
        $show_score = $type == "show" ? 1 : 0;
        try {
            $roomTag->show_score = $show_score;
            $roomTag->save();
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'message'=>$msg . "对阵失败"]);
        }

        return response()->json(['code'=>200, 'message'=>$msg . '对阵成功']);
    }

    /**
     * 根据球队名称获取足球比赛
     * @param $search
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    protected function findFootballMatches($search) {
        //
        $start = date('Y-m-d H:i');
        $end = date('Y-m-d H:i', strtotime('+7 days'));
        $query = Match::query();
        $query->where('status', '>=', 0);
        $query->where(function ($orQuery) use ($search) {
            $orQuery->where('hname', 'like', '%' . $search . '%');
            $orQuery->orWhere('aname', 'like', '%' . $search . '%');
            $orQuery->orWhere('win_lname', 'like', '%' . $search . '%');
        });
        $query->whereBetween('time', [$start, $end]);
        $query->selectRaw("hname, aname, time, id as mid, win_lname, lname, status");
        $query->orderBy("time")->orderBy("id");
        return $query->take(15)->get();
    }

    /**
     * 根据球队名称 获取篮球比赛
     * @param $search
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    protected function findBasketballMatches($search) {
        $start = date('Y-m-d H:i');
        $end = date('Y-m-d H:i', strtotime('+7 days'));
        $query = BasketMatch::query();
        $query->where('status', '>=', 0);
        $query->where(function ($orQuery) use ($search) {
            $orQuery->where('hname', 'like', '%' . $search . '%');
            $orQuery->orWhere('aname', 'like', '%' . $search . '%');
            $orQuery->orWhere('win_lname', 'like', '%' . $search . '%');
        });
        $query->whereBetween('time', [$start, $end]);
        $query->selectRaw("hname, aname, time, id as mid, win_lname, lname, status");
        $query->orderBy("time")->orderBy("id");
        return $query->take(15)->get();
    }

    /**
     * 获取推流信息
     * @param $room_id
     * @param $level
     * @param $isHttp
     * @param $refresh
     * @return mixed
     */
    protected function getPushLive($room_id, $level, $isHttp, $refresh = false) {
        $host = env('PUSH_URL', 'http://live.push.qiushengke.com');
        $url = $host . '/api/v1/get_push_stream?uid=12345&level=1' . ($refresh ? '&refresh=1' : '');
        $jsonStr = \App\Http\Controllers\Controller::execUrl($url, 2, $isHttp);
        $json = json_decode($jsonStr, true);
        return $json;
    }

    /**
     * 获取推流地址level
     * @param $lid
     * @param $sport
     * @return int
     */
    protected function getLiveLevel($lid, $sport) {
        if ($sport == 1) {
            if (in_array($lid, self::FOOTBALL_BIG_LEAGUE)) {
                return 3;
            } else if (in_array($lid, self::FOOTBALL_MIDDLE_LEAGUE)){
                return 2;
            } else {
                return 1;
            }
        } else if ($sport == 2) {
            if (in_array($lid, self::BASKETBALL_BIG_LEAGUE)) {
                return 3;
            } else if (in_array($lid, self::BASKETBALL_MIDDLE_LEAGUE)){
                return 2;
            } else {
                return 1;
            }
        }
        return 1;
    }

}
