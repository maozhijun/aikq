<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/13
 * Time: 下午3:12
 */

namespace App\Http\Controllers\Admin\Anchor;

use App\Http\Controllers\Admin\UploadTrait;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AnchorController extends Controller
{
    use UploadTrait;
    /**
     * 主播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function anchors(Request $request){
        $query = Anchor::query();
        $query->orderByDesc('created_at');
        $page = $query->paginate(15);

        $result['page'] = $page;
        return view('admin.anchor.anchor_list', $result);
    }

    /**
     * 更新主播信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request){
        if ($request->input('id',0) == 0){
            return back()->with('error', 'id不能为空');
        }
        $anchor = Anchor::find($request->input('id'));
        if (is_null($anchor)){
            return back()->with('error', '找不到该主播');
        }
        $anchor->name = $request->input('name');
        $anchor->phone = $request->input('phone');
        $anchor->hot = $request->input('hot');

        if ($request->hasFile("icon")) {
            $icon = $this->saveUploadedFile($request->file("icon"), 'cover');
            $anchor->icon = $icon->getUrl();
        }

        if ($anchor->save()){
            return back()->with('success', '保存成功');
        }
        else{
            return back()->with('error', '保存失败');
        }
    }

    /**
     * 删除主播
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delAnchor(Request $request){
        if ($request->input('id',0) == 0){
            return response()->json(array('error'=>'id不能为空'));
        }
        $anchor = Anchor::find($request->input('id'));
        if (is_null($anchor)){
            return response()->json(array('error'=>'找不到该主播'));
        }
        if ($anchor->delete()){
            return response()->json(array('success'=>'删除成功'));
        }
        else{
            return response()->json(array('error'=>'删除失败'));
        }
    }

    public function register(Request $request){
        return view('admin.anchor.anchor_register');
    }

    public function create(Request $request){
        $phone = $request->input("phone");//只能是数字和字母的组合
        $password = $request->input("password");//加密后的密码
        $re_password = $request->input("re_password");//加密后的密码
        $name = $request->input("name");//用户名称

        if (empty($phone) || empty($password) || empty($re_password) || empty($name)) {
            return response()->json(["code" => 401, "msg" => "参数错误"]);
        }

        if ($password != $re_password) {
            return response()->json(["code" => 401, "msg" => "两次输入的密码不一致"]);
        }

        $admin = Anchor::query()
            ->where("phone", $phone)
            ->orwhere("name", $name)
            ->first();
        if (isset($admin)) {
            return response()->json(["code" => 403, "msg" => "此手机号或主播名已有账户"]);
        }

        try {
            DB::transaction(function () use ($phone, $name, $password) {
                $salt = uniqid();
                $admin = new Anchor();
                $admin->phone = $phone;
                $admin->name = $name;
                $admin->salt = $salt;
                $admin->passport = Anchor::shaPassword($salt, $password);
                $admin->save();

                $room = new AnchorRoom();
                $room->anchor_id = $admin->id;
                $room->title = $admin->name.'的直播间';
                $room->save();
            });
            return response()->json(["code" => 0, "msg" => "success"]);
        } catch (\Exception $e) {
            Log::info('create merchant error : ' . $e->getMessage());
            return response()->json(["code" => 500, "msg" => "数据库异常",'e'=>$e->getMessage()]);
        }
    }
}