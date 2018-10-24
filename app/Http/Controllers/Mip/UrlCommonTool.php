<?php

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/8/27
 * Time: 12:47
 */
namespace App\Http\Controllers\Mip;

use App\Http\Controllers\PC\CommonTool;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\Match;

class UrlCommonTool
{
    const MIP_STATIC_PATH = "/mip";
//    const MIP_PREFIX = env("MIP_URL", "http://yingchaozhibo.cc");
    const MIP_PREFIX = "//mip.aikanqiu.com";

    protected static function getMipPrefix() {
        return env('MIP_URL', self::MIP_PREFIX);
    }

    /*********************直播相关*************************/

    public static function homeLivesUrl($prefix = self::MIP_PREFIX) {
        return '/';
        return $prefix."/";
    }

    public static function matchLiveUrl($lid, $sport, $id, $prefix = self::MIP_PREFIX) {
        $url = CommonTool::getLiveDetailUrl($sport, $lid, $id);
//        $str = 'other';
//        if ($sport == 1){
//            if (array_key_exists($lid,Match::path_league_football_arrays)){
//                $str = Match::path_league_football_arrays[$lid];
//            }
//        }
//        elseif($sport == 2){
//            if (array_key_exists($lid,Match::path_league_basketball_arrays)){
//                $str = Match::path_league_basketball_arrays[$lid];
//            }
//        }
        return $prefix.$url;
    }


    /*********************录像相关*************************/

    public static function homeVideosUrl($type = "all", $page = 1, $prefix = self::MIP_PREFIX) {
        return $prefix."/live/subject/videos/$type/$page.html";
    }

    public static function matchVideoUrl($vid, $prefix = self::MIP_PREFIX) {
        $first = substr($vid, 0, 2);
        $second = substr($vid, 2, 2);
        return $prefix."/live/subject/video/$first/$second/$vid.html";
    }


    /*********************主播相关*************************/

    public static function homeAnchorUrl($prefix = self::MIP_PREFIX) {
        return $prefix."/anchor/";
    }

    public static function anchorRoomUrl($roomId, $prefix = self::MIP_PREFIX) {
        return $prefix."/anchor/room/$roomId.html";
    }


    /*********************文章相关*************************/

    public static function homeNewsUrl($prefix = self::MIP_PREFIX) {
        return $prefix."/news/";
    }

    public static function newsForPageUrl($type) {
        $prefix = env("API_URL", "http://api.aikq.cc");
        return $prefix."/mip/news/$type";
    }

    public static function newsDetailUrl(PcArticle $article, $prefix = self::MIP_PREFIX) {
        return $prefix.$article->getUrl();
    }

    /*********************专题相关*************************/

    public static function subjectUrl($name, $prefix = self::MIP_PREFIX) {
        return $prefix."/$name/";
    }


    /*********************其他*************************/

    public static function downloadUrl() {
//        return self::MIP_PREFIX."/download.html";
        return self::getMipPrefix()."/downloadPhone.html";
    }


    public static function getMobileUrl() {
        return self::convertHost(env('M_URL'));
    }

    public static function convertHost($host) {
        if (!starts_with($host, "http")) {
            $host = "https://$host";
        }
        return $host;
    }
}