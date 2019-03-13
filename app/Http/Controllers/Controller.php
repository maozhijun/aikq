<?php

namespace App\Http\Controllers;

use App\Models\LgMatch\Match;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    const
        SUBJECT_NAME_IDS = [
        "zhongchao"=>['id'=>1002, 'name'=>'中超','lid'=>46],
        "yingchao"=>['id'=>1000, 'name'=>'英超','lid'=>31],
        "xijia"=>['id'=>1003, 'name'=>'西甲','lid'=>26],
        "yijia"=>['id'=>1004, 'name'=>'意甲','lid'=>29],
        "fajia"=>['id'=>1005, 'name'=>'法甲','lid'=>11],
        "dejia"=>['id'=>1006, 'name'=>'德甲','lid'=>8],
////        "j1"=>['id'=>1002, 'name'=>'J联赛'],
////        "k1"=>['id'=>1002, 'name'=>'K联赛'],
////        "aojia"=>['id'=>1002, 'name'=>'澳甲'],
        "nba"=>['id'=>1009, 'name'=>'NBA','lid'=>1],
        "cba"=>['id'=>1010, 'name'=>'CBA','lid'=>4],
//        "worldcup"=>['id'=>1008, 'name'=>'世界杯','lid'=>57],
        "uefacl"=>['id'=>1001, 'name'=>'欧冠杯','lid'=>73],
        "uefael"=>['id'=>1011, 'name'=>'欧罗巴杯','lid'=>77],
        "afccl"=>['id'=>1007, 'name'=>'亚冠杯','lid'=>139],
    ];

    const MATCH_LEAGUE_IDS = [
        "1-46"=>["name_en"=>"zhongchao", "sid"=>1002, 'name'=>'中超'],
        "1-31"=>["name_en"=>"yingchao", "sid"=>1000, 'name'=>'英超'],
        "1-26"=>["name_en"=>"xijia", "sid"=>1003, 'name'=>'西甲'],
        "1-29"=>["name_en"=>"yijia", "sid"=>1004, 'name'=>'意甲'],
        "1-11"=>["name_en"=>"fajia", "sid"=>1005, 'name'=>'法甲'],
        "1-8"=>["name_en"=>"dejia", "sid"=>1006, 'name'=>'德甲'],
//        "1-57"=>["name_en"=>"worldcup", "sid"=>1008, 'name'=>'世界杯'],
        "1-73"=>["name_en"=>"uefacl", "sid"=>1001, 'name'=>'欧冠杯'],
        "1-77"=>["name_en"=>"uefael", "sid"=>1011, 'name'=>'欧罗巴杯'],
        "1-139"=>["name_en"=>"afccl", "sid"=>1007, 'name'=>'亚冠杯'],
        "2-1"=>["name_en"=>"nba", "sid"=>1009, 'name'=>'NBA'],
        "2-4"=>["name_en"=>"cba", "sid"=>1010, 'name'=>'CBA'],
    ];

    protected $html_var = [];

    function __construct()
    {
        $this->html_var['title'] = '爱看球-爱看球直播|爱看球-JRS|JRS直播|NBA直播|NBA录像|CBA直播|英超直播|西甲直播|低调看|直播吧|CCTV5在线';
        $this->html_var['keywords'] = '爱看球,爱看球直播,JRS,JRS直播,NBA直播,NBA录像,CBA直播,英超直播,西甲直播,足球直播,篮球直播,低调看,直播吧,CCTV5在线,CCTV5+';
        $this->html_var['description'] = '爱看球是一个专业为球迷提供免费的NBA,CBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大体育赛事直播、解说平台，无广告，无插件，高清，直播线路多';
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();//self::SUBJECT_NAME_IDS;
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
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ($code >= 400 || empty($server_out)) {
            return "";
        }
        return $server_out;
    }

    protected function onHtmlStatic($html, $path) {
        try {
            if (!empty($html)) {
                Storage::disk("public")->put($path, $html);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    protected static function getMobileHttpUrl($path) {
        $mobileUrl = env('M_URL');
        if (!starts_with($mobileUrl, "http")) {
            if (starts_with($mobileUrl, "//")) {
                $mobileUrl = "https:".$mobileUrl;
            } else {
                $mobileUrl = "https://".$mobileUrl;
            }
        }
        return $mobileUrl.$path;
    }
}
