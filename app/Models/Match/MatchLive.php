<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/20
 * Time: 12:52
 */

namespace App\Models\Match;


use App\Http\Controllers\PC\CommonTool;
use Illuminate\Database\Eloquent\Model;

class MatchLive extends Model
{
//    protected $connection = 'match';
    const kSportFootball = 1, kSportBasketball = 2, kSportSelfMatch = 3;//1：足球，2：篮球，3：自建赛事

    const kIsPrivate = 2;
    const kShow = 1, kHide = 2;
    const kPlatformAll = 1, kPlatformPc = 2, kPlatformPhone = 3;
    const FootballPrivateArray = [8, 11, 26, 29, 31, 46, 73, 77, 139];//德甲，法甲，西甲，意甲，英超，中超，欧冠，欧罗巴，亚冠。
    const BasketballPrivateArray = [1, 2, 3, 4, 5];//NBA, WNBA, NBA明星赛, CBA, CBA明星赛

    public function football() {
        return $this->hasOne(Match::class, 'id', 'match_id');
    }

    public function basketball() {
        return $this->hasOne(BasketMatch::class, 'id', 'match_id');
    }

    public static function liveChannels($liveId) {
        return MatchLiveChannel::query()->where('live_id', $liveId)->orderBy('od')->get();
    }

    public function hasWapChannel() {
        $query = MatchLiveChannel::query()->where('live_id', $this->id)->where('show', MatchLiveChannel::kShow);
        $query->where(function ($orQ) {
            $orQ->where('platform', MatchLiveChannel::kPlatformAll);
            $orQ->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        return $query->count() > 0;
    }

    /**
     * 是否有直播线路
     * @param int $use      使用的网站
     * @param int $platform 平台（PC、WAP）默认获取PC的线路
     * @return bool
     */
    public function hasChannel($use = 0, $platform = MatchLiveChannel::kPlatformPC) {
        $query = MatchLiveChannel::query()->where('live_id', $this->id)->where('show', MatchLiveChannel::kShow);
        $query->where(function ($orQ) use ($platform) {
            $orQ->where('platform', MatchLiveChannel::kPlatformAll);
            $orQ->orWhere('platform', $platform);
        });
        if ($use != 0) {
            $query->where(function ($orQ) use ($use) {
                $orQ->where('use', MatchLiveChannel::kUseAll);
                $orQ->orWhere('use', $use);
            });
        }
        return $query->count() > 0;
    }

    public function getMatch($id = null) {
        if (!isset($id)) $id = $this->match_id;
        if ($this->sport == self::kSportFootball) {
            return Match::query()->find($id);
        } else if ($this->sport == self::kSportBasketball) {
            return BasketMatch::query()->find($id);
        } else if ($this->sport == self::kSportSelfMatch) {
            return OtherMatch::query()->find($id);
        }
    }

    public function getLeagueName($id) {
        $match = $this->getMatch($id);
        if (isset($match)) {
            return $match->getLeagueName();
        }
        return "";
    }

    public function getStatusText($id) {
        $match = $this->getMatch($id);
        if (isset($match)) {
            return $match->getStatusText();
        }
        return "";
    }

    /**
     * 视频线路总数量
     */
    public function channelCount() {
        return MatchLiveChannel::query()->where('live_id', $this->id)->count();
    }

    public function channels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 看球吗用channel,因为liaogou现在不支持tt直播,这个临时
     * @return array
     */
    public function kChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use', MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
            $orQuery->orWhere('use', MatchLiveChannel::kUseLg310);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        $lid = $this->league_id;
        if (!isset($lid)) {
            $match = $this->getMatch();
            $lid = $match->lid;
        }
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $tmp['impt'] = $this->impt;
            if ($channel->use == MatchLiveChannel::kUseAiKQ) {
                $tmp['live_url'] = CommonTool::getLiveDetailUrl($this->sport, $lid, $this->match_id);
            }
            $array[] = $tmp;
        }
        return $array;
    }

    public function dbChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use', MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
            $orQuery->orWhere('use', MatchLiveChannel::kUseLg310);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $tmp['impt'] = $this->impt;
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 手机端的直播线路
     * @return array
     */
    public function mChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
            $orQuery->orWhere('use',MatchLiveChannel::kUseLg310);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifNull(od, 99) as nod');
        $query->orderBy('nod');
        $channels = $query->limit(6)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        $lid = $this->league_id;
        if (!isset($lid)) {
            $match = $this->getMatch();
            $lid = $match->lid;
        }

        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            if ($channel->use == MatchLiveChannel::kUseAiKQ) {
                $tmp['live_url'] = CommonTool::getLiveDetailUrl($this->sport, $lid, $this->match_id);
            }
            $array[] = $tmp;
        }
        return $array;
    }


    /**
     * 看球吗用channel,因为liaogou现在不支持tt直播,这个临时
     * @return array
     */
    public function kAiKqChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
//        $query->where(function ($orQuery) {
//            $orQuery->where('use',MatchLiveChannel::kUseAll);
//            $orQuery->orWhere('use',MatchLiveChannel::kUseAiKQ);
//        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->where(function ($orQuery) {
            $orQuery->where('isPrivate', MatchLiveChannel::kIsPrivate);
//            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
        });
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        $match = $this->getMatch();
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $tmp['impt'] = $this->impt;
            $tmp['akq_url'] = $channel->akq_url;
            if ($channel->use == MatchLiveChannel::kUseAiKQ) {
                $tmp['live_url'] = CommonTool::getLiveDetailUrl($this->sport, $match->lid, $this->match_id);
            }
            $array[] = $tmp;
            if(isset($channel->akq_url) && strlen($channel->akq_url) > 0){
                $params = explode('/',$channel->akq_url);
                $params = $params[count($params) - 1];
                $params = explode('.',$params);
                $params = $params[0];
                $params = str_replace('room','',$params);
                $tmp['room_id'] = $params;
            }
        }
        return $array;
    }

    /**
     * 手机端的直播线路
     * @return array
     */
    public function mAiKqChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
//        $query->where(function ($orQuery) {
//            $orQuery->where('use',MatchLiveChannel::kUseAll);
//            $orQuery->orWhere('use',MatchLiveChannel::kUseAiKQ);
//        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->where(function ($orQuery) {
            $orQuery->where('isPrivate', MatchLiveChannel::kIsPrivate);
//            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
        });
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(6)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $tmp['impt'] = $this->impt;
            $tmp['akq_url'] = $this->akq_url;
            $array[] = $tmp;
            if(isset($this->akq_url) && strlen($this->akq_url) > 0){
                $params = explode('/',$this->akq_url);
                $params = $params[count($params) - 1];
                $params = explode('.',$params);
                $params = $params[0];
                $params = str_replace('room','',$params);
                $tmp['room_id'] = $params;
            }
        }
        return $array;
    }

    /**
     * 看球吗用channel,因为liaogou现在不支持tt直播,这个临时
     * @return array
     */
    public function kHeiTuChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use',MatchLiveChannel::kUseHeiTu);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 手机端的直播线路
     * @return array
     */
    public function mHeiTuChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use',MatchLiveChannel::kUseHeiTu);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(6)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $array[] = $tmp;
        }
        return $array;
    }

    public static function isLive($mid, $sport = MatchLive::kSportFootball) {
        $count = MatchLive::query()->where('sport', $sport)
            ->where('match_id', $mid)->get()->count();
        return $count > 0;
    }

    public static function getMatchLives($mids, $sport = self::kSportFootball) {
        $lives = MatchLive::query()->where('sport', $sport)
            ->whereIn('match_id', $mids)
            ->select('match_id')
            ->get();
        return $lives;
    }

    /**
     * 是否存在有版权
     * @param $mid
     * @param int $sport
     * @return bool
     */
    public static function isPriMatchLive4Pc($mid, $sport = MatchLive::kSportFootball) {
        $query = MatchLiveChannel::query()
            ->join('match_lives', 'match_lives.id', 'match_live_channels.live_id')
            ->where('match_lives.match_id', $mid)
            ->where('match_lives.sport', $sport)
            ->where('match_live_channels.show', MatchLiveChannel::kShow)
            ->where('match_live_channels.isPrivate', MatchLiveChannel::kIsPrivate)
            ->whereIn('match_live_channels.platform', [MatchLiveChannel::kPlatformAll, MatchLiveChannel::kPlatformPC]);
        $isPri = $query->count() > 0;
        return $isPri;
    }

    public static function copyLgMatchLive(\App\Models\LgMatch\MatchLive $live) {
        $live_id = $live->id;
        $newLive = self::query()->find($live_id);
        if (!isset($newLive)) {
            $newLive = new MatchLive();
            $newLive->id = $live_id;
        }
        $newLive->match_id = $live->match_id;
        $newLive->sport = $live->sport;
        $newLive->league_id = $live->league_id;
        $newLive->ad_id = $live->ad_id;
        $newLive->impt = $live->impt;
        $newLive->created_at = $live->created_at;
        $newLive->updated_at = $live->updated_at;
        try {
            $newLive->save();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

}