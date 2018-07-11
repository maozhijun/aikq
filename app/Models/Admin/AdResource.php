<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 11:33
 */

namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdResource extends Model
{
    const IS_MENU = 1;
    const FIRST_LEVEL = 1;//一级目录
    const SECOND_LEVEL = 2;//二级目录
    const THIRD_LEVEL = 3;//三级目录
    const Z_TREE_NODE_ARRAY = [];

    public function childrenMenus() {
        return $this->hasMany(self::class, 'parent', 'id')->where('is_menu', self::IS_MENU);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function children() {
        $query = self::query()->where("parent", $this->id);
        return $query->get();
    }

//    public function accountChildrenMenus($account_id) {
//        $query = self::query();
//        $query->where('ad_resources.is_menu', AdResource::IS_MENU);
//        $query->where('ad_resources.parent', $this->id);
//        if ($account_id != 1) {//不是超级管理员
//            $query->join('ad_role_resources', function ($join) {
//                $join->on('ad_resources.id', '=', 'ad_role_resources.re_id');
//            });
//            $query->join('ad_role_accounts', function ($join) {
//                $join->on('ad_role_resources.ro_id', '=', 'ad_role_accounts.role_id');
//            });
//            $query->where('ad_role_accounts.account_id', $account_id);
//        }
//        $query->orderBy("ad_resources.od")->orderBy("ad_resources.id");
//        $query->select("ad_resources.*");
//        return $query->get();
//    }

    public function parentResource() {
        return $this->hasOne(AdResource::class, 'id', 'parent');
    }

    public function deleteResource() {
        $adResource = $this;
        $exception = DB::transaction(function () use ($adResource) {
            //找出所有子项权限并删除。
            $adResource->deleteChildren([$adResource->id]);
            $adResource->delete();
            AdRoleResource::query()->where('re_id', $adResource->id)->delete();//删除  角色-权限 关系
        });
        if (isset($exception)) {
            Log::error($exception->getMessage());
        }
        return !isset($exception);
    }

    /**
     * 递归删除子权限
     * @param $id_array
     */
    protected function deleteChildren($id_array) {
        $query = self::query()->whereIn('parent', $id_array);
        $children_id_array = [];
        $resources = $query->get();
        if (isset($resources) && count($resources) > 0) {
            foreach ($resources as $re) {
                $children_id_array[] = $re->id;
            }
            $this->deleteChildren($children_id_array);
        }
        $query->delete();//删除子权限
        //删除  角色-权限 关系
        if (count($children_id_array) > 0) {
            AdRoleResource::query()->whereIn('re_id', $children_id_array)->delete();
        }
    }

    /**
     * 构成 zTree 树 返回指定格式的数组
     * [
     * { id:1, pId:0, name:"随意勾选 1", open:true},
     * { id:11, pId:1, name:"随意勾选 1-1", open:true}
     * ]
     * @param $role_id 角色id
     * @return array
     */
    public static function resource2zTreeNode($role_id = null) {
        $zNodes = [];
        $query = self::query();
        $query->where(function ($or) {
            $or->where("parent", 0);
            $or->orWhereNull("parent");
        });
        $firstMenus = $query->get();
        $roleResources = self::roleResourcesArray($role_id);
        $zNodes = self::createNodes($firstMenus, $zNodes, $roleResources);
        return $zNodes;
    }

    protected static function createNodes($menus, $zNodes = [], $roleResources = []) {
        foreach ($menus as $menu) {
            $pId = isset($menu->parent) ? $menu->parent : 0;
            $checked = isset($roleResources[$menu->id]);
            $zNodes[] = ["id"=>$menu->id, "pId"=>$pId, "name"=>$menu->name, "open"=>true, "checked"=>$checked];
            $children = $menu->children();//子权限
            if (isset($children) || count($children) > 0) {
                $zNodes = self::createNodes($children, $zNodes, $roleResources);
            }
        }
        return $zNodes;
    }

    /**
     * 获取角色所对应的权限
     * @param $role_id  角色id
     * @return array
     */
    protected static function roleResourcesArray($role_id = null) {
        $array = [];

        if (is_numeric($role_id)) {
            $query = self::query();
            $query->join('ad_role_resources', function ($join) {
                $join->on('ad_resources.id', '=', 'ad_role_resources.re_id');
            });
            $query->where('ad_role_resources.ro_id', $role_id);
            $query->select("ad_resources.*");
            $resources = $query->get();

            foreach ($resources as $resource) {
                $array[$resource->id] = $resource;
            }
        }

        return $array;
    }

}