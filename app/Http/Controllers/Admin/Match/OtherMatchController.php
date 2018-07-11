<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/23
 * Time: 11:16
 */

namespace App\Http\Controllers\Admin\Match;


use App\Models\Match\OtherMatch;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class OtherMatchController extends Controller
{

    /**
     * 自建赛事列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function matches(Request $request) {
        $type = $request->input('m_type');
        $lname = $request->input('lname');
        $name = $request->input('name');//节目/主队/客队名称
        $s_time = $request->input('s_time');
        $e_time = $request->input('e_time');
        $typeArray = OtherMatch::kTypeArray;

        $query = OtherMatch::query();
        if (isset($typeArray[$type])) {
            $query->where('type', $type);
        }
        if (!empty($lname)) {
            $query->where('lname', 'like', '%' . $lname . '%');
        }
        if (!empty($name)) {
            if ($type == OtherMatch::kTypeMenu) {
                $query->where('hname', 'like', '%' . $name . '%');
            } else {
                $query->where(function ($orQuery) use ($name) {
                    $orQuery->where('hname', 'like', '%' . $name . '%');
                    $orQuery->orWhere('aname', 'like', '%' . $name . '%');
                });
            }
        }
        if (!empty($s_time)) {
            $query->where('time', '>=', $s_time);
        }
        if (!empty($e_time)) {
            $query->where('time', '<=', $e_time);
        }
        $query->orderByDesc('time');
        $page = $query->paginate(10);
        $page->appends($request->all());
        
        $result['page'] = $page;
        $result['sport'] = MatchLive::kSportSelfMatch;
        $result['types'] = MatchLiveChannel::kTypeArrayCn;

        return view('admin.other.match.list', $result);
    }

    /**
     * 保存自建赛事
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOther(Request $request) {
        $id = $request->input('id');
        $hname = $request->input('hname');
        $aname = $request->input('aname');
        $time = $request->input('time');//开始时间
        $end_time = $request->input('end_time');//结束时间
        $lname = $request->input('lname');
        $type = $request->input('type');
        $project = $request->input('project');//项目
        $uid = $request->_account->id;

        //判断参数
        $type_array = OtherMatch::kTypeArray;
        if (!isset($type_array[$type])) {
            return response()->json(['code'=>401, 'msg'=>'类型参数错误']);
        }
        if ($type == OtherMatch::kTypeMatch && empty($lname)) {
            return response()->json(['code'=>401, 'msg'=>'赛事不能为空']);
        }
        if (empty($hname)) {
            if ($type == OtherMatch::kTypeMenu) {
                $h_name_error = "节目名称不能为空";
            } else {
                $h_name_error = "主队名称不能为空";
            }
            return response()->json(['code'=>401, 'msg'=>$h_name_error]);
        }
        if (mb_strlen($project) > 16) {
            return response()->json(['code'=>401, 'msg'=>"项目不能大于16字符"]);
        }
        if (empty($aname) && $type == OtherMatch::kTypeMatch) {
            return response()->json(['code'=>401, 'msg'=>'客队名称不能为空']);
        }
        if (empty($time)) {
            return response()->json(['code'=>401, 'msg'=>'开始时间不能为空']);
        }
        if (empty($end_time)) {
            return response()->json(['code'=>401, 'msg'=>'结束时间不能为空']);
        }
        if (strtotime($end_time) <= strtotime($time)) {
            return response()->json(['code'=>401, 'msg'=>'结束时间必须大于开始时间']);
        }
        try {
            if (is_numeric($id)) {
                $om = OtherMatch::query()->find($id);
            }
            if (!isset($om)) {
                $om = new OtherMatch();
                $om->ad_id = $uid;
            }
            $om->lname = $lname;
            $om->hname = $hname;
            $om->aname = $type == OtherMatch::kTypeMenu ? '' : $aname;
            $om->time = $time;
            $om->end_time = $end_time;
            $om->type = $type;
            $om->up_id = $uid;
            $om->project = $project;
            $om->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['code'=>401, 'msg'=>'保存自建赛事失败']);
        }

        return response()->json(['code'=>200, 'msg'=>'保存自建赛事成功']);
    }

}