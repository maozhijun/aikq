<?php
namespace App\Http\Controllers\IntF\Common;

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2019/3/8
 * Time: 12:27
 */
class LeagueDataTool
{
    public static function getLeagueDataBySeason($sport, $lid, $season = "") {
        if (strlen($season) > 0) {
            $url = env('MATCH_URL')."/static/league/$sport/$lid/$season/detail.json";
        } else {
            $url = env('MATCH_URL')."/static/league/$sport/$lid.json";
        }
        return self::curlData($url, 20);
    }

    /**
     * 新服务器获取数据
     * @param $sport
     * @param $lid
     * @param string $season
     * @return mixed
     */
    public static function getLeagueDataBySeasonNew($sport, $lid, $season = "") {
        if (strlen($season) > 0) {
            $url = env("MATCH_URL"). "/static/league/$sport/$lid/$season/detail.json";
        } else {
            $url = env("MATCH_URL") . "/static/league/$sport/$lid.json";
        }
        return self::curlData($url, 5);
    }

    /**
     * 请求match接口
     * @param $url
     * @param $timeout
     * @return mixed
     */
    private static function curlData($url,$timeout = 8){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }
}