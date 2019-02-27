<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 12:38
 */

namespace App\Http\Controllers\Admin\Role;


use App\Models\Admin\AdResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ResourceController extends Controller
{

    /**
     * 源列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resources(Request $request) {
        $name = $request->input("name");
        $parent = $request->input("parent");
        $isMenu = $request->input("isMenu");

        $query = AdResource::query();
        if (!empty($name)) {
            $query->where("name", "like", "%$name%");
        }
        if (is_numeric($parent)) {
            $query->where("parent", "like", "%$parent%");
        }
        if (in_array($isMenu, [1, 2])) {
            $query->where("is_menu", "like", "%$isMenu%");
        }
        $query->orderBy("is_menu")->orderBy("od");
        $resources = $query->paginate(20);
        $resources->appends($request->all());
        $result['resources'] = $resources;
        return view("admin.resource.resource_list", $result);
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveRes(Request $request) {
        $id = $request->input("id");
        $name = $request->input("name");
        $action = $request->input("action");
        $parent = $request->input("parent");
        $is_menu = $request->input("is_menu");
        $menu_level = $request->input("menu_level");
        $od = $request->input("od");

        //判断参数 开始
        if (empty($name)) {
            return response()->json(["code"=>401, "msg"=>"权限名称不能为空"]);
        }
        if (empty($action)) {
            return response()->json(["code"=>401, "msg"=>"访问链接不能为空"]);
        }
        if (!empty($parent) && !is_numeric($parent)) {
            return response()->json(["code"=>401, "msg"=>"父权限错误". $parent]);
        }
        if ($parent > 0) {
            $parentRes = AdResource::query()->find($parent);
            if (!isset($parentRes)) {
                return response()->json(["code"=>401, "msg"=>"父权限不存在"]);
            }
        }

        if (!in_array($is_menu, [1, 2])) {
            return response()->json(["code"=>401, "msg"=>"目录设置错误"]);
        }
        if (isset($menu_level) && (!is_numeric($menu_level) || $menu_level < 0) ) {
            return response()->json(["code"=>401, "msg"=>"目录级别错误"]);
        }
        if (isset($od) && (!is_numeric($od) || $od < 0)) {
            return response()->json(["code"=>401, "msg"=>"排序错误"]);
        }
        //判断参数 结束

        if (is_numeric($id)) {
            $res = AdResource::query()->find($id);
        }
        if (!isset($res)) {
            $res = new AdResource();
        }

        try {
            $res->name = $name;
            $res->action = $action;
            $res->parent = $parent;
            $res->is_menu = $is_menu;
            $res->menu_level = $menu_level;
            $res->od = $od;
            $res->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(["code"=>500, "msg"=>"保存失败"]);
        }

        return response()->json(["code"=>200, "msg"=>"保存成功"]);
    }

    /**
     * 删除权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function delRes(Request $request) {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return response()->json(["code"=>401, "msg"=>"参数错误"]);
        }

        $res = AdResource::query()->find($id);
        if (!isset($res)) {
            return response()->json(["code"=>403, "msg"=>"权限不存在"]);
        }
        if (!$res->deleteResource()) {
            return response()->json(["code"=>200, "msg"=>"删除失败"]);
        }
        return response()->json(["code"=>200, "msg"=>"删除成功"]);
    }

}