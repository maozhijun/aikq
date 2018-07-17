<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 11:09
 */

namespace App\Http\Middleware;

use App\Http\Controllers\Backstage\BsController;
use App\Models\Anchor\Anchor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BackstageAuthVerify
{

    public function handle(Request $request, Closure $next)
    {
        if ($this->hasAuth($request)) {
            return $next($request);
        } else {
            if (isset($isJson)) {
                return response()->json(["code" => 444, "msg" => "请先登录"]);
            }
            return redirect('/backstage/login?target_url=' . urlencode(request()->fullUrl()));
        }
    }

    /**
     * 判断用户是否登录
     * 已登录则返回登录用户
     * 未登录则跳转到登录页面
     * @param Request $request
     * @return bool
     */
    public static function hasAuth(Request $request) {
        //检查session
        $aid = session(BsController::BS_LOGIN_SESSION);
        if (isset($aid)) {
            $anchor = Anchor::query()->find($aid);
            if (isset($anchor)) {
                $request->admin_user = $anchor;
                return true;
            }
        }
        $cookie = isset($_COOKIE[BsController::BS_LOGIN_SESSION]) ? $_COOKIE[BsController::BS_LOGIN_SESSION] : "";//获取cookie
        if (!empty($cookie)) {
            $aid = Redis::get($cookie);
            if (isset($aid)) {
                $anchor = Anchor::query()->find($aid);
                if (isset($anchor)) {
                    $request->admin_user = $anchor;
                    return true;
                }
            }
        }
        return false;
    }

}