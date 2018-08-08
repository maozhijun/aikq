<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/8
 * Time: 16:40
 */

namespace App\Models\Match;


use Illuminate\Database\Eloquent\Model;

class BasketTeam extends Model
{
//    protected $connection = 'match';

    public static function getIcon($icon) {
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return env('CDN_URL') . '/img/icon_team_default.png';
            }
            return 'http://nba.win007.com'.$icon;
        } else {
            return env('CDN_URL') . '/img/icon_team_default.png';
        }
    }

    public static function getIconByTid($tid) {
        $team = BasketTeam::query()->find($tid);
        if (isset($team)) {
            $icon = $team->icon;
        }
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return "";
            }
            return 'http://nba.win007.com'.$icon;
        } else {
            return "";
        }
    }
}