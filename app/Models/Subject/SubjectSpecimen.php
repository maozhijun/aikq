<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/3
 * Time: 17:34
 */

namespace App\Models\Subject;


use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Database\Eloquent\Model;

/**
 * 录像集锦
 * Class SubjectVideoSpecimen
 * @package App\Models\CMS\Subject
 */
class SubjectSpecimen extends Model
{
//    protected $connection = "match";
    const kShow = 1, kHide = 2;// show 字段，1：显示，2：隐藏

    public function subjectLeague() {
        return $this->hasOne(SubjectLeague::class, 'id', 's_lid');
    }

    public function playerCn() {
        $player = $this->player;
        $playerArray = MatchLiveChannel::kPlayerArrayCn;
        if (isset($playerArray[$player])) {
            return $playerArray[$player];
        }
        return "";
    }

    public function showCn() {
        $show = $this->show;
        if ($show == self::kShow) {
            return "显示";
        }
        return "隐藏";
    }

    /**
     * 获取专题最新的集锦
     * @param $slid
     * @param bool $isMobile
     * @param int $count
     * @return array
     */
    public static function getNewSpecimens($slid, $isMobile = false, $count = 30) {
        $query = self::query();
        $query->where('s_lid', $slid);
        $query->where('show', self::kShow);
        $platform = $isMobile ? MatchLive::kPlatformPhone : MatchLive::kPlatformPc;
        $query->where(function ($orQuery) use ($platform) {
            $orQuery->where('platform', MatchLive::kPlatformAll);
            $orQuery->orWhere('platform', $platform);
        });
        $query->orderByDesc('time')->orderByRaw('ifNull(od, 999)');
        $specimens = $query->take($count)->get();
        $array = [];
        foreach ($specimens as $specimen) {
            $time = strtotime($specimen->time);
            $day = strtotime(date('Ymd', $time));
            $obj = ['id'=>$specimen->id, 'cover'=>$specimen->cover, 'title'=>$specimen->title, 'time'=>$specimen->time, 'player'=>$specimen->player];
            $array[$day][] = $obj;
        }
        return $array;
    }

}