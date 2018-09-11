<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/3
 * Time: 17:48
 */

namespace App\Http\Controllers\Admin\Subject;


use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectSpecimen;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class SubjectSpecimenController extends Controller
{
    /**
     * 集锦列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specimens(Request $request) {
        $s_lid = $request->input('s_lid');
        $title = $request->input('title');
        $query = SubjectSpecimen::query();
        if (is_numeric($s_lid)) {
            $query->where('s_lid', $s_lid);
        }
        if (!empty($title)) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        $query->selectRaw('*, ifNull(od, 999) as n_od');
        $query->orderBy('n_od')->orderByDesc('updated_at');
        $page = $query->paginate(15);
        $page->appends($request->all());

        $leagues = SubjectLeague::getAllLeagues();
        $result['page'] = $page;
        $result['leagues'] = $leagues;
        return view('admin.subject.specimen.list', $result);
    }

    /**
     * 编辑页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request) {
        $id = $request->input('id');

        $leagues = SubjectLeague::getAllLeagues();
        $players = MatchLiveChannel::kPlayerArrayCn;

        $result['leagues'] = $leagues;
        $result['players'] = $players;
        if (is_numeric($id)) {
            $result['specimen'] = SubjectSpecimen::query()->find($id);
        }
        return view('admin.subject.specimen.edit', $result);
    }

    /**
     * 保存集锦
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSpecimen(Request $request) {
        $id = $request->input('id');
        $title = $request->input('title');
        $link = $request->input('link');
        $s_lid = $request->input('s_lid');
        $player = $request->input('player');
        $show = $request->input('show');
        $od = $request->input('od');
        $time = $request->input('time');
        $platform = $request->input('platform', MatchLiveChannel::kPlatformPC);
        $cover = $request->input('cover');//封面

        $players = MatchLiveChannel::kPlayerArrayCn;

        //判断参数 开始
        if (empty($title)) {
            return response()->json(['code'=>401, 'msg'=>'标题不能为空']);
        }
        if (mb_strlen($title) > 16) {
            return response()->json(['code'=>401, 'msg'=>'标题不能大于16字符']);
        }
        if (empty($link)) {
            return response()->json(['code'=>401, 'msg'=>'播放链接不能为空']);
        }
        if (!is_numeric($s_lid)) {
            return response()->json(['code'=>401, 'msg'=>'请选择专题赛事']);
        }
        if (!isset($players[$player])) {
            return response()->json(['code'=>401, 'msg'=>'播放方式错误']);
        }
        if (!in_array($show, [SubjectSpecimen::kShow, SubjectSpecimen::kHide])) {
            return response()->json(['code'=>401, 'msg'=>'是否显示参数错误']);
        }
        if (empty($cover)) {
            return response()->json(['code'=>401, 'msg'=>'请先上传集锦封面']);
        }
        if (empty($time)) {
            return response()->json(['code'=>401, 'msg'=>'请填写比赛时间']);
        }
        if (!empty($od) && !is_numeric($od)) {
            return response()->json(['code'=>401, 'msg'=>'排序必须为正整数']);
        }
        $sl = SubjectLeague::query()->find($s_lid);
        if (!isset($sl)) {
            return response()->json(['code'=>401, 'msg'=>'您选择的专题不存在']);
        }
        //判断参数 结束

        try {
            if (is_numeric($id)) {
                $specimen = SubjectSpecimen::query()->find($id);
            }
            if (!isset($specimen)) {
                $specimen = new SubjectSpecimen();
            }
            $specimen->title = $title;
            $specimen->link = $link;
            $specimen->s_lid = $s_lid;
            $specimen->player = $player;
            $specimen->show = $show;
            $specimen->platform = $platform;
            $specimen->cover = $cover;
            $specimen->od = $od;
            $specimen->time = $time;
            $specimen->save();
            $this->flushSpecimen($specimen->id);
        } catch (\Exception $exception) {
            Log::error($exception);
        }

        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

    /**
     * 删除集锦
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSpecimens(Request $request) {
        $id = $request->input('id');
        if (is_numeric($id)) {
            $specimen = SubjectSpecimen::query()->find($id);
            if (isset($specimen)) {
                $specimen->delete();
                $this->flushSpecimen($id);
            }
        }
        return response()->json(['code'=>200, 'msg'=>'删除成功']);
    }

    /**
     * 刷新爱球集锦终端，线路
     * @param $sid
     */
    public function flushSpecimen($sid) {
        $url = 'http://cms.aikq.cc/static/subject/specimen/' . $sid;
        self::excUrl($url, 2);
    }

    public static function excUrl($url, $timeout = 1) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

}