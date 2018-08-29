<?php

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/8/27
 * Time: 12:47
 */
namespace App\Http\Controllers\Mip;

class UrlCommonTool
{
    const MIP_STATIC_PATH = "/mip";
    const MIP_PREFIX = "/mip";

    /*********************直播相关*************************/

    public static function homeLivesUrl() {
        return self::MIP_PREFIX."/lives.html";
    }

    public static function matchLiveUrl($sport, $mid) {
        switch ($sport) {
            case 3:
                $url = self::MIP_PREFIX."/live/other/$mid.html";
                break;
            case 2:
                $url = self::MIP_PREFIX."/live/basketball/$mid.html";
                break;
            case 1:
            default:
                $url = self::MIP_PREFIX."/live/football/$mid.html";
                break;
        }
        return $url;
    }


    /*********************录像相关*************************/

    public static function homeVideosUrl($type = "all", $page = 1) {
        return self::MIP_PREFIX."/live/subject/videos/$type/$page.html";
    }

    public static function matchVideoUrl($vid) {
        $first = substr($vid, 0, 2);
        $second = substr($vid, 2, 2);
        return "/live/subject/video/$first/$second/$vid.html";
    }


    /*********************主播相关*************************/

    public static function homeAnchorUrl() {
        return self::MIP_PREFIX."/anchor/index.html";
    }

    public static function anchorRoomUrl($roomId) {
        return self::MIP_PREFIX."/anchor/room/$roomId.html";
    }


    /*********************文章相关*************************/

    public static function homeNewsUrl() {
        return self::MIP_PREFIX."/news/";
    }

    public static function newsForPageUrl($type) {
        return self::MIP_PREFIX."/news/$type";
    }

    /*********************专题相关*************************/

    public static function subjectUrl($name) {
        return self::MIP_PREFIX."/$name/";
    }


    /*********************其他*************************/

    public static function downloadUrl() {
//        return self::MIP_PREFIX."/download.html";
        return "/mip/downloadPhone.html";
    }
}