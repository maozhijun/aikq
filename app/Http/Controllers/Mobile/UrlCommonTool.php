<?php

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/8/27
 * Time: 12:47
 */
namespace App\Http\Controllers\Mobile;

use App\Models\LgMatch\Match;

class UrlCommonTool
{
    const MIP_PREFIX = "/mip";

    /*********************直播相关*************************/

    public static function homeLivesUrl() {
        return "/";
    }

    public static function matchLiveUrl($lid,$sport,$id){
        $str = 'other';
        if ($sport == 1){
            if (array_key_exists($lid,Match::path_league_football_arrays)){
                $str = Match::path_league_football_arrays[$lid];
            }
        }
        elseif($sport == 2){
            if (array_key_exists($lid,Match::path_league_basketball_arrays)){
                $str = Match::path_league_basketball_arrays[$lid];
            }
        }
        return '/'.$str.'/'.'live'.$sport.$id.'.html';
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