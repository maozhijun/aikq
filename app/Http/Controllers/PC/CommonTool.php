<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/5
 * Time: 上午11:06
 */

namespace App\Http\Controllers\PC;

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

}