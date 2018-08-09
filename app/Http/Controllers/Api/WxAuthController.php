<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/9
 * Time: 12:47
 */

namespace App\Http\Controllers\Api;


use App\Models\Api\WxUser;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WxAuthController extends Controller
{

    const WX_OPENID_COOKIE_KEY = 'wechat_openid';

    public function __construct() {
        $this->middleware('wx_auth')->only('wxAuthToOther');
        //$this->middleware('wx_auth:snsapi_userinfo')->only('wxAuthToOtherByUserInfo');
    }

    /**
     * 给第三方应用微信静默授权
     * @param Request $request
     * @return mixed
     */
    public function wxAuthToOther(Request $request)
    {
        $wxu = session('wechat.oauth_user');
        if ($wxu) {
            $wxUser = WxUser::query()->where("openid", $wxu->id)->first();//微信服务号用户信息
            if (!isset($wxUser)) {
                $wxUser = new WxUser();
                $wxUser->openid = $wxu->id;
                $wxUser->subscribe = 0;
                $wxUser->save();
            }
            setcookie(self::WX_OPENID_COOKIE_KEY, $wxu->id, null, '/', 'dlfyb.com');
        }
        $targetUrl = $request->input("target_url", "http://mp.dlfyb.com");
        return redirect($targetUrl);
    }

    public function jsSign(Request $request, Application $app)
    {
        $js = $app->js;
        if ($request->has('url')) {
            $js->setUrl(urldecode($request->input('url')));
        }
        $apis = [];
        if ($request->has('apis')) {
            $apis = explode(',', $request->input('apis'));
        }
        $debug = false;
        if ($request->has('debug')) {
            $debug = ($request->input('debug') == 'true');
        }
        if ($request->has('callback')) {
            return response()->jsonp($request->input('callback'), $js->config($apis, $debug, false, false));
        } else {
            return response()->json($js->config($apis, $debug, false, false));
        }
    }

}