<?php

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/8/27
 * Time: 12:47
 */
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\Live\SubjectVideoController;
use App\Models\LgMatch\Match;
use App\Models\Subject\SubjectLeague;

class UrlCommonTool
{
    const MOBILE_STATIC_PATH = "/m";
    const MOBILE_PREFIX = "/m";

    /*********************直播相关*************************/

    public static function homeLivesUrl() {
        return "/";
    }

    public static function matchLiveUrl($lid,$sport,$id){
        return CommonTool::getLiveDetailUrl($sport, $lid, $id);
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
//        return '/'.$str.'/'.'live'.$sport.$id.'.html';
    }

    /*********************录像相关*************************/

    public static function homeVideosUrl($type = "all", $page = 1) {
        return self::MOBILE_PREFIX."/live/subject/videos/$type/$page.html";
    }

    public static function matchVideoUrl($vid) {
        $first = substr($vid, 0, 2);
        $second = substr($vid, 2, 2);
        return "/live/subject/video/$first/$second/$vid.html";
    }


    /*********************主播相关*************************/

    public static function homeAnchorUrl() {
        return self::MOBILE_PREFIX."/anchor/index.html";
    }

    public static function anchorRoomUrl($roomId) {
        return self::MOBILE_PREFIX."/anchor/room/$roomId.html";
    }


    /*********************文章相关*************************/

    public static function homeNewsUrl() {
        return self::MOBILE_PREFIX."/news/";
    }

    public static function newsForPageUrl($type) {
        return self::MOBILE_PREFIX."/news/$type";
    }

    /**
     * 获取文章终端静态化页面路径
     * @param $name_en
     * @param $id
     * @return string
     */
    public static function getArticleDetailPath($name_en, $id) {
        $len = strlen($id);
        if ($len < 4) {
            return "";
        }
        $first = substr($id, 0, 2);
        $second = substr($id, 2, 3);
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (isset($sl)) {
            $path = "/".$name_en."/news/".$first."/".$second."/".$id.".html";
        } else {
            $path = "/news/".$name_en."/".$first."/".$second."/".$id.".html";
        }
        return $path;
    }

    protected static function getSubjectLeagueNameEn($lid) {
        $name_en = "";
        if ($lid != 'all' && $lid != 999) {
            $videoIntF = new SubjectVideoController();
            $leagues = $videoIntF->getLeagues();
            if (isset($leagues[$lid])) {
                $name_en = $leagues[$lid]['name_en'];
            }
        } else if ($lid == 999) {
            $name_en = "other";
        }
        return $name_en;
    }

    /*********************专题相关*************************/

    public static function subjectUrl($name) {
        return self::MOBILE_PREFIX."/$name/";
    }

    public static function getTeamDetailUrl($sport, $lid, $tid) {
        $url = CommonTool::getTeamDetailUrl($sport, $lid, $tid);
        return $url;
    }


    /*********************其他*************************/

    public static function downloadUrl() {
//        return self::MIP_PREFIX."/download.html";
        return "/mip/downloadPhone.html";
    }
}