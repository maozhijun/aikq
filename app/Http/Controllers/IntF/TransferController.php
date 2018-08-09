<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/9
 * Time: 9:52
 */

namespace App\Http\Controllers\IntF;


use App\Models\Api\ApiTransfer;
use App\Models\Api\WxUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;

class TransferController extends Controller
{
    const RANK_CACHE_KEY = "TransferController_Rank";

    public function __construct() {
        //$this->middleware('wx_base')->only('saveTransfer');
    }

    /**
     * 保存转会信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTransfer(Request $request) {
//        $openid = session('_openid');
//        if (empty($openid)) {
//            return response()->json(['code'=>401, 'mes'=>'请使用微信授权后再提交']);
//        }
        $name = $request->input('name');
        $club = $request->input('club');
        $money = $request->input('money');

        if (empty($name)) {
            return response()->json(['code'=>401, 'mes'=>'请填写球员名称']);
        }
        if (empty($club)) {
            return response()->json(['code'=>401, 'mes'=>'请提交球会信息']);
        }
        if (!is_numeric($money)) {
            return response()->json(['code'=>401, 'mes'=>'请提交转会费']);
        }

//        $wxUser = WxUser::query()->where("openid", $openid)->first();//微信服务号用户信息
//        if (!isset($wxUser)) {
//            $wxUser = new WxUser();
//            $wxUser->openid = $openid;
//            $wxUser->subscribe = 0;
//            $wxUser->save();
//        }
//        $uid = $wxUser->id;

        //$tran = ApiTransfer::query()->find($uid);
        $tran = ApiTransfer::query()->where('name', $name)->first();
        if (!isset($tran)) {
            $tran = new ApiTransfer();
        }
        $tran->name = $name;
        $tran->club = $club;
        $tran->money = $money;
        $rank = 999;
        try {
            $tran->save();
            $uid = $tran->id;
            $rankArray = $this->moneyRank(['uid'=>$uid, 'name'=>$name, 'club'=>$club, 'money'=>$money]);
            if (isset($rankArray[$uid])) {
                $rank = $rankArray[$uid]['number'];
            }
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'mes'=>'保存转会信息失败']);
        }
        return response()->json(['code'=>200, 'mes'=>'success', 'rank'=>$rank]);
    }

    /**
     * 转会排名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rank(Request $request) {
        $rankArray = $this->moneyRank(null);
        return view('transfer.rank', ["rankArray"=>$rankArray]);
        //return response()->json(['code'=>200, 'mes'=>'success', 'rank'=>$rankArray]);
    }

    /**
     * 使用redis保存排名
     * @param ApiTransfer $tran
     * @return array
     */
    public function moneyRank($tran){
        $key = self::RANK_CACHE_KEY;
        $rankStr = Redis::get($key);
        $rankArray = json_decode($rankStr, true);
        if (!isset($rankArray)) {
            $rankArray = [];
        }
        $uid = isset($tran['uid']) ? $tran['uid'] : null;
        if (!isset($uid)) {
            return $rankArray;
        }
        $rankArray[$uid] = $tran;
        //排序
        usort($rankArray, function ($a, $b) {
            $aMoney = isset($a['money']) ? $a['money'] : 0;
            $bMoney = isset($b['money']) ? $b['money'] : 0;
            if ($aMoney == $bMoney) {
                return 0;
            } else {
                return $bMoney > $aMoney ? 1 : -1;
            }
        });

        $cacheArray = [];
        foreach ($rankArray as $index=>$rank) {
            if ($index >= 50) break;
            $uid = $rank['uid'];
            $rank['number'] = $index + 1;
            $cacheArray[$uid] = $rank;
        }

        Redis::set($key, json_encode($cacheArray));
        return $cacheArray;
    }

}