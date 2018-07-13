<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/8
 * Time: 15:19
 */

namespace App\Models\Subject;


use Illuminate\Database\Eloquent\Model;

/**
 * 录像链接
 * Class SubjectVideoLink
 * @package App\Models\CMS\Subject
 */
class SubjectVideoChannels extends Model
{
//    protected $connection = "match";

    public function video() {
        return $this->hasOne(SubjectVideo::class, 'id', 'sv_id');
    }

    /**
     * 获取线路总数
     * @param $slid
     * @return int
     */
    public static function channelsCount($slid) {
        $query = self::query();
        $query->join('subject_videos', 'subject_video_channels.sv_id', '=', 'subject_videos.id');
        if ($slid != 'all') {
            $query->where('subject_videos.s_lid', $slid);
        }
        return $query->count();
    }

}