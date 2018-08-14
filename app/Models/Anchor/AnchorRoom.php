<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/11
 * Time: 下午6:46
 */
namespace App\Models\Anchor;


use App\Models\LgMatch\BasketMatch;
use App\Models\Match\Match;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnchorRoom extends Model
{
    protected $connection = 'akq';
    const kStatusValid = 1, kStatusInvalid = 0;//有效
    const kLiveStatusLiving = 1, kLiveClose = 2, kLiveStatusOffline = 0;

    static public function getLivingRooms()
    {
        $rooms = self::where('live_status', self::kLiveStatusLiving)->get();
        return $rooms;
    }

    public function anchor()
    {
        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
    }

    /**
     * 有效的主播房间
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function validRooms()
    {
        $query = AnchorRoom::query();
        $query->join('anchors', 'anchors.id', '=', 'anchor_rooms.anchor_id');
        $query->where('anchors.status', Anchor::kStatusValid);
        $query->select("anchor_rooms.*");
        return $query->get();
    }

    /**
     * 正在直播什么比赛tag
     * @return Model|null|static
     */
    public function getLivingTag()
    {
//        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
        $tags = AnchorRoomTag::where('room_id', $this->id)
            ->where('match_time', '>', date_create('-4 hours'))
            ->where('valid', '=', AnchorRoomTag::KValid)
            ->orderby('match_time', 'asc')
            ->get();
        $match = null;
        foreach ($tags as $tag) {
            $tmp = $tag->getMatch();
            if ($tmp['status'] >= 0) {
                $tmp['sport'] = $tag['sport'];
                $tmp['tag'] = $tag;
                $match = $tmp;
                break;
            }
        }
        return $match;
    }

    /**
     * 正在直播什么比赛
     * @return Model|null|static
     */
    public function getLivingMatch()
    {
//        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
        $tags = AnchorRoomTag::where('room_id', $this->id)
            ->where('match_time', '>', date_create('-4 hours'))
            ->where('valid', '=', AnchorRoomTag::KValid)
            ->orderBy('match_time', 'asc')
            ->get();
        $match = null;
        foreach ($tags as $tag) {
            $tmp = $tag->getMatch();
            if ($tmp['status'] > 0) {
                $tmp['sport'] = $tag['sport'];
                $match = $tmp;
                break;
            }
        }
        return $match;
    }

    public function getCurrentMatch()
    {
//        return $this->hasOne('App\Models\Anchor\Anchor', 'id', 'anchor_id');
        $tags = AnchorRoomTag::where('room_id', $this->id)
            ->where('match_time', '>', date_create('-4 hours'))
            ->where('valid', '=', AnchorRoomTag::KValid)
            ->orderBy('match_time', 'asc')
            ->get();
        $match = null;
        foreach ($tags as $tag) {
            $tmp = $tag->getMatch();
            if ($tmp['status'] >= 0) {
                $tmp['sport'] = $tag['sport'];
                $match = $tmp;
                break;
            }
        }
        return $match;
    }

    /**
     * 正在直播什么比赛(AKQ的表,一般不要求太实时)
     * @return Model|null|static
     */
    public function getLivingMatchAKQ()
    {
        $tags = AnchorRoomTag::where('room_id', $this->id)
            ->where('match_time', '>', date_create('-4 hours'))
            ->where('valid', '=', AnchorRoomTag::KValid)
            ->get();
        $mids = array();
        foreach ($tags as $tag) {
            $mids[] = $tag['match_id'];
        }
        $match = Match::whereIn('id', $mids)
            ->where('status', '>', 0)
            ->orderBy('time', 'asc')
            ->first();
        return $match;
    }

    /**
     * 预约的比赛
     * @return \Illuminate\Support\Collection
     */
    public function getTagMatch()
    {
        $tags = AnchorRoomTag::where('room_id', $this->id)
            ->where('match_time', '>', date_create('-4 hours'))
            ->where('valid', '=', AnchorRoomTag::KValid)
            ->get();
        $f_mids = array();
        $b_mids = array();
        foreach ($tags as $tag) {
            if ($tag['sport'] == 1)
                $f_mids[] = $tag['match_id'];
            else if ($tag['sport'] == 2)
                $b_mids[] = $tag['match_id'];
        }
        $f_matches = Match::whereIn('id', $f_mids)
            ->where('status', '>=', 0)
            ->orderBy('time', 'asc')
            ->get();
        $b_matches = BasketMatch::whereIn('id', $b_mids)
            ->where('status', '>=', 0)
            ->orderBy('time', 'asc')
            ->get();
        $matches = array_merge($f_matches->toArray(), $b_matches->toArray());
        return $matches;
    }

    public function appModel($withMatch = false)
    {
        $tmp = array();
        $tmp['id'] = $this->id;
        $tmp['title'] = $this->title;
        $tmp['cover'] = isset($this->live_cover) ? (env('APP_URL') . $this->live_cover) : $this->cover;
        //主播
        $anchor = $this->anchor;
        $tmp['anchor']['name'] = $anchor->name;
        $tmp['anchor']['id'] = $anchor->id;
        $tmp['anchor']['icon'] = $anchor->icon;
        //比赛
        if ($withMatch) {
            $tmp['match'] = $this->getLivingMatch();
        } else {
            $tmp['match'] = null;
        }
        return $tmp;
    }
}