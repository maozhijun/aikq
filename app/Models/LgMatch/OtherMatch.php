<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/23
 * Time: 12:18
 */

namespace App\Models\LgMatch;


use Illuminate\Database\Eloquent\Model;

/**
 * 自建赛事
 * Class OtherMatch
 * @package App\Models\CMS
 */
class OtherMatch extends Model
{
    protected $connection = "match";
    const kTypeMenu = 1, kTypeMatch = 2;
    const kTypeArray = [self::kTypeMenu=>'节目', self::kTypeMatch=>'比赛'];

    public function live() {
        return $this->hasOne(MatchLive::class, 'match_id', 'id');
    }


    public function typeCn() {
        $type = $this->type;
        $cn = "";
        switch ($type) {
            case 1:
                $cn = '节目';
                break;
            case 2:
                $cn = "比赛";
                break;
        }
        return $cn;
    }

    /**
     * 计算出状态
     * @return int
     */
    public function getStatus() {
        $time = strtotime($this->time);
        $end_time = strtotime($this->end_time);
        $now = time();
        if ($time > $now) {//未开始
            $status = 0;
        } else {
            $status = 1;//比赛中
        }
        if ($end_time + 30 * 60 < $now) {
            $status = -1;//已结束
        }
        return $status;
    }

    /**
     * @return mixed|string
     */
    public function getLeagueName() {
        $type = $this->type;
        if ($type == self::kTypeMatch) {
            return $this->lname;
        }
        return $this->typeCn();
    }

    /**
     * 获取项目
     * @return mixed|string
     */
    public function projectCn() {
        $project = $this->project;
        if (empty($project)) {
            $project = $this->typeCn();
        }
        return $project;
    }

}