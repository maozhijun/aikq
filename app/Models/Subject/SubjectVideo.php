<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/8
 * Time: 15:18
 */

namespace App\Models\Subject;


use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use Illuminate\Database\Eloquent\Model;

/**
 * 录像
 * Class SubjectVideo
 * @package App\Models\CMS\Subject
 */
class SubjectVideo extends Model
{
    const kOther = 999;
//    protected $connection = "match";

    /**
     * 获取所有录像线路
     * @param $platForm
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getChannels($platForm = MatchLive::kPlatformAll) {
        $query = $this->hasMany(SubjectVideoChannels::class, 'sv_id', 'id');
        if (!in_array($platForm, [MatchLive::kPlatformPc, MatchLive::kPlatformPhone])) {
            $platForm = null;
        }
        if (isset($platForm)) {
            $query->where(function ($orQuery) use ($platForm) {
                $orQuery->where('platform', MatchLive::kPlatformAll);
                $orQuery->orWhere('platform', $platForm);
            });
        }
        $query->orderByRaw('ifNull(od, 999)');
        return $query->get();
    }

    /**
     * 获取最新录像
     * @param $slid
     * @param $isMobile
     * @param int $count
     * @return array
     */
    public static function newVideos($slid, $isMobile = false, $count = 20) {
        $query = self::query();
        $query->where('s_lid', $slid);
        $query->orderByDesc('time');
        $videos = $query->take($count)->get();
        $array = [];
        foreach ($videos as $video) {
            $time = strtotime($video->time);
            $day = strtotime(date('Ymd', $time));
            $obj = self::video2Array($video, $isMobile);
            if (!isset($obj) || (!isset($obj['channels']) || count($obj['channels']) == 0) ) continue;
            $array[$day][] = $obj;
        }
        return $array;
    }

    /**
     * 录像实体转为数组
     * @param SubjectVideo $video
     * @param $isMobile
     * @return array|null
     */
    public static function video2Array(SubjectVideo $video, $isMobile = false) {
        $time = strtotime($video->time);
        $platform = $isMobile ? MatchLive::kPlatformPhone : MatchLive::kPlatformPc;
        $channels = $video->getChannels($platform);
        if (count($channels) == 0) {
            return null;
        }
        $lname = $video->lname;
        if (empty($lname)) {
            $sl = SubjectLeague::query()->find($video->s_lid);
            if (isset($sl)) {
                $lname = $sl->name;
            }
        }

        $obj = ['id'=>$video->id, 'mid'=>$video->mid, 'lname'=>$lname,  'hname'=>$video->hname, 'aname'=>$video->aname, 'hscore'=>$video->hscore, 'round'=>$video->round,
                    'ascore'=>$video->ascore, 'time'=>$time, 'season'=>$video->season, 'group'=>$video->group, 'sport'=>$video->sport, 's_lid'=>$video->s_lid];

        $match = Match::query()->find($video->mid);
        if (isset($match)) {
            $hid = $match->hid;
            $aid = $match->aid;
            $obj['lid'] = $match->lid;
            $obj['hicon'] = Match::getTeamIconCn($hid);
            $obj['aicon'] = Match::getTeamIconCn($aid);
        }

        $channel_array = [];
        foreach ($channels as $channel) {
            $ch_array = ['id'=>$channel->id, 'title'=>$channel->title, 'cover'=>$channel->cover, 'player'=>$channel->player, 'link'=>$channel->content];
            $ch_array['platform'] = $channel->platform;
            $ch_array['playurl'] = $channel->content;
            $channel_array[] = $ch_array;
        }
        $obj['channels'] = $channel_array;
        return $obj;
    }

}