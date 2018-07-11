<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 11:41
 */

namespace App\Http\Controllers\Admin\Role;


use App\Models\Admin\AdResource;
use App\Models\Admin\AdRole;
use App\Models\Admin\AdRoleAccount;
use App\Models\Admin\AdRoleResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    /**
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $name = $request->input("name");

        $query = AdRole::query();
        if (!empty($name)) {
            $query->where("name", "like", "%$name%");
        }
        $roles = $query->paginate(20);
        $roles->appends($request->all());
        $result["roles"] = $roles;
        return view("admin.role.role_list", $result);
    }

    /**
     * 修改、新建页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request) {
        $id = $request->input("id");
        if (is_numeric($id)) {
            $role = AdRole::query()->find($id);
            $result['role'] = $role;
        }
        $result["zNodes"] = json_encode(AdResource::resource2zTreeNode($id));
        return view("admin.role.role_detail", $result);
    }

    /**
     * 保存角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveRole(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');//角色名称
        $resources = $request->input('resources');//权限id，使用 逗号 分隔开。

        if (empty($name)) {
            return response()->json(['code'=>401, "msg"=>"角色名称不能为空"]);
        }

        if (is_numeric($id) && $id > 0) {
            $role = AdRole::query()->find($id);
        }

        if (!isset($role)) {
            $role = new AdRole();
        }

        $role->name = $name;
        $new_resource_array = [];
        if (!empty($resources)) {
            $new_resource_array = explode(",", $resources);//角色的权限
        }

        $exception = DB::transaction(function () use ($role, $new_resource_array) {
            if (isset($role->id)) {
                $role->save();//更新
                $role_resources = $role->resources;
                //排除重复的权限，删除去除的权限
                if (isset($role_resources) && count($role_resources) > 0) {
                    $old_re_array = [];
                    foreach ($role_resources as $rr) {
                        $old_re_array[] = $rr->id . '';
                    }
                    $add_array = [];
                    foreach ($new_resource_array as $new_re) {
                        if (!in_array($new_re, $old_re_array)) {//新的不在旧的里面则增加。
                            $add_array[] = $new_re;
                        }
                    }
                    foreach ($old_re_array as $index=>$old_re) {
                        if (in_array($old_re, $new_resource_array)) {
                            unset($old_re_array[$index]);
                        }
                    }
                    foreach ($add_array as $add_id) {
                        $role_resource = new AdRoleResource();
                        $role_resource->ro_id = $role->id;
                        $role_resource->re_id = $add_id;
                        $role_resource->save();
                    }
                    AdRoleResource::query()->where('ro_id', $role->id)->whereIn('re_id', $old_re_array)->delete();
                } else {
                    foreach ($new_resource_array as $re_id) {
                        $role_resource = new AdRoleResource();
                        $role_resource->ro_id = $role->id;
                        $role_resource->re_id = $re_id;
                        $role_resource->save();
                    }
                }
            } else {
                $role->save();//新增
                foreach ($new_resource_array as $re_id) {
                    $role_resource = new AdRoleResource();
                    $role_resource->ro_id = $role->id;
                    $role_resource->re_id = $re_id;
                    $role_resource->save();
                }
            }
        });

        if (isset($exception)) {//保存错误
            return response()->json(["code"=>500, "msg"=>""]);
        }

        return response()->json(["code"=>0, "msg"=>"保存成功", "id"=>$role->id]);
    }

    /**
     * 删除角色
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delRole(Request $request) {
        $id = $request->input("id");
        if (!is_numeric($id) || $id <= 0) {
            return response()->json(["code"=>401, "msg"=>"参数错误"]);
        }

        $role = AdRole::query()->find($id);
        if (!isset($role)) {
            return response()->json(["code"=>403, "msg"=>"角色不存在"]);
        }
        $exception = DB::transaction(function () use ($role) {
            //删除角色-权限关系
            AdRoleResource::query()->where('ro_id', $role->id)->delete();
            //删除角色-用户关系
            AdRoleAccount::query()->where('role_id', $role->id)->delete();
            $role->delete();
        });
        if (isset($exception)) {
            return response()->json(["code"=>500, "msg"=>"删除角色失败"]);
        }
        return response()->json(["code"=>0, "msg"=>"删除角色成功"]);

    }

}