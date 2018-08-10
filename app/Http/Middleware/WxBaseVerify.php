<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/9
 * Time: 14:09
 */

namespace App\Http\Middleware;


use App\Http\Controllers\Api\WxAuthController;
use App\Models\Api\WxUser;
use Illuminate\Http\Request;

class WxBaseVerify
{
    public function handle(Request $request, \Closure $next)
    {
        $userAgent = $request->header('user_agent', '');
        if (strpos($userAgent, 'MicroMessenger') !== false) {
            $openid = session('_openid');
            if ($openid) {
                $request->_openid = $openid;
            } else {
                $openid = isset($_COOKIE['wechat_openid']) ? $_COOKIE['wechat_openid'] : "";
                if (empty($openid)) {
                    $wxu = session('wechat.oauth_user');
                    if ($wxu) {
                        $wxUser = WxUser::query()->where("openid", $wxu->id)->first();//微信服务号用户信息
                        if (!isset($wxUser)) {
                            $wxUser = new WxUser();
                            $wxUser->openid = $wxu->id;
                            $wxUser->subscribe = 0;
                            $wxUser->save();
                        }
                        setcookie(WxAuthController::WX_OPENID_COOKIE_KEY, $wxu->id, null, '/', 'dlfyb.com');
                    }
                } else {
                    session(['_openid' => $openid]);
                    $request->_openid = $openid;
                }
            }
        }
        return $next($request);
    }
}