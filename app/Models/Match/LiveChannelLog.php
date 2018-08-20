<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/10
 * Time: 16:44
 */

namespace App\Models\Match;


use Illuminate\Database\Eloquent\Model;

class LiveChannelLog extends Model
{
    const kLiveStatusValid = 1, kLiveStatusInvalid = 0;
    //爱看球直播比赛线路日志记录

    public function getStatusCn() {
        $live_status = $this->live_status;
        if ($live_status == self::kLiveStatusValid) {
            return "推流恢复";
        }
        return "推流中断";
    }

    public function getPlatformCn() {
        $platform = $this->platform;
        if ($platform == MatchLiveChannel::kPlatformAll) {
            return "全部终端";
        } else if ($platform == MatchLiveChannel::kPlatformPC) {
            return "电脑端";
        }
        return "手机端";
    }

}