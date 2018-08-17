<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/16
 * Time: 18:11
 */

namespace App\Http\Controllers\Admin\Sign;


use App\Models\Admin\LiveAccountSign;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiveAccountSignController extends Controller
{

    /**
     * 直播管理员打卡表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signs(Request $request) {
        $name = $request->input('name');
        $start = $request->input('start');
        $end = $request->input('end');
        $status = $request->input('status');

        $query = LiveAccountSign::query();
        $query->join('accounts', 'accounts.id', '=', 'live_account_signs.account_id');
        if (!empty($name)) {
            $query->where('accounts.name', 'like', '%' . $name . '%');
        }
        if (!empty($start)) {
            $query->where('live_account_signs.created_at', '>=', $start);
        }
        if (!empty($end)) {
            $query->where('live_account_signs.created_at', '<=', $end);
        }
        if (in_array($status, [1, 2])) {
            $query->where('live_account_signs.status', '=', $status);
        }
        $query->orderByDesc('created_at');
        $query->select('live_account_signs.*');
        $query->addSelect('accounts.name');
        $page = $query->paginate(20);
        $page->appends($request->all());
        $result['page'] = $page;
        return view('admin.sign.list', $result);
    }

    /**
     * 直播人员打卡页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signPage(Request $request) {
        $account = $request->_account;
        $query = LiveAccountSign::query()->where('account_id', $account->id);
        $query->orderByDesc('id');
        $sign = $query->first();
        $result['sign'] = $sign;
        return view('admin.sign.sign', $result);
    }

    /**
     * 直播管理员打卡
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSign(Request $request) {
        $account = $request->_account;
        $account_id = $account->id;

        $query = LiveAccountSign::query()->where('account_id', $account_id);
        $query->orderByDesc('id');
        $sign = $query->first();

        if (!isset($sign) || $sign->status == LiveAccountSign::kStatusOff) {
            $sign = new LiveAccountSign();
            $sign->account_id = $account_id;
            $sign->on_time = date('Y-m-d H:i:s');
            $sign->status = LiveAccountSign::kStatusOn;
        } else {
            $sign->status = LiveAccountSign::kStatusOff;
            $sign->off_time = date('Y-m-d H:i:s');
        }
        try {
            $sign->save();
        } catch (\Exception $exception) {
            return response()->json(['code'=>200, 'message'=>'打卡失败']);
        }
        return response()->json(['code'=>200, 'message'=>'打卡成功']);
    }

}