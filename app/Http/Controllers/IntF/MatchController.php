<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/25
 * Time: 14:35
 */

namespace App\Http\Controllers\IntF;




use App\Models\Match\MatchLive;
use Illuminate\Routing\Controller;

class MatchController extends Controller
{

    public static function tech($sport, $mid) {
        $url = self::getJsonUrl($sport, $mid, 'tech');
        $jsonStr = \App\Http\Controllers\Controller::execUrl($url, 2, false);
        return json_decode($jsonStr, $jsonStr);
    }


    public static function footballLineup($mid) {
        $sport = MatchLive::kSportFootball;
        $url = self::getJsonUrl($sport, $mid, 'lineup');
        $jsonStr = \App\Http\Controllers\Controller::execUrl($url, 2, false);
        $json = json_decode($jsonStr, true);
        $array = [];
        if (isset($json)) {
            if (isset($json['home'])) {
                $home = [];
                if (isset($json['home']['first'])) {
                    $first = $json['home']['first'];
                    foreach ($first as $p) {
                        $home[] = ['num'=>$p['num'], 'name'=>$p['name'], 'first'=>1];
                    }
                }
                if (isset($json['home']['back'])) {
                    $back = $json['home']['back'];
                    foreach ($back as $p) {
                        $home[] = ['num'=>$p['num'], 'name'=>$p['name'], 'first'=>0];
                    }
                }
                $array['home'] = $home;
            }

            if (isset($json['away'])) {
                $away = [];
                if (isset($json['away']['first'])) {
                    $first = $json['away']['first'];
                    foreach ($first as $p) {
                        $away[] = ['num'=>$p['num'], 'name'=>$p['name'], 'first'=>1];
                    }
                }
                if (isset($json['away']['back'])) {
                    $back = $json['away']['back'];
                    foreach ($back as $p) {
                        $away[] = ['num'=>$p['num'], 'name'=>$p['name'], 'first'=>0];
                    }
                }
                $array['away'] = $away;
            }
        }
        return $array;
    }

    public static function basketballLineup($mid) {
        $sport = MatchLive::kSportBasketball;
        $url = self::getJsonUrl($sport, $mid, 'player');
        $jsonStr = \App\Http\Controllers\Controller::execUrl($url, 2, false);
        $json = json_decode($jsonStr, $jsonStr);
        $array = [];
        if (isset($json)) {
            if (isset($json['home'])) {
                $players = $json['home'];
                $home = [];
                foreach ($players as $p) {
                    $type = $p['type'];
                    if ($type != "player" || count($home) >= 5) continue;
                    $home[] = ['num'=>'-', 'first'=>1, 'name'=>$p['name']];
                }
                $array['home'] = $home;
            }
            if (isset($json['away'])) {
                $players = $json['away'];
                $away = [];
                foreach ($players as $p) {
                    $type = $p['type'];
                    if ($type != "player" || count($away) >= 5) continue;
                    $away[] = ['num'=>'-', 'first'=>1, 'name'=>$p['name']];
                }
                $array['away'] = $away;
            }
        }
        return $array;
    }

    protected static function getJsonUrl($sport, $mid, $name) {
        $prefix = self::getMatchUrl();
        $first = substr($mid, 0, 2);
        $second = substr($mid, 2, 2);
        $url = "/static/terminal/$sport/$first/$second/$mid/$name.json";
        return $prefix . $url;
    }

    protected static function getMatchUrl() {
        return env('MATCH_URL', 'http://match.liaogou168.com');
    }

}