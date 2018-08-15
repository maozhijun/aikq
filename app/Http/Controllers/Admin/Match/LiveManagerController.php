<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/15
 * Time: 11:07
 */

namespace App\Http\Controllers\Admin\Match;


use App\Models\Match\LiveDuty;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiveManagerController extends Controller
{

    /**
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $name = $request->input('name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $query = LiveDuty::query();
        if (!empty($name)) {
            $query->where('name', '=', $name);
        }
        if (!empty($start_date)) {
            $query->where('start_date', '>=', $start_date);
        } else {
            $date = date('Y-m-d', strtotime('-1 days'));
            $query->where('start_date', '>=', $date);
        }
        if (!empty($end_date)) {
            $query->where('end_date', '<=', $end_date);
        }

        $query->orderBy('start_date');

        $page = $query->paginate(20);
        $page->appends($request->all());
        $result = [];
        $result['page'] = $page;
        $result['managers'] = LiveDuty::Duty_User_Array;
        return view('admin.duty.list', $result);
    }

    /**
     * 保存值班记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDuty(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $openid = $request->input('openid');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (empty($name)) {
            return response()->json(['code'=>401, 'message'=>'值班人员名称不能为空']);
        }
        if (empty($openid)) {
            return response()->json(['code'=>401, 'message'=>'值班人员微信联系方式错误']);
        }
        if (empty($start_date)) {
            return response()->json(['code'=>401, 'message'=>'值班开始时间不能为空']);
        }
        if (empty($end_date)) {
            return response()->json(['code'=>401, 'message'=>'值班结束时间不能为空']);
        }
        $start_time = strtotime($start_date);
        $end_time = strtotime($end_date);
        if ($start_time >= $end_time) {
            return response()->json(['code'=>401, 'message'=>'值班结束时间必须大于开始时间']);
        }

        $duty = LiveDuty::query()->find($id);
        if (!isset($duty)) {
            $duty = new LiveDuty();
        }
        $duty->name = $name;
        $duty->start_date = $start_date;
        $duty->end_date = $end_date;
        $duty->openid = $openid;
        try {
            $duty->save();
        } catch (\Exception $exception) {
            response()->json(['code'=>500, 'message'=>'保存失败']);
        }
        return response()->json(['code'=>200, 'message'=>'保存成功']);
    }

    /**
     * 删除值班
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delDuty(Request $request) {
        $id = $request->input('id');
        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'message'=>'参数错误']);
        }
        $duty = LiveDuty::query()->find($id);
        try {
            if (isset($duty)) {
                $duty->delete();
            }
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'message'=>'删除失败']);
        }
        return response()->json(['code'=>200, 'message'=>'删除成功']);
    }

}