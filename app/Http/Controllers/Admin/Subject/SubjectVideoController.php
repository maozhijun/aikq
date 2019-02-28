<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/8
 * Time: 15:23
 */

namespace App\Http\Controllers\Admin\Subject;


use App\Models\LgMatch\BasketStage;
use App\Models\LgMatch\Stage;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 录像
 * Class SubjectVideoController
 * @package App\Http\Controllers\CMS\akq
 */
class SubjectVideoController extends Controller
{

    /**
     * 录像列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videos(Request $request) {
        $s_lid = $request->input('s_lid');
        $hname = $request->input('hname');
        $aname = $request->input('aname');

        $result = [];
        $query = SubjectVideo::query();
        if (is_numeric($s_lid)) {
            $query->where('s_lid', $s_lid);
        }
        if (!empty($hname)) {
            $query->where('hname', 'like', '%' . $hname . '%');
        }
        if (!empty($aname)) {
            $query->where('aname', 'like', '%' . $aname . '%');
        }
        $query->orderByDesc('id');
        //$query->orderByDesc('time');
        $page = $query->paginate(10);

        $leagues = SubjectLeague::getAllLeagues();

        $result['page'] = $page;
        $result['leagues'] = $leagues;
        $result['players'] = MatchLiveChannel::kPlayerArrayCn;
        return view('admin.subject.video.list', $result);
    }

    /**
     * 录像编辑页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request) {

        $leagues = SubjectLeague::getAllLeagues();
        $players = MatchLiveChannel::kPlayerArrayCn;

        $result['sports'] = [MatchLive::kSportFootball=>'足球', MatchLive::kSportBasketball=>'篮球'];
        $result['leagues'] = $leagues;
        $result['players'] = $players;
        return view('admin.subject.video.edit', $result);
    }

    /**
     * 保存专题录像
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveVideo(Request $request) {
        $id = $request->input('id');
        $s_lid = $request->input('s_lid');//sport
        $mid = $request->input('mid');
        $cover = $request->input('cover');//封面图
        $tags = $request->input("tags");//标签

        if (!is_numeric($mid)) {
            return back()->with('error', '请选择比赛');
        }

        if ($s_lid == SubjectVideo::kOther . '-1' || $s_lid == SubjectVideo::kOther . '-2') {
            if ($s_lid == SubjectVideo::kOther . '-1') {
                $sport = MatchLive::kSportFootball;
            } else {
                $sport = MatchLive::kSportBasketball;
            }
            $s_lid = SubjectVideo::kOther;
        } else {
            $sl = SubjectLeague::query()->find($s_lid);
            if (!isset($sl)) {
                return back()->with('error', '您选择得专题不存在');
            }
            $sport = $sl->sport;
        }



        if ($sport == MatchLive::kSportFootball) {
            $match = Match::query()->find($mid);
        } else if ($sport == MatchLive::kSportBasketball) {
            $match = BasketMatch::query()->find($mid);
        }
        $lname = $match->win_lname;
        if (!isset($match)) {
            return back()->with('error', '您选择得比赛不存在');
        }

        if (is_numeric($id)) {
            $s_video = SubjectVideo::query()->find($id);
        }
        if (!isset($s_video)) {
            $s_video = new SubjectVideo();
        }
        try {
            $s_video->s_lid = $s_lid;
            $s_video->mid = $mid;
            $s_video->sport = $sport;
            $s_video->hname = $match->hname;
            $s_video->aname = $match->aname;
            $s_video->hid = $match->hid;
            $s_video->aid = $match->aid;
            $s_video->hscore = $match->hscore;
            $s_video->ascore = $match->ascore;
            $s_video->season = $match->season;
            $s_video->stage = $match->stage;
            $s_video->round = $match->round;
            $s_video->group = $match->group;
            $s_video->lname = $lname;
            $s_video->time = $match->time;
            $s_video->cover = $cover;

            if (!empty($match->stage)) {
                $stageId = $match->stage;
                if ($sport == MatchLive::kSportFootball) {
                    $stage = Stage::query()->find($stageId);
                } else if ($sport == MatchLive::kSportBasketball) {
                    $stage = BasketStage::query()->find($stageId);
                }
                if (isset($stage)) {
                    $s_video->stage_cn = $stage->name;
                }
            }
            DB::transaction(function () use ($s_video, $tags) {
                $s_video->save();
                $tagArray = json_decode($tags, true);
                $tagArray = is_null($tagArray) ? [] : $tagArray;
                TagRelation::savePlayBackTagRelation($s_video->sport, $s_video->id, $tagArray);
            });
            $this->flushVideo($s_video->id);
        } catch (\Exception $exception) {
            Log::error($exception);
            return back()->with('error', '保存失败');
        }
        return back()->with('success', '保存成功');
    }

    /**
     * 删除录像
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delVideo(Request $request) {
        $id = $request->input('id');
        if (is_numeric($id)) {
            $sv = SubjectVideo::query()->find($id);
            if (isset($sv)) {
                $links = $sv->getChannels();
                foreach ($links as $link) {
                    $link->delete();
                }
                $sv->delete();
                $this->flushVideo($id);
            }
        }
        return back()->with('success', '删除成功');
    }

    /**
     * 保存录像线路
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveVideoChannel(Request $request) {
        $id = $request->input('id');
        $sv_id = $request->input('sv_id');//专题录像的ID
        $title = $request->input('title');//录像标题
        $cover = $request->input('cover');//录像封面
        $platform = $request->input('platform');//录像播放平台
        $type = $request->input('type');//录像类型，1：录像，2：集锦
        $player = $request->input('player');//录像播放方式
        $content = $request->input('content');//录像链接内容
        $od = $request->input('od');//录像排序

        if (!is_numeric($sv_id)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        if (empty($title) || mb_strlen($title) > 32) {
            return response()->json(['code'=>401, 'msg'=>'录像标题不能为空或者多于32字符']);
        }
//        if (empty($cover)) {
//            return response()->json(['code'=>401, 'msg'=>'请上传录像封面']);
//        }
        if (!in_array($platform, [MatchLiveChannel::kPlatformAll, MatchLiveChannel::kPlatformPC, MatchLiveChannel::kPlatformWAP])) {
            return response()->json(['code'=>401, 'msg'=>'可播放平台参数错误']);
        }
        if (!in_array($player, MatchLiveChannel::kPlayerArray)) {
            return response()->json(['code'=>401, 'msg'=>'播放方式错误']);
        }
        if (empty($content)) {
            return response()->json(['code'=>401, 'msg'=>'播放链接不能为空']);
        }
        if (!empty($od) && !is_numeric($od)) {
            return response()->json(['code'=>401, 'msg'=>'排序必须填写正整数']);
        }
        $sv = SubjectVideo::query()->find($sv_id);
        if (!isset($sv)) {
            return response()->json(['code'=>401, 'msg'=>'专题录像不存在']);
        }

        try {
            if (is_numeric($id)) {
                $sv_ch = SubjectVideoChannels::query()->find($id);
            }
            if (!isset($sv_ch)) {
                $sv_ch = new SubjectVideoChannels();
                $sv_ch->sv_id = $sv_id;
            }
            $sv_ch->title = $title;
            $sv_ch->cover = $cover;
            $sv_ch->platform = $platform;
            $sv_ch->type = $type;
            $sv_ch->player = $player;
            $sv_ch->content = $content;
            $sv_ch->od = $od;
            $sv_ch->save();
            $this->flushVideo($sv_id, $sv_ch->id);
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'msg'=>'保存录像线路失败']);
        }

        $ch = curl_init();
        $url = env('CMS_URL').'/static/record/'.$sv_id;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);

        return response()->json(['code'=>200, 'msg'=>'保存录像线路成功']);
    }

    /**
     * 删除录像线路
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delVideoChannel(Request $request) {
        $id = $request->input('id');

        if (is_numeric($id)) {
            $sv_ch = SubjectVideoChannels::query()->find($id);
            if (isset($sv_ch)) {
                $vid = $sv_ch->sv_id;
                $sv_ch->delete();
                $this->flushVideo($vid, $id);
            }
        }
        return back()->with('success', '删除录像线路成功');
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findMatches(Request $request) {
        $name = $request->input('name');//主队名称
        $s_lid = $request->input('s_lid');//专题id
        if (empty($name)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        if ($s_lid == SubjectVideo::kOther .'-1' || $s_lid == SubjectVideo::kOther . '-2') {
            if ($s_lid == SubjectVideo::kOther .'-1') {
                $sport = MatchLive::kSportFootball;
            } else {
                $sport = MatchLive::kSportBasketball;
            }
        } else {
            $sl = SubjectLeague::query()->find($s_lid);
            if (!isset($sl)) {
                return response()->json(['code'=>401, 'msg'=>'参数错误']);
            }
            $sport = $sl->sport;
            $lid = $sl->lid;
        }


        if ($sport == MatchLive::kSportFootball) {
            $query = Match::query();
        } else {//if ($sport == MatchLive::kSportBasketball){
            $query = BasketMatch::query();
        }
        if (isset($lid)) {
            $query->where('lid', $lid);
        }
        $query->where(function ($orQuery) use ($name) {
            $orQuery->where('hname', 'like', '%' . $name  . '%');
            $orQuery->orWhere('aname', 'like', '%' . $name  . '%');
        });
        $query->where('time', '<=', date('Y-m-d H:i', strtotime('+10 hours')));
        $query->orderByDesc('time');
        $query->take(30);
        $query->selectRaw('*, id as mid');
        $matches = $query->get();

        return response()->json(['code'=>200, 'msg'=>'', 'matches'=>$matches]);
    }

    /**
     * 刷新爱球 录像终端/线路终端
     * @param $vid
     * @param null $ch_id
     */
    public function flushVideo($vid, $ch_id = null) {
        $url = 'http://cms.aikq.cc/static/subject/video/' . $vid;
        SubjectSpecimenController::excUrl($url);
        if (isset($ch_id)) {
            $ch_url = 'http://cms.aikq.cc/static/subject/video/channel/' . $ch_id;
            SubjectSpecimenController::excUrl($ch_url);
        }
    }

}