<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/5
 * Time: 上午11:06
 */

namespace App\Http\Controllers\PC;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Http\Controllers\PC\Live\SubjectVideoController;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\OtherMatch;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use Illuminate\Support\Facades\Storage;

class CommonTool
{
    //比赛类型
    const kSportFootball = 1, kSportBasketball = 2;//1：足球，2：篮球，其他待添加。

    /**
     * 根据比赛id返回live path
     * @param $mid
     * @param int $sport
     * @return string
     */
    public static function matchLivePathWithId($mid,$sport=CommonTool::kSportFootball){
        $path = '';
        if ($mid > 1000) {
            if ($sport == 2) {
                $path = '/live/basketball/' . $mid . '.html';
            } else {
                $path = '/live/football/' . $mid . '.html';
            }
        }
        return $path;
    }

    public static function matchWapLivePathWithId($mid,$sport=CommonTool::kSportFootball){
        $path = '/m'. CommonTool::matchLivePathWithId($mid,$sport);
        return $path;
    }

    /**************** match类 *****************/
    /**
     * 获取足球比赛的即时时间
     * @param $time
     * @param $timehalf
     * @param $status
     * @return string
     */
    public static function getMatchWapCurrentTime($time, $timehalf, $status)
    {
        $time = $timehalf > 0 ? $timehalf : $time;
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($status < 0 || $status == 2 || $status == 4) {
            $matchTime = self::getStatusTextCn($status);
        }elseif ($status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45+') : ((floor(($diff) % 86400 / 60)));
        } elseif ($status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90+') : ((floor(($diff) % 86400 / 60) + 45));
        } else if ($status == 0){
            $matchTime = date('m/d H:i',$time);
        }
        else{
            $matchTime = '';
        }
        return $matchTime;
    }

    /**
     * 根据比赛id返回wap path
     * @param $mid
     * @param int $sport
     * @return string
     */
    public static function matchWapPathWithId($mid,$sport=CommonTool::kSportFootball){
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        if ($sport == CommonTool::kSportFootball){
            $path = 'https://shop.liaogou168.com/match/football/detail/'.$first.'/'.$second.'/'.$mid.'.html';
        }
        else{
            $path = 'https://shop.liaogou168.com/match/basketball/detail/'.$first.'/'.$second.'/'.$mid.'.html';
        }
        return $path;
    }

    /**
     * 根据比赛id返回path
     * @param $mid
     * @param int $sport
     * @return string
     */
    public static function matchPathWithId($mid,$sport=CommonTool::kSportFootball,$time){
        $path = '';
        if ($mid > 1000) {
            if ($sport == 2) {
                $path = '';
            } else {
                $time = date('Ymd',$time);
                $path = 'https://www.liaogou168.com/match_detail/' . $time . '/' . $mid . '.html';
            }
        }
        return $path;
    }

    public static function getStatusTextCn($status) {
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return "上半场";
            case 2:
                return "中场";
            case 3:
                return "下半场";
            case 4:
                return "加时";
            case -1:
                return "已结束";
            case -14:
                return "推迟";
            case -11:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }

    /**
     * 录像分页链接
     * @param $type
     * @param $page
     * @return string
     */
    public static function getVideoPageUrl($type, $page) {
        $name_en = self::getSubjectLeagueNameEn($type);
        $name_en = empty($name_en) ? "" : "/$name_en";
        return $name_en."/videos/".($page > 1 ?  ('index'.$page.'.html') : '');
    }

    /**
     * 获取PC录像终端链接
     * @param $slid
     * @param $id
     * @param $type  video/specimen
     * @return string
     */
    public static function getVideosDetailUrlByPc($slid, $id, $type) {
        $name_en = self::getSubjectLeagueNameEn($slid);
        $name_en = empty($name_en) ? "/videos" : "/$name_en";
        return $name_en."/$type"."$id.html";
    }

    /**
     * 录像终端静态化路径
     * @param $slid
     * @param $id
     * @return string
     */
    public static function getSubjectVideoDetailPath($slid, $id) {
        $name_en = self::getSubjectLeagueNameEn($slid);
        $name_en = empty($name_en) ? "/videos" : "/$name_en";
        $len = strlen($id);
        if ($len < 4) {
            return "";
        }
        $first = substr($id, 0, 2);
        $second = substr($id, 2, 3);
        $path = $name_en.'/video/' . $first . '/' . $second . '/' . $id . '.html';
        return $path;
    }

    /**
     * 集锦终端静态化路径
     * @param $lid
     * @param $id
     * @return string
     */
    public static function getSubjectSpecimenDetailPath($lid, $id) {
        $name_en = self::getSubjectLeagueNameEn($lid);
        $name_en = empty($name_en) ? "/specimens" : "/$name_en";
        $len = strlen($id);
        if ($len < 4) {
            return "";
        }
        $first = substr($id, 0, 2);
        $second = substr($id, 2, 3);
        $path = $name_en.'/specimen/' . $first . '/' . $second . '/' . $id . '.html';
        return $path;
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

    /**
     * 获取文章终端静态化页面路径
     * @param $name_en
     * @param $id
     * @return string
     */
    public static function getArticleDetailUrl($name_en, $id) {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (isset($sl)) {
            $path = "/".$name_en."/news".$id.".html";
        } else {
            $path = "/news/".$name_en.$id.".html";
        }
        return $path;
    }

    protected static function getSubjectLeagueNameEn($lid) {
        $name_en = "";
        if ($lid != 'all' && $lid != 999) {
            $sl = SubjectLeague::query()->find($lid);
            if (isset($sl)) {
                $name_en = $sl->name_en;
            }
        } else if ($lid == 999) {
            $name_en = "other";
        }
        return $name_en;
    }

    /**
     * 球队终端静态path
     */
    public static function getTeamDetailPath($sport, $lid, $tid) {
        $subject = SubjectLeague::getSubjectLeagueByLid($sport, $lid);
        if (isset($subject)) {
            $name_en = $subject->name_en;
        } else {
            $name_en = "other";
        }

        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        return "/$name_en/team/$sport/".substr($tempTid, 0, 2). "/". $tempTid . ".html";
    }

    /**
     * 球队终端静态path
     */
    public static function getTeamDetailPathWithType($sport, $name_en, $tid, $type, $page) {
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        return "/$name_en/team/$type/$sport/".substr($tempTid, 0, 2). "/". $tempTid."_". $page . ".html";
    }

    /**
     * 球队终端url
     */
    public static function getTeamDetailUrl($sport, $lid, $tid) {
        $subject = SubjectLeague::getSubjectLeagueByLid($sport, $lid);
        if (isset($subject)) {
            $name_en = $subject->name_en;
        } else {
            $name_en = "other";
        }
        return self::getTeamDetailUrlByNameEn($name_en, $sport, $tid);
    }

    /**
     * 根据name_en 获取球队终端路径
     * @param $name_en
     * @param $sport
     * @param $tid
     * @return string
     */
    public static function getTeamDetailUrlByNameEn($name_en, $sport, $tid) {
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        if ($name_en == 'other'){
            return "javascript:void(0)";
        }
        return "/$name_en/team$sport$tempTid".".html";
    }

    public static function getTeamDetailUrl2($sport, $s_lid, $tid) {
        $subject = SubjectLeague::find($s_lid);
        if (isset($subject)) {
            $name_en = $subject->name_en;
        } else {
            $name_en = "other";
        }
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        if ($name_en == 'other'){
            return "javascript:void(0)";
        }
        return "/$name_en/team$sport$tempTid".".html";
    }

    public static function getTeamRecordUrl($sport, $lid, $tid) {
        $subject = SubjectLeague::getSubjectLeagueByLid($sport, $lid);
        if (isset($subject)) {
            $name_en = $subject->name_en;
        } else {
            $name_en = "other";
        }
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        if ($name_en == 'other'){
            return "javascript:void(0)";
        }
        return "/$name_en/team$sport$tempTid"."_record_1.html";
    }

    public static function getTeamVideoUrl($sport, $lid, $tid) {
        $subject = SubjectLeague::getSubjectLeagueByLid($sport, $lid);
        if (isset($subject)) {
            $name_en = $subject->name_en;
        } else {
            $name_en = "other";
        }
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        if ($name_en == 'other'){
            return "javascript:void(0)";
        }
        return "/$name_en/team$sport$tempTid"."_video_1.html";
    }

    public static function getLiveDetailStaticPath($mid, $sport) {
        $tempData = self::getDetailNameDataByMid($mid, $sport);
        //只有满足，mid等于比赛两队最近的比赛才返回保存路径
        $recentMid = "";
        if (isset($tempData['hid']) && isset($tempData['aid'])
            && $tempData['hid'] > 0 && $tempData['aid'] > 0) {
            //$recentMid = self::getRecentMidByTid($mid, $sport, $tempData['hid'], $tempData['aid']);
        }
        //if ($mid != $recentMid) return "";

        $tempMid = $tempData['name'];
        $name_en = $tempData['name_en'];

        $first = substr($tempMid, 0, 2);
        $second = substr($tempMid, 2, 2);

        $path = "/".$name_en."/live/".$sport."/".$first."/".$second."/".$tempMid.".html";

        return $path;
    }

    /**
     * 获取直播终端url
     * @param $sport
     * @param $lid
     * @param $mid
     * @return string
     */
    public static function getLiveDetailUrl($sport, $lid = 0, $mid) {
        $tempData = self::getDetailNameDataByMid($mid, $sport);

        $tempMid = $tempData['name'];
        $name_en = $tempData['name_en'];

        $url = "/".$name_en."/live".$sport.$tempMid.".html";
        return $url;
    }

    public static function getDetailNameDataByMid($mid, $sport) {
        $name_en = "other";
        $len = strlen($mid);
        $tempMid = $mid;
        if ($len < 4) {
            for ($index = $len; $index < 4; $index++) {
                $tempMid = "0".$tempMid;
            }
        }

        if ($sport == MatchLive::kSportFootball) {
            $match = Match::query()->find($mid);
        } else if ($sport == MatchLive::kSportBasketball) {
            $match = BasketMatch::query()->find($mid);
        } else if ($sport == MatchLive::kSportSelfMatch) {
            $match = OtherMatch::query()->find($mid);
        }
        if (!isset($match)) {
            return ['name'=>$tempMid, 'name_en'=>$name_en];
        }

        $hid = 0;
        if (isset($match->hid)) {
            $hid = $match->hid;
        }
        $aid = 0;
        if (isset($match->aid)) {
            $aid = $match->aid;
        }

        $name = self::getMatchVsByTid($hid, $aid, $tempMid);

        //赛事专题名字
        $lid = $match->lid;
        $mls = Controller::MATCH_LEAGUE_IDS;
        if (isset($mls[$sport.'-'.$lid])) {
            $name_en = $mls[$sport . '-' . $lid]['name_en'];
        }

        return ['name'=>$name, 'name_en'=>$name_en, 'hid'=>$hid, 'aid'=>$aid];
    }

    /**
     * 根据输入的球队id，组装出比赛终端，对阵id
     */
    public static function getMatchVsByTid($hid, $aid, $tempMid = "") {
        //统一让更小的tid放在前面
        if ($hid > $aid) {
            $tempTid = $hid;
            $hid = $aid;
            $aid = $tempTid;
        }

        if ($hid == 0 && $aid == 0) {
            $name = $tempMid;
        } else {
            $len = strlen($hid);
            $tempHid = $hid;
            if ($len < 4) {
                for ($index = $len; $index < 4; $index++) {
                    $tempHid = "0".$tempHid;
                }
            }
            $name = $tempHid.$aid;
        }
        return $name;
    }

    /**
     * 根据两队id，和sport获取最近的两队对阵比赛id
     */
    private static function getRecentMidByTid($mid, $sport, $hid, $aid) {
        if ($sport == MatchLive::kSportFootball) {
            $query = Match::query();
        } else if ($sport == MatchLive::kSportBasketball) {
            $query = BasketMatch::query();
        }
        if (isset($query)) {
            $recentMatch = $query->whereIn('hid', [$hid, $aid])
                ->whereIn('aid', [$hid, $aid])
                ->where('status', '>=', 0) //比赛还未结束
                ->orderBy('time') //最近的比赛
                ->first();

            if (isset($recentMatch)) {
                return $recentMatch->id;
            }
        }
        return $mid;
    }

    /**
     * 获取录像终端静态化页面路径
     * @param $name_en
     * @param $id
     * @return string
     */
    public static function getRecordDetailPath($name_en, $id) {
        $len = strlen($id);
        if ($len < 4) {
            return "";
        }
        $first = substr($id, 0, 2);
        $second = substr($id, 2, 2);
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (isset($sl)) {
            $path = "/".$name_en."/record/".$first."/".$second."/".$id.".html";
        } else {
            $path = "/record/".$name_en."/".$first."/".$second."/".$id.".html";
        }
        return $path;
    }

    /**
     * 获取录像终端静态化页面路径
     * @param $name_en
     * @param $id
     * @return string
     */
    public static function getRecordDetailUrl($name_en, $id) {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (isset($sl)) {
            $path = "/".$name_en."/record".$id.".html";
        } else {
            $path = "/record/".$name_en.$id.".html";
        }
        return $path;
    }

    public static function getComboData($name_en = 'all'){
        if (!Storage::disk("public")->exists("/static/json/pc/comboData/". $name_en . '.json')){
            $jsonStr = null;
        }
        else{
            $jsonStr = Storage::disk("public")->get("/static/json/pc/comboData/". $name_en . '.json');
        }
        if (is_null($jsonStr)){
            return HomeController::updateFileComboData($name_en);
        }
        $json = json_decode($jsonStr,true);
        if (is_null($json)){
            return HomeController::updateFileComboData($name_en);
        }
        return $json;
    }


    public static function getPlayerData($sport, $lid, $season, $kind) {
        $url = "/static/technical/".$sport."/" . $lid . "/player/" . $season . '_' . $kind . ".json";
        $url = "http://match.liaogou168.com" . $url;
        $playerTech = Controller::execUrl($url, 5);
        return $playerTech;
    }

}