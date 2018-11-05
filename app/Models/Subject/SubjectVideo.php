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

    public function getAllChannels() {
        $query = SubjectVideoChannels::query();
        $query->where('sv_id', $this->id);
        $query->orderBy('type');
        $query->orderBy('od');
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
        $obj['hid'] = $video->hid;
        $obj['aid'] = $video->aid;
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

    public static function relationVideos($hname, $aname, $count = 10) {
        $query = self::query();
        $query->join('subject_video_channels', 'subject_video_channels.sv_id', '=', 'subject_videos.id');
        if (!empty($hname) && !empty($aname)) {
            $query->where(function ($orQuery) use ($hname, $aname) {
                $orQuery->whereIn('subject_videos.hname', [$hname, $aname]);
                $orQuery->orWhereIn('subject_videos.aname', [$hname, $aname]);
            });
        } else {
            $name = empty($hname) ? $aname : $hname;
            if (!empty($name)) {
                $query->where(function ($orQuery) use ($name) {
                    $orQuery->where('subject_videos.hname', $name);
                    $orQuery->orWhere('subject_videos.aname', $name);
                });
            }
        }

        $query->take($count);
        $query->orderByDesc('subject_videos.time')->orderBy('subject_video_channels.od');
        $query->orderByDesc('subject_video_channels.id');
        $query->select("subject_video_channels.*", "subject_videos.hname", 'subject_videos.aname', 'subject_videos.s_lid');
        $query->addSelect("subject_videos.season", "subject_videos.round", "subject_videos.group", "subject_videos.stage_cn", "subject_videos.lname");
        return $query->get();
    }

    public static function relationVideosByTid($hid, $aid, $sport = MatchLive::kSportFootball, $count = 10) {
        $query = self::query();
        $query->join('subject_video_channels', 'subject_video_channels.sv_id', '=', 'subject_videos.id');
        if (is_numeric($sport)) {
            $query->where('subject_videos.sport', $sport);
        }
        if (is_numeric($hid) && is_numeric($aid)) {
            $query->where(function ($orQuery) use ($hid, $aid) {
                $orQuery->whereIn('subject_videos.hid', [$hid, $aid]);
                $orQuery->orWhereIn('subject_videos.aid', [$hid, $hid]);
            });
        } else {
            $tid = is_numeric($hid) ? $hid : $aid;
            if (is_numeric($tid)) {
                $query->where(function ($orQuery) use ($tid) {
                    $orQuery->where('subject_videos.hid', $tid);
                    $orQuery->orWhere('subject_videos.aid', $tid);
                });
            }
        }

        $query->take($count);
        $query->orderByDesc('subject_videos.time')->orderBy('subject_video_channels.od');
        $query->orderByDesc('subject_video_channels.id');
        $query->select("subject_video_channels.*", "subject_videos.hname", 'subject_videos.aname', 'subject_videos.s_lid');
        $query->addSelect("subject_videos.season", "subject_videos.round", "subject_videos.group", "subject_videos.stage_cn", "subject_videos.lname");
        return $query->get();
    }

    public static function moreVideos($curChannelId = null, $count = 12) {
        $query = self::query();
        $query->join('subject_video_channels', 'subject_video_channels.sv_id', '=', 'subject_videos.id');
        if (is_numeric($curChannelId)) {
            $query->where('subject_video_channels.id', '<>', $curChannelId);
        }
        $query->orderByDesc('subject_videos.time')->orderBy('subject_video_channels.od');
        $query->orderByDesc('subject_video_channels.id');
        $query->select("subject_video_channels.*", "subject_videos.hname", 'subject_videos.aname', 'subject_videos.s_lid');
        $query->addSelect("subject_videos.season", "subject_videos.round", "subject_videos.group", "subject_videos.stage_cn", "subject_videos.lname");
        $query->take($count);
        return $query->get();
    }

    public static function firstVideo($mid) {
        $query = self::query();
        $query->join('subject_video_channels', 'subject_video_channels.sv_id', '=', 'subject_videos.id');
        $query->where('mid', $mid);
        $query->select("subject_video_channels.*", "subject_videos.hname", 'subject_videos.aname', 'subject_videos.s_lid');
        $query->orderBy('subject_video_channels.od');
        return $query->first();
    }

    public function getMatchInfo($showTime = false) {
        $lname = $this->lname;
        $hname = $this->hname;
        $aname = $this->aname;

        $season = $this->season;
        $round = $this->round;

        $stage_cn = $this->stage_cn;
        $group = $this->group;
        $time = substr($this->time, 0, 11);
        if (!empty($stage_cn)) {
            return $season . "" . $lname . "" . $stage_cn . $group . " " .$hname." VS ".$aname . ($showTime ? (' ' . $time ) : '' );
        } else {
            return $season . "" . $lname . "" .$round . "轮 " . $hname." VS ".$aname . ($showTime ? (' ' . $time ) : '' );
        }
    }

    public function getVideoTitle($type = '', $showTime = false) {
        $type = empty($type) ? $this->type : $type;
        return ($type == 1 ? $this->getMatchInfo($showTime) . ' ' : '') . $this->title;
    }

}