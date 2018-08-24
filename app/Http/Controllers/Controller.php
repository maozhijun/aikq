<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    const SUBJECT_NAME_IDS = [
        "zhongchao"=>1002,
        "yingchao"=>1000,
        "xijia"=>1003,
        "yijia"=>1004,
        "fajia"=>1005,
        "dejia"=>1006,
//        "j1"=>1002,
//        "k1"=>1002,
//        "aojia"=>1002,
        "nba"=>1009,
        "cba"=>1010,
        "worldcup"=>1008,
        "uefacl"=>1001,
        "uefael"=>1011,
        "afccl"=>1007,
    ];

    protected $html_var = [];

    function __construct()
    {
        $this->html_var['title'] = '爱看球-爱看球直播|爱看球-JRS|JRS直播|NBA直播|NBA录像|CBA直播|英超直播|西甲直播|低调看|直播吧|CCTV5在线';
        $this->html_var['keywords'] = '爱看球,爱看球直播,JRS,JRS直播,NBA直播,NBA录像,CBA直播,英超直播,西甲直播,足球直播,篮球直播,低调看,直播吧,CCTV5在线,CCTV5+';
        $this->html_var['description'] = '爱看球是一个专业为球迷提供免费的NBA,CBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大体育赛事直播、解说平台，无广告，无插件，高清，直播线路多';
    }

    static public function isMobile(Request $request)
    {
        $userAgent = $request->header('user_agent', '');
        if ($userAgent) {
            $userAgent = $request->header('user_agent', '');
            if (preg_match("/(iPad).*OS\s([\d_]+)/", $userAgent)) {
                return true;
            }
            else if (preg_match("/(iPhone\sOS)\s([\d_]+)/", $userAgent)){
                return true;
            }
            else if (preg_match("/(Android)\s+([\d.]+)/", $userAgent)){
                return true;
            }
        }
        return false;
    }

    public static function isMobileUAgent($userAgent) {
        if ($userAgent) {
            if (preg_match("/(iPad).*OS\s([\d_]+)/", $userAgent)) {
                return true;
            }
            else if (preg_match("/(iPhone\sOS)\s([\d_]+)/", $userAgent)){
                return true;
            }
            else if (preg_match("/(Android)\s+([\d.]+)/", $userAgent)){
                return true;
            }
        }
        return false;
    }

    public static function execUrl($url, $timeout = 5, $isHttps = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//8秒超时
        if ($isHttps) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $server_out = curl_exec ($ch);
        curl_close ($ch);
        return $server_out;
    }

}
