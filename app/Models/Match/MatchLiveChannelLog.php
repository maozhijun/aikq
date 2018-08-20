<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25
 * Time: 17:23
 */

namespace App\Models\Match;


use App\Models\Admin\Account;
use Illuminate\Database\Eloquent\Model;

/**
 * 线路日志表
 * Class MatchLiveChannelLog
 * @package App\Models\Match
 */
class MatchLiveChannelLog extends Model
{
    const kStatusNew = 1, kStatusEdit = 2;

    /**
     * 保存日志记录
     * @param array $old              保存在redis的字符转化的数组
     * @param MatchLiveChannel $new   MatchLiveChannel对象
     * @return bool
     */
    public static function saveLog(array $old, MatchLiveChannel $new) {
        if (!isset($new)) {
            return false;
        }

        $log = new MatchLiveChannelLog();
        $log->ch_id = $new->id;

        //获取比赛信息 开始
        $match_live_id = $new->live_id;
        $matchLive = MatchLive::query()->find($match_live_id);
        $match = $matchLive->getMatch();

        $sport = $matchLive->sport;
        $mid = $matchLive->match_id;
        $lname = $match->getLeagueName();
        $hname = $match->hname;
        $aname = $match->aname;
        $time = $match->time;
        //获取比赛信息 结束

        $log->status = count($old) == 0 ? self::kStatusNew : self::kStatusEdit;//无旧记录则为新建线路
        if (count($old) > 0) {//无旧记录则为新建线路
            $log->old_name = $old['name'];
            $log->old_content = $old['content'];
            $log->old_show = $old['show'];
            $log->old_platform = $old['platform'];
            $log->old_isPrivate = $old['isPrivate'];
            $log->old_od = $old['od'];
            $log->old_admin_id = $old['admin_id'];
        }
        $log->new_name = $new['name'];
        $log->new_content = $new['content'];
        $log->new_show = $new['show'];
        $log->new_platform = $new['platform'];
        $log->new_isPrivate = $new['isPrivate'];
        $log->new_od = $new['od'];
        $log->new_admin_id = $new['admin_id'];


        $log->sport = $sport;
        $log->mid = $mid;
        $log->lname = $lname;
        $log->hname = $hname;
        $log->aname = $aname;
        $log->time = $time;
        $log->save();
        return true;
    }

    public function oldAdmin() {
        return $this->hasOne(Account::class, 'id', 'old_admin_id');
    }

    public function newAdmin() {
        return $this->hasOne(Account::class, 'id', 'new_admin_id');
    }

    public function oldAdminName() {
        $admin = $this->oldAdmin;
        if (!isset($admin)) {
            return "";
        }
        return $admin->name;
    }

    public function newAdminName() {
        $admin = $this->newAdmin;
        if (!isset($admin)) {
            return "";
        }
        return $admin->name;
    }

    public function getStatusCn() {
        if ($this->status == self::kStatusNew) {
            return "新增线路";
        }
        return "修改线路";
    }

    public function getOldShow() {
        $show = $this->old_show;
        if ($show == MatchLiveChannel::kShow) {
            return "显示";
        }
        return "隐藏";
    }

    public function getNewShow() {
        $show = $this->new_show;
        if ($show == MatchLiveChannel::kShow) {
            return "显示";
        }
        return "隐藏";
    }

    public function getOldPrivate() {
        if ($this->old_isPrivate == MatchLiveChannel::kPrivate) {
            return "有版权";
        }
        return "无版权";
    }

    public function getNewPrivate() {
        if ($this->new_isPrivate == MatchLiveChannel::kPrivate) {
            return "有版权";
        }
        return "无版权";
    }

    public function getOldPlatform() {
        $platform = $this->old_platform;
        if ($platform == MatchLiveChannel::kPlatformAll) {
            return "全部";
        } else if ($platform == MatchLiveChannel::kPlatformPC) {
            return "电脑";
        }
        return "手机";
    }

    public function getNewPlatform() {
        $platform = $this->new_platform;
        if ($platform == MatchLiveChannel::kPlatformAll) {
            return "全部";
        } else if ($platform == MatchLiveChannel::kPlatformPC) {
            return "电脑";
        }
        return "手机";
    }

}