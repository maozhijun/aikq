<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/3
 * Time: 17:16
 */
namespace App\Http\Middleware;

use App\Models\Admin\Account;
use Closure;
use Illuminate\Http\Request;

class AdminAuthVerify
{

    public function handle(Request $request, Closure $next)
    {
        if ($this->hasAuth($request)) {
            $login = $request->_account;
            if (isset($login)) {
                if ($login->id != 1) {
                    $url = $request->url();
                    $start = stripos($url, '/admin');
                    $action = substr($url, $start);
                    if ($action != "/admin" && $action != "/admin/") {//首页每个角色都应该可以访问。
                        //答题串关，有主页权限则拥有所有答题串关的权限
                        $hasAccess = $login->hasAccess($action);
                        if (!$hasAccess) {
                            $method = $request->method();
                            if (strtolower($method) == 'post') {
                                return response()->json(["code"=>403, "msg"=>"没有权限"]);
                            } else {
                                return redirect('/admin/noatt');
                            }
                        }
                    }
                }
            }
            return $next($request);
        } else {
            return redirect('/admin/login/?target=' . urlencode(request()->fullUrl()));
        }
    }

    /**
     * 判断是登录
     * @param Request $request
     * @return bool
     */
    protected function hasAuth(Request $request)
    {
        $login = session(Account::AIKQ_ADMIN_AUTH_SESSION);
        if (isset($login)) {
            $request->_account = $login;
            return true;
        }
        if ($request->has(Account::AIKQ_ADMIN_AUTH_TOKEN)) {
            $token = $request->input(Account::AIKQ_ADMIN_AUTH_TOKEN);
        } else {
            $token = $request->cookie(Account::AIKQ_ADMIN_AUTH_TOKEN);
        }
        if (isset($token)) {
            $login = Account::query()->find($token);
            if (isset($login) && $login->status == 1) {
                if (strtotime($login->expired_at) > strtotime('now')) {
                    session([Account::AIKQ_ADMIN_AUTH_SESSION => $login]);
                    $request->_account = $login;
                    return true;
                }
            }
        }
        return false;
    }

}