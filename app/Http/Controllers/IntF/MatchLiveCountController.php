<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/12/14
 * Time: 下午4:33
 */
namespace App\Http\Controllers\IntF;


use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MatchLiveCountController extends Controller
{
    /**
     * 外部调用接口
     */
    /**
     * 根据比赛id获取直播数量
     * @param $sport
     * @param $mid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function matchLiveCountById(Request $request, $sport, $mid) {
        $platform = $request->input("platform", MatchLiveChannel::kUseAll);

        return response()->json(self::getMatchLiveCountById($mid, $sport, $platform));
    }

    /**
     * 根据时间范围获取相应的直播数量
     * @param $sport
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function matchLiveCountByDate(Request $request, $sport) {
        return response()->json(["code"=>500]);
//        $startDate = $request->input("startDate");
//        $endDate = $request->input("endDate");
//        $startDate2 = $request->input("startDate2");
//        $isForPc = $request->input("isForPc", 0) == 1;
//        $platform = $request->input("platform", MatchLiveChannel::kUseAll);
//
//        $dateArray['startDate'] = $startDate;
//        $dateArray['startDate2'] = $startDate2;
//        $dateArray['endDate'] = $endDate;

//        return response()->json(self::getMatchLiveCountByDate($dateArray, $isForPc, $sport, $platform));
    }



    //根据传入的比赛id, 获取比赛推荐的数量z
    protected static function getMatchLiveCountByIds($mids,$isForPc = false, $sport = MatchLive::kSportFootball,$platform = MatchLiveChannel::kUse310) {
        //是否有直播
        $query = MatchLive::query()
            ->selectRaw('match_id as id, count(channel.id) as count')
            ->leftJoin('match_live_channels as channel', 'match_lives.id', 'channel.live_id')
            ->whereIn('match_id', $mids)
            ->where('sport', $sport)
            ->where('show', MatchLiveChannel::kShow);
        if ($isForPc) {
            $query->whereIn('channel.platform', [1, 2]);
        } else {
            $query->whereIn('channel.platform', [1, 3]);
        }

        switch ($platform){
            case MatchLiveChannel::kUse310:
                $query->where(function ($orQuery) {
                    $orQuery->where('use',MatchLiveChannel::kUseAll);
                    $orQuery->orWhere('use',MatchLiveChannel::kUse310);
                });
                break;
            case MatchLiveChannel::kUseAikq:{
                $query->where(function ($orQuery) {
                    $orQuery->where('use',MatchLiveChannel::kUseAll);
                    $orQuery->orWhere('use',MatchLiveChannel::kUseAikq);
                });
            }
                break;
            case MatchLiveChannel::kUseHeitu:{
                $query->where('use',MatchLiveChannel::kUseAll);
            }
                break;
            case MatchLiveChannel::kUseAll:
                break;
        }

        $liveCounts = $query
            ->groupBy('match_id', 'sport')->get()
            ->mapWithKeys(function ($item){
                return [$item->id => $item->count];
            })->all();
        return $liveCounts;
    }

    //根据传入的时间范围，获取直播平台的数量
    protected static function getMatchLiveCountByDate($dateArray,$isForPc = false,$sport = MatchLive::kSportFootball,$platform = MatchLiveChannel::kUse310) {
        if ($sport == MatchLive::kSportBasketball) {
            $query = BasketMatch::query()->select('basket_matches.id')
                ->whereBetween('status', [-1, 100]);
        } else {
            $query = Match::query()->select('matches.id')
                ->whereBetween('status', [-1, 4]);
        }

        $startDate = isset($dateArray['startDate2']) ? $dateArray['startDate2'] : $dateArray['startDate'];

        $normalIds = $query->whereBetween('time', [$startDate, $dateArray['endDate']])
            ->orderBy('time', 'desc')->get()
            ->map(function ($item){
                return $item->id;
            })->all();
        return self::getMatchLiveCountByIds($normalIds,$isForPc, $sport,$platform);
    }


    //根据传入的时间范围，获取直播平台的数量
    protected static function getMatchLiveCountById($mid,$sport = MatchLive::kSportFootball,$platform = MatchLiveChannel::kUse310) {
        $liveCountArray = array();
        $pcLiveCount = 0;
        $wapLiveCount = 0;
        //是否有直播 开始
        $live = MatchLive::query()->where('sport', $sport)
            ->where('match_id', $mid)->first();
        if (isset($live)) {
            $channels = MatchLiveChannel::query()->where('live_id', $live->id)->where('show', MatchLiveChannel::kShow)->get();
            foreach ($channels as $channel) {
                $platform = $channel->platform;
                if ($platform == MatchLiveChannel::kPlatformAll) {
                    $pcLiveCount++;
                    $wapLiveCount++;
                } else if ($platform == MatchLiveChannel::kPlatformPC) {
                    $pcLiveCount++;
                } else if ($platform == MatchLiveChannel::kPlatformWAP) {
                    $wapLiveCount++;
                }
            }
        }
        $liveCountArray['pc_live'] = $pcLiveCount;
        $liveCountArray['live'] = $wapLiveCount;

        return $liveCountArray;
    }
}