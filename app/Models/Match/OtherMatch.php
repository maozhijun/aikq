<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/23
 * Time: 12:18
 */

namespace App\Models\Match;


use Illuminate\Database\Eloquent\Model;

/**
 * 自建赛事
 * Class OtherMatch
 * @package App\Models\CMS
 */
class OtherMatch extends Model
{
//    protected $connection = "match";
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


    public static function copyLgOtherMatch(\App\Models\LgMatch\OtherMatch $other) {
        $other_id = $other->id;
        $newOther = self::query()->find($other_id);
        if (!isset($newOther)) {
            $newOther = new OtherMatch();
            $newOther->id = $other_id;
        }
        $newOther->lid = $other->lid;
        $newOther->type = $other->type;
        $newOther->lname = $other->lname;
        $newOther->hname = $other->hname;
        $newOther->aname = $other->aname;
        $newOther->hicon = $other->hicon;
        $newOther->aicon = $other->aicon;
        $newOther->hscore = $other->hscore;
        $newOther->ascore = $other->ascore;
        $newOther->time = $other->time;
        $newOther->end_time = $other->end_time;
        $newOther->status = $other->status;
        $newOther->ad_id = $other->ad_id;
        $newOther->up_id = $other->up_id;
        $newOther->project = $other->project;
        $newOther->sport = $other->sport;
        $newOther->created_at = $other->created_at;
        $newOther->updated_at = $other->updated_at;
        try {
            $newOther->save();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

}