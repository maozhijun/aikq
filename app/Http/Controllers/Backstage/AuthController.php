<?php

namespace App\Http\Controllers\Backstage;


use App\Models\Anchor\Anchor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    const BS_LOGIN_SESSION = 'AKQ_BS_LOGIN_SESSION';

    public function __construct()
    {
        $this->middleware('backstage_auth')->except(['login', 'logout']);
    }

    /**
     * 登录页面、登录逻辑
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function login(Request $request)
    {
        $method = $request->getMethod();
        if (strtolower($method) == "get") {//跳转到登录页面
            return view('backstage.index');
        }

        $target = $request->input("target");
        $phone = $request->input("phone", '');
        $password = $request->input("password");
        $remember = $request->input("remember", 0);

        $anchor = Anchor::query()->where("phone", $phone)->first();
        if (!isset($anchor)) {
            return back()->with(["error" => "账户或密码错误", 'phone'=>$phone]);
        }

        $salt = $anchor->salt;
        $pw = $anchor->passport;
        //判断是否登录
        if ($pw != Anchor::shaPassword($salt, $password)) {
            return back()->with(["error" => "账户或密码错误", 'phone'=>$phone]);
        }
        $target = empty($target) ? '/backstage/info' : $target;
        session([self::BS_LOGIN_SESSION => $anchor->id]);//登录信息保存在session
        if ($remember == 1) {
            //$c = cookie(self::BS_LOGIN_SESSION, $token, 60 * 24 * 7, '/', 'aikq.cc', false, true);
            return response()->redirectTo($target);//->withCookies([$c]);
        } else {
            return response()->redirectTo($target);
        }
        return back()->with(["error" => "账户或密码错误"]);
    }

    /**
     * 主播信息页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info(Request $request) {
        $anchor = $request->admin_user;
        $result['anchor'] = $anchor;
        return view('backstage.info', $result);
    }

    /**
     * 修改密码页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function passwordEdit(Request $request) {
        $method = $request->getMethod();
        if (strtolower($method) == "get") {//跳转到登录页面
            return view('backstage.password', []);
        }

        $target = $request->input("target", '/backstage/login');
        $old = $request->input("old", '');
        $new = $request->input("new");
        $copy = $request->input("copy", 0);

        if (empty($old)) {
            return back()->with(["error" => "当前密码不能为空"]);
        }
        if (empty($new)) {
            return back()->with(["error" => "新密码不能为空"]);
        }
        if ($new != $copy) {
            return back()->with(["error" => "两次输入的新密码不一致"]);
        }

        $anchor = $request->admin_user;
        $salt = $anchor->salt;
        $pw = $anchor->passport;
        //判断是否登录
        if ($pw != Anchor::shaPassword($salt, $old)) {
            return back()->with(["error" => "账户的原密码密码错误"]);
        }

        try {
            $anchor->passport = Anchor::shaPassword($salt, $new);
            $anchor->save();
        } catch (\Exception $exception) {
            return back()->with(["error" => "修改失败。"]);
        }

        session([self::BS_LOGIN_SESSION => null]);//清除登录信息
        setcookie(self::BS_LOGIN_SESSION, '', time() - 3600, '/', 'aikq.cc');
        $request->admin_user = null;//清除登录信息

        return response()->redirectTo($target);
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request) {
        $request->admin_user = null;
        session()->forget(self::BS_LOGIN_SESSION);
        setcookie(self::BS_LOGIN_SESSION, '', time() - 3600, '/', 'aikq.cc');
        return response()->redirectTo('/backstage/login');
    }

}
