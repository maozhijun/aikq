<?php
namespace App\Http\Controllers\Vendor\Weixin;

use App\Jobs\SendWXTemplateMessage;
use App\Models\CMS\Account;
use App\Models\Shop\Business\ChatRoom;
use App\Models\Shop\Business\GoodsArticles;
use App\Models\Shop\Business\Merchant;
use App\Models\Shop\Business\Order;
use App\Models\Shop\Business\WechatTemplate;
use App\Models\Shop\Business\WechatTemplateSendLog;
use App\Models\Shop\Customer\AccountFavor;
use App\Models\Shop\Customer\AppPushSetting;
use App\Models\Vendor\WxUser;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10
 * Time: 17:36
 */
class WeixinTampleMessage
{

    /**
     * 同步执行微信模板消息。
     * @param Application $app
     * @param $templateId
     * @param $url
     * @param $data
     * @param $openid
     * @return bool
     */
    public static function wechatMessage(Application $app, $templateId, $url, $data, $openid)
    {
        if (empty($templateId) || empty($url) || !is_array($data) || !isset($openid)) {
            return false;
        }

//        $wx_user = WxUser::query()->where("openid", $openid)->where("subscribe", 1);
//        if (!isset($wx_user)) {
//            return false;//没关注则不发送
//        }

        $app = isset($app) ? $app : new Application(config('wechat_lg'));
        $notice = $app->notice;
        try {
            $result = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openid)->send();
            Log::info($result);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 工具更新
     *
     * 资料发送提醒
     * x月x日
     *
     * 您订购的料狗工具有新的推荐信息。
     * 资料名称：VIP工具 - 莫罗预测
     * 资料简介：建议选择 莫斯巴达 让半球（07-19 00:30 俄超 莫斯科迪纳摩VS莫斯巴达）
     * 点击查看推荐理由。
     * @param Application $app
     * @param $openid
     * @param $first
     * @param $keyword1
     * @param $keyword2
     * @internal param $order
     */
    public static function liveTip(Application $app, $openid, $first, $keyword1, $keyword2 = '')
    {
        $templateId = "yYyxpYIO12FxkvfIfOLmfsfZH7eTrvM23XBse_IVBkU";
        $url = asset("/");
        //$first = '您订购的料狗工具有新的推荐信息。';
        //$keyword1 = "VIP工具 - " . $toolOrder->tool->name;
        //$keyword2 = $content;
        $remark = "点击查看。";

        $data = ["first" => $first, "keyword1" => $keyword1, "keyword2" => $keyword2, "remark" => $remark];
        self::wechatMessage($app, $templateId, $url, $data, $openid);
    }



}
