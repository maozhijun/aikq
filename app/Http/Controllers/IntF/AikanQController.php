<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/12/14
 * Time: 下午4:33
 */
namespace App\Http\Controllers\IntF;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Stage;
use App\Models\LgMatch\Team;
use App\Models\Match\BasketLeague;
use App\Models\Match\BasketMatch;
use App\Models\Match\League;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\Match\OtherMatch;
use App\Models\Subject\SubjectArticle;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectSpecimen;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class AikanQController extends Controller
{
    public $errorCid = 'liaogou_pc_error_url_cid';

    function usortTime($a, $b) {
        if ($a['time'] > $b['time']){
            return 1;
        }
        elseif ($a['time'] < $b['time']){
            return -1;
        }
        else{
            return 0;
        }
    }

    /*************** kanqiuma接口 ********************/

    public function livesError(Request $request){
        $cid = $request->input('cid',0);
        $this->saveLivesError($cid);
    }

    public function saveLivesError($cid) {
        if ($cid <= 0)
            return;
        if (Redis::exists($this->errorCid)){
            $cache = Redis::get($this->errorCid);
            $cache = json_decode($cache,true);
        }
        else{
            $cache = array();
        }
        $cache[$cid] = array('cid'=>$cid,'time'=>date_create()->getTimestamp());
        Redis::set($this->errorCid,json_encode($cache));
        //设置过期时间 60分钟
        Redis::expire($this->errorCid, 60*60);
    }

    ////////////////////////////////   列表接口开始   ////////////////////////////////
    /**
     * 足球、篮球所有比赛列表接口
     * @param Request $request
     * @param $isMobile
     * @return mixed
     */
    public function livesJson(Request $request, $isMobile = null) {
        $isMobile = isset($isMobile) ? $isMobile : $request->input('isMobile',0);
        $bet = $request->input('bet', 0);//0：非竞彩，1：竞彩
        $result = $this->livesJsonData($bet, $isMobile);
        return response()->json($result);
    }

    /**
     * 直播数据
     * @param $bet
     * @param bool $isMobile
     * @return array
     */
    public function livesJsonData($bet, $isMobile = false) {
        $match_array = [];
        $startTime = time();
        $query = $this->getLiveMatches(MatchLive::kSportFootball, $bet);
        $footballMatches = $query->get();
        //dump("获取足球实体时间：" . (time() - $startTime));

        $fArray = [];

        $startTime = time();
        foreach ($footballMatches as $match){
            $fArray[] = $this->match2Array($match, $isMobile, MatchLive::kSportFootball);
        }
        //dump("足球实体转化时间：" . (time() - $startTime));

        //篮球
        $startTime = time();
        $query = $this->getLiveMatches(MatchLive::kSportBasketball, $bet);
        $basketballMatches = $query->get();
        //dump("获取篮球实体时间：" . (time() - $startTime));

        $bArray = [];
        $startTime = time();
        foreach ($basketballMatches as $match){
            $bArray[] = $this->match2Array($match, $isMobile, MatchLive::kSportBasketball);
        }
        //dump("篮球实体转化时间：" . (time() - $startTime));

        //自建赛事
        $startTime = time();
        $query = $this->getLiveMatches(MatchLive::kSportSelfMatch);
        $otherMatches = $query->get();
        //dump("获取自建实体时间：" . (time() - $startTime));

        $startTime = time();
        foreach ($otherMatches as $match){
            $now = time();
            $time = strtotime($match->time);
            $end_time = strtotime($match->end_time);
            $match['status'] = $match->getStatus();
            $match['sport'] = MatchLive::kSportSelfMatch;
            $match['league_name'] = $match['lname'];
            $match['isMatching'] = ($now >= $time && $end_time >= $now);
            $match['host_icon'] = '';
            $match['away_icon'] = '';
            $match['project'] = $match->projectCn();
            //是否有专家推荐
            if ($isMobile){
                $array = MatchLive::query()->find($match['live_id'])->mAiKqChannels();
            }
            else{
                $array = MatchLive::query()->find($match['live_id'])->kAiKqChannels();
            }
            $match['channels'] = $array;
        }
        $otherMatches = $otherMatches->toArray();
        //dump("自建实体转化时间：" . (time() - $startTime));

        $matches = array_merge($fArray, $bArray);
        $matches = array_merge($matches, $otherMatches);

        usort($matches, array($this,"usortTime"));

        foreach ($matches as $match) {
            if (count($match['channels']) == 0)
                continue;
            $time = date('Y-m-d', strtotime($match['time']));
            $key = $match['sport']."_".$match['mid'];
            if (isset($match_array[$time])) {
                $match_array[$time][$key] = $match;
            } else {
                $match_array[$time] = [$key=>$match];
            }
        }
        $result = ['matches'=>$match_array];
        return $result;
    }


    protected function getLiveMatches($sport, $bet = '') {
        $start = date('Y-m-d H:i:s', strtotime('-6 hours'));
        $end = date('Y-m-d H:i:s', strtotime('+7 days'));
        $ch_time = date('Y-m-d H:i:s', strtotime('-10 days'));
        //$ch_table = DB::raw('(SELECT live_id FROM match_live_channels WHERE (isPrivate = ' . MatchLiveChannel::kIsPrivate . ' or `use` = ' . MatchLiveChannel::kUseAiKQ . ') AND created_at >= \'' . $ch_time . '\' AND `show` = ' . MatchLiveChannel::kShow . ' GROUP BY live_id) as match_live_channels');
        $ch_table = DB::raw('(SELECT live_id FROM match_live_channels WHERE isPrivate = ' . MatchLiveChannel::kIsPrivate . ' AND updated_at >= \'' . $ch_time . '\' AND `show` = ' . MatchLiveChannel::kShow . ' GROUP BY live_id) as match_live_channels');
        if ($sport == MatchLive::kSportFootball) {
            //足球
            $query = Match::query();
            $query->join('match_lives', 'matches.id', '=', 'match_lives.match_id');
            //有版权的线路比赛也显示在列表中 开始
            $query->leftjoin($ch_table,'match_lives.id', 'match_live_channels.live_id');
            //有版权的线路比赛也显示在列表中 结束
            //$query->where('matches.status', '>', -1);//已完结或者比赛状态异常的赛事不显示
            $query->where('match_lives.sport', MatchLive::kSportFootball);
            $query->whereBetween('matches.time', [$start, $end]);
            //爱球只出有版权的赛事。
            $query->where(function ($orQuery) {
                $orQuery->whereNotNull('match_live_channels.live_id');
                $orQuery->orWhereIn('matches.lid', MatchLive::FootballPrivateArray);
            });

            if ($bet == 1) {
                $query->whereNotNull('matches.betting_num');
            }
            $query->select("matches.*");
            $query->addSelect("matches.genre as genre");
            $query->addSelect("matches.id as mid");
            $query->addSelect("match_lives.id as live_id");
            $query->orderby('time','asc');
            return $query;
        } else if ($sport == MatchLive::kSportBasketball) {
            $matchTable = 'basket_matches';
            $query = BasketMatch::query();
            $query->join('match_lives', $matchTable.'.id', '=', 'match_lives.match_id');

            //有版权的线路比赛也显示在列表中 开始
            $query->join($ch_table,'match_lives.id', 'match_live_channels.live_id');
            //有版权的线路比赛也显示在列表中 结束

            //$query->where($matchTable.'.status', '<>', -1);//已完结的赛事不显示
            $query->where('match_lives.sport', MatchLive::kSportBasketball);
            $query->whereBetween($matchTable.'.time', [$start, $end]);
            $query->where(function ($orQuery) use ($matchTable) {
                $orQuery->whereNotNull('match_live_channels.live_id');
                $orQuery->orWhereIn($matchTable.'.lid', MatchLive::BasketballPrivateArray);//只出NBA\CBA\的大赛事
            });
            if ($bet == 1) {
                $query->whereNotNull('basket_matches.betting_num');
            }
            $query->select($matchTable.".*");
            $query->addSelect($matchTable.".id as mid");
            $query->addSelect("match_lives.id as live_id");
            $query->orderby('time','asc');
            return $query;
        } else if ($sport == MatchLive::kSportSelfMatch) {
            //自建赛事
            $other_start = date('Y-m-d H:i:s', strtotime('-3 hours'));
            $query = OtherMatch::query();
            $query->join('match_lives', 'other_matches.id', '=', 'match_lives.match_id');
            //有版权的线路比赛也显示在列表中 开始
            $query->leftjoin($ch_table,'match_lives.id', 'match_live_channels.live_id');
            //有版权的线路比赛也显示在列表中 结束
            $query->where('match_lives.sport', MatchLive::kSportSelfMatch);

            $query->whereBetween('other_matches.time', [$other_start, $end]);//未开始的赛事
            $query->where('other_matches.end_time', '>=', date('Y-m-d H:i'));//未结束

            //爱球只出有版权的赛事。
            $query->where(function ($orQuery) {
                $orQuery->whereNotNull('match_live_channels.live_id');
            });
            $query->select("other_matches.*");
            $query->addSelect("other_matches.id as mid");
            $query->addSelect("match_lives.id as live_id");
            $query->orderby('time','asc');
            return $query;
        }
        return null;
    }

    /**
     * 篮球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function basketballLivesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);

        $result = $this->basketballLivesJsonData($isMobile);
        return response()->json($result);
    }

    public function basketballLivesJsonData($isMobile) {
        $match_array = [];
        $bArray = [];

        $query = $this->getLiveMatches(MatchLive::kSportBasketball);
        $matches = $query->get();
        foreach ($matches as $match) {
            $bArray[] = $this->match2Array($match, $isMobile, MatchLive::kSportBasketball);
        }

        foreach ($bArray as $match) {
            if (count($match['channels']) == 0) continue;
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($match_array[$time])) {
                $match_array[$time][] = $match;
            } else {
                $match_array[$time] = [$match];
            }
        }

        $result = ['matches'=>$match_array];
        return $result;
    }

    /**
     * 足球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function footballLivesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);
        $result = $this->footballLivesJsonData($isMobile);
        return response()->json($result);
    }

    public function footballLivesJsonData($isMobile) {
        $query = $this->getLiveMatches(MatchLive::kSportFootball);
        $matches = $query->get();

        $match_array = [];
        $fArray = [];

        foreach ($matches as $match) {
            $fArray[] = $this->match2Array($match, $isMobile, MatchLive::kSportFootball);
        }

        foreach ($fArray as $match) {
            if (count($match['channels']) == 0) continue;
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($match_array[$time])) {
                $match_array[$time][] = $match;
            } else {
                $match_array[$time] = [$match];
            }
        }

        $result = ['matches'=>$match_array];
        return $result;
    }

    /**
     * 自建赛事列表
     * @param Request $request
     * @return mixed
     */
    public function otherLivesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);
        $result = $this->otherLivesJsonData($isMobile);
        return Response::json($result);
    }

    public function otherLivesJsonData($isMobile) {
        $match_array = [];
        $query = $this->getLiveMatches(MatchLive::kSportSelfMatch);
        $matches = $query->get();
        foreach ($matches as $match) {
            $now = time();
            $time = strtotime($match->time);
            $end_time = strtotime($match->end_time);
            $match['status'] = $match->getStatus();
            $match['sport'] = MatchLive::kSportSelfMatch;
            $match['league_name'] = $match->lname;
            $match['isMatching'] = ($now >= $time && $end_time >= $now);
            $match['host_icon'] = '';
            $match['away_icon'] = '';
            $match['project'] = $match->projectCn();
            if ($isMobile){
                $live = MatchLive::find($match['live_id']);
                $array = $this->getMobileChannels($live);
            }
            else{
                $array = MatchLive::find($match['live_id'])->kAiKqChannels();
            }
            if (count($array) > 0) {
                $match['channels'] = $array;
                $time = date('Y-m-d', strtotime($match->time));
                if (isset($match_array[$time])) {
                    $match_array[$time][] = $match;
                } else {
                    $match_array[$time] = [$match];
                }
            }
        }
        $result = ['matches'=>$match_array];
        return $result;
    }
    ////////////////////////////////   列表接口结束   ////////////////////////////////

    ////////////////////////////////   终端接口开始   ////////////////////////////////

    /**
     * 篮球直播终端接口
     * @param Request $request
     * @param $id
     * @param $mobile
     * @return mixed
     */
    public function basketDetailJson(Request $request, $id, $mobile = false){
        $mobile = $request->input('isMobile',0) || $mobile;
        $result = $this->basketDetailJsonData($id, $mobile);
        return response()->json($result);
    }

    /**
     * 篮球直播终端数据
     * @param $id
     * @param bool $mobile
     * @return mixed
     */
    public function basketDetailJsonData($id, $mobile = false){
        $match = BasketMatch::query()->select('basket_matches.*',"basket_matches.id as mid")->find($id);
        if (!isset($match)) {
            return null;
        }
        $live = MatchLive::query()->where('match_id', $id)->where('sport', MatchLive::kSportBasketball)->first();
        //赛前五分钟、比赛中、比赛结束后10分钟 显示视频
        $show_live = false;
        if (!$show_live) {//比赛中
            $show_live = $match->status > 0;
        }
        if (!$show_live) {
            $matchTime = strtotime($match->time);
            if ($match->status == 0) {//未开始
                $show_live = (($matchTime - time()) / 60) <= 60;//赛前五分钟
            } else if ($match->status == -1) {//已结束
                if (isset($match->timehalf))
                    $time = strtotime($match->timehalf);
                else
                    $time = strtotime($match->time);
                if (isset($timehalf)) {
                    $show_live = ((time() - $time) / 60) <= (45 + 60);//赛后十分钟
                }
                else {
                    $show_live = ((time() - $time) / 60) <= (120 + 60);//赛后十分钟
                }
            }
        }

        $match['sport'] = 2;
        $result['match'] = $match;
        if (isset($live)) {
            if ($mobile) {
                $live['channels'] = $this->getMobileChannels($live);
                //$live['channels'] = $live->mAiKqChannels();
            } else
                $live['channels'] = $live->kAiKqChannels();
        }
        $result['live'] = $live;
        $result['host_icon'] = $match->getTeamIcon(true);
        $result['away_icon'] = $match->getTeamIcon(false);
        $result['show_live'] = $show_live;
        $result['data'] = array();
        
        $time = date('m月d日 H:i', strtotime($match['time']));

        //title，主vs客，根据球队id大小排序
        $result['title'] =  "[".$match['hname']."VS".$match['aname']."]".$match['win_lname'].$match['hname']."VS".$match['aname']."直播、历史战绩_爱看球直播";
        $result['h1'] = $match['hname'] . 'VS' . $match['aname'];
        $result['description'] = '爱看球正在为直播 ' . date('m月d日 H:i', strtotime($match['time'])) . ' ' . $match['lname'] . ' ' . $match['hname'] . ' VS ' . $match['aname'] . "，JRS低调看直播就来爱看球直播。";
        if (isset($match['hid']) && isset($match['aid'])) {
            if ($match['hid'] > $match['aid']) {
                $result['title'] =  "[".$match['aname']."VS".$match['hname']."]".$match['win_lname'].$match['aname']."VS".$match['hname']."直播、历史战绩_爱看球直播";
                $result['h1'] = $match['aname'] . 'VS' . $match['hname'];
                $result['description'] = '爱看球正在为直播 ' . date('m月d日 H:i', strtotime($match['time'])) . ' ' . $match['lname'] . ' ' . $match['aname'] . ' VS ' . $match['hname'] . "，JRS低调看直播就来爱看球直播。";
            }
        }
        $result['keywords'] = '爱看球直播,' . $match['lname'] . '直播,' . $match['hname'] . '直播,' . $match['aname'] . '直播,高清直播';

        $result['ma_url'] = self::getMobileHttpUrl(CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $id));
//        dump($result);
        return $result;
    }

    /**
     * 自建赛事直播终端接口
     * @param Request $request
     * @param $id
     * @param $mobile
     * @return mixed
     */
    public function otherDetailJson(Request $request, $id, $mobile = false){
        $mobile = $request->input('isMobile',0) == 1 || $mobile;
        $result = $this->otherDetailJsonData($id, $mobile);
        if (!isset($result)) {
            return abort(404);
        }
        return response()->json($result);
    }

    /**
     * 其他直播终端数据
     * @param $id
     * @param bool $mobile
     * @return mixed
     */
    public function otherDetailJsonData($id, $mobile = false){
        $match = OtherMatch::query()->selectRaw("*, other_matches.id as mid")->find($id);
        if (!isset($match)) {
            return null;
        }
        $live = MatchLive::query()->where('match_id', $id)->where('sport', MatchLive::kSportSelfMatch)->first();
        //赛前五分钟、比赛中、比赛结束后10分钟 显示视频
        $show_live = false;
        if (!$show_live) {
            $matchTime = strtotime($match->time);
            $end_time = strtotime($match->end_time);
            if ($matchTime >= time()) {
                $show_live = (($matchTime - time()) / 60) <= 60;
            } else {
                $show_live = $end_time + 30 * 60 >= time() ;
            }
        }

        $match['sport'] = MatchLive::kSportSelfMatch;
        $result['match'] = $match;
        if (isset($live)) {
            if ($mobile) {
                $live['channels'] = $this->getMobileChannels($live);
                //$live['channels'] = $live->mAiKqChannels();
            } else
                $live['channels'] = $live->kAiKqChannels();
        }
        $result['live'] = $live;
        $result['host_icon'] = '';
        $result['away_icon'] = '';
        $result['show_live'] = $show_live;
        $result['data'] = array();
        return $result;
    }

    /**
     * 直播终端接口
     * @param Request $request
     * @param $id
     * @param $mobile
     * @return mixed
     */
    public function detailJson(Request $request, $id, $mobile = false){
        $mobile = $request->input('isMobile',0) || $mobile;
        $result = $this->detailJsonData($id, $mobile);
        if (!isset($result)) {
            return abort(404);
        }
        return response()->json($result);
    }

    /**
     * 足球终端页数据接口
     * @param $id
     * @param $mobile
     * @return mixed
     */
    public function detailJsonData($id, $mobile) {
        $match = Match::query()->select('matches.*',"matches.id as mid")->find($id);
        if (!isset($match)) {
            return null;
        }
        $live = MatchLive::query()->where('match_id', $id)->where('sport', MatchLive::kSportFootball)->first();
        //赛前五分钟、比赛中、比赛结束后10分钟 显示视频
        $show_live = false;
        if (!$show_live) {//比赛中
            $show_live = $match->status > 0 && $match->status < 5;
        }
        if (!$show_live) {
            $matchTime = strtotime($match->time);
            if ($match->status == 0) {//未开始
                $show_live = (($matchTime - time()) / 60) <= 60;//赛前五分钟
            } else if ($match->status == -1) {//已结束
                $timehalf = strtotime($match->timehalf);
                $show_live = ((time() - $timehalf) / 60) <= (45 + 60);//赛后十分钟
            }
        }

        $match['sport'] = 1;
        $result['match'] = $match;
        if (isset($live)) {
            if ($mobile) {
                $live['channels'] = $this->getMobileChannels($live);//判断是否有移动线路，如果没有，则取一条PC线路显示。
            } else {
                $live['channels'] = $live->kAiKqChannels();
            }
        }
        $result['live'] = $live;
        $result['host_icon'] = $match->getTeamIcon(true);
        $result['away_icon'] = $match->getTeamIcon(false);
        $result['show_live'] = $show_live;

        $match['getMatchTimeMin'] = $match->getMatchTimeMin();
        $result['match'] = $match;
        $result['live'] = $live;
        $result['show_live'] = $show_live;

        $time = date('m月d日 H:i', strtotime($match['time']));

        //title，主vs客，根据球队id大小排序
        $result['title'] =  "[".$match['hname']."VS".$match['aname']."]".$match['win_lname'].$match['hname']."VS".$match['aname']."直播、历史战绩_爱看球直播";
        $result['h1'] = $match['hname'] . 'VS' . $match['aname'];
        $result['description'] = '爱看球正在为直播 ' . date('m月d日 H:i', strtotime($match['time'])) . ' ' . $match['lname'] . ' ' . $match['hname'] . ' VS ' . $match['aname'] . "，JRS低调看直播就来爱看球直播。";
        if (isset($match['hid']) && isset($match['aid'])) {
            if ($match['hid'] > $match['aid']) {
                $result['title'] =  "[".$match['aname']."VS".$match['hname']."]".$match['win_lname'].$match['aname']."VS".$match['hname']."直播、历史战绩_爱看球直播";
                $result['h1'] = $match['aname'] . 'VS' . $match['hname'];
                $result['description'] = '爱看球正在为直播 ' . date('m月d日 H:i', strtotime($match['time'])) . ' ' . $match['lname'] . ' ' . $match['aname'] . ' VS ' . $match['hname'] . "，JRS低调看直播就来爱看球直播。";
            }
        }
        $result['keywords'] = '爱看球直播,' . $match['lname'] . '直播,' . $match['hname'] . '直播,' . $match['aname'] . '直播,高清直播';

        $result['ma_url'] = self::getMobileHttpUrl(CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $id));
        return $result;
    }

    public function mobileDetailJson(Request $request, $id) {
        return $this->detailJson($request, $id, true);
    }

    public function mobileBasketDetailJson(Request $request, $id) {
        return $this->basketDetailJson($request, $id, true);
    }

    public function mobileOtherDetailJson(Request $request, $id) {
        return $this->otherDetailJson($request, $id, true);
    }
    /////////////////////////////   终端接口结束   /////////////////////////////

    /**
     * 获取WAP端直播线路。如果有则取全部wap端线路，如果没有则取一条PC线路。
     * @param $live
     * @return array
     */
    protected function getMobileChannels($live) {
        $channels = [];
        if (!isset($live)) {
            return $channels;
        }
        $mChannels = $live->mAiKqChannels();
        if (count($mChannels) == 0) {
            $pcChannels = $live->kAiKqChannels();
            if (count($pcChannels) > 0) {
                $channels= [$pcChannels[count($pcChannels) - 1]];
            } else {
                $channels = [];
            }
        } else {
            $channels = $mChannels;
        }
        //判断是否有移动线路，如果没有，则取一条PC线路显示。
        return $channels;
    }

    /**
     * 获取当前正在直播的比赛
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLiveMatchesJson(Request $request) {
        $sport = $request->input('sport',0);
        $mid = $request->input('mid');//当前页面直播中的id 格式应该是 id1-id2...-idn
        if (!empty($mid)) {
            $id_array = explode('-', $mid);
        }
        $tmp = array();
        if (isset($id_array) && count($id_array) > 0) {
            foreach ($id_array as $a) {
                $tmp[] = substr($a, 1);
            }
            $id_array = $tmp;
        }
        $matchQuery = Match::query();
        $matchQuery->join('match_lives', 'matches.id', '=', 'match_lives.match_id');
        $matchQuery->where('match_lives.sport', MatchLive::kSportFootball);
        $matchQuery->whereBetween('status', [1, 4]);//未开始 至 加时
        if (isset($id_array)) {
            $matchQuery->whereNotIn('matches.id', $id_array);
        }
        $matchQuery->where('matches.time', '>=', date('Y-m-d H:i:s', strtotime('-3 hours')));
        $matchQuery->where('matches.time', '<=', date('Y-m-d H:i:s', strtotime('+5 min')));
        $matchQuery->select('matches.*');
        $matchQuery->addSelect('matches.id as mid');
        $matches = $matchQuery->get();

        foreach ($matches as $match){
            $match['sport'] = 1;
            $match['league_name'] = $match->getLeagueName();
        }
        $footballMatches = $matches;

        $matchQuery = BasketMatch::query();
        $matchQuery->join('match_lives', 'basket_matches.id', '=', 'match_lives.match_id');
        $matchQuery->where('match_lives.sport', MatchLive::kSportBasketball);
        $matchQuery->whereBetween('status', [1, 50]);//未开始 至 加时
        if (isset($id_array)) {
            $matchQuery->whereNotIn('basket_matches.id', $id_array);
        }
        $matchQuery->where('basket_matches.time', '>=', date('Y-m-d H:i:s', strtotime('-3 hours')));
        $matchQuery->where('basket_matches.time', '<=', date('Y-m-d H:i:s', strtotime('+5 min')));
        $matchQuery->select('basket_matches.*');
        $matchQuery->addSelect('basket_matches.id as mid');
        $matches = $matchQuery->get();

        foreach ($matches as $match){
            $match['sport'] = 2;
            $match['league_name'] = $match->getLeagueName();
        }
        $basketMatches = $matches;

        switch ($sport){
            case 0:
                $basketMatches = $basketMatches->toArray();
                $footballMatches = $footballMatches->toArray();
                break;
            case 1:
                $basketMatches = array();
                $footballMatches = $footballMatches->toArray();
                break;
            case 2:
                $basketMatches = $basketMatches->toArray();
                $footballMatches = array();
                break;
        }
        $matches = array_merge($footballMatches,$basketMatches);
        usort($matches, array($this,"usortTime"));

        $result['matches'] = $matches;
        return Response::json($result);
    }

    /******************* *******************/

    /**
     * 获取直播频道内容
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function liveChannelJson(Request $request, $id) {
        $ch = MatchLiveChannel::query()->find($id);
        $result = ['code'=>0];
        if (!isset($ch)) {
            $result['code'] = -1;
        } else {
            $result['id'] = $id;
            $result['show'] = $ch->show;//是否显示
            $result['player'] = $ch->player;//播放方式
            $result['link'] = $ch->content;//外链
        }
        return response()->json($result);
    }

    /**
     * 刷新json
     * @param $mid
     * @param $sport
     * @param $ch_id
     */
    protected function flushJson($mid, $sport, $ch_id) {
        //$url = 'http://www.aikq.cc/live/cache/match/detail_id/' . $mid . '/' . $sport . '?ch_id=' . $ch_id;
        $url = 'http://cms.aikq.cc/live/player-json/' . $ch_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,2);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

    //============================================================================================================================================================//
    //专题相关
    /**
     * 专题列表接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjects(Request $request) {
        $subjects = $this->subjectsData();
        return response()->json($subjects);
    }

    /**
     * 专题列表接口
     * @return array
     */
    public function subjectsData() {
        $query = SubjectLeague::query();
        $query->orderByRaw('ifNull(od, 999)');
        $sls = $query->get();
        $subjects = [];
        foreach ($sls as $sl) {
            $subjects[$sl->id] = ['name'=>$sl->name, 'name_en'=>$sl->name_en];
        }
        return $subjects;
    }

    /**
     * 获取专题封面图
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectLeaguesImages(Request $request) {
        $time = $request->input('time');
        $query = SubjectLeague::query();
        if (!empty($time)) {
            $query->where('updated_at', '>', date('Y-m-d H:i:s', $time) );
        }
        $query->orderByDesc('updated_at');
        $leagues = $query->get();
        $league_array = [];
        foreach ($leagues as $league) {
            $league_array[] = $league->icon;
        }
        if (count($leagues) > 0) {
            $last = strtotime($leagues[0]->updated_at);
        } else {
            $last = '';
        }
        $array = ['covers'=>$league_array, 'last'=>$last];
        return response()->json($array);
    }

    /**
     * 世界杯焦点图
     * @param Request $request
     * @param $slid
     * @return mixed
     */
    public function subjectCarousel(Request $request, $slid){
        $array = Carousel::FIFACarousel();
        return response()->json($array);
    }

    /**
     * 专题终端内容接口
     * @param Request $request
     * @param $slid
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectDetail(Request $request, $slid) {
        $isMobile = $request->input('isMobile') == 1;
        $sl = SubjectLeague::query()->find($slid);
        if (!isset($sl)) {
            return \response()->json(['error'=>'专题不存在']);
        }
        $result = $this->subjectDetailData($isMobile, $sl);
        return response()->json($result);
    }

    /**
     * @param $isMobile
     * @param SubjectLeague $sl
     * @return array|null
     */
    public function subjectDetailData($isMobile, $sl) {
        $result = [];
        if (!isset($sl)) {
            return null;
        }
        $sport = $sl->sport;
        $slid = $sl->id;

        //专题资讯 开始
        $articles = PcArticle::articlesByType($sl->name_en);
        $article_array = [];
        foreach ($articles as $article) {
            $url = $article->url;
            if (is_null($url)){
                $url = $article->getUrl();
            }
            $article_array[] = ['title'=>$article->title, 'link'=>$url,'update_at'=>$article->publish_at, 'cover'=>$article->cover];
        }
        $result['articles'] = $article_array;
        //专题资讯 结束

        //联赛排名 开始
        $lid = $sl->lid;
        if ($sport == MatchLive::kSportFootball) {//联赛才出
            if ($sl->type == 1) {//联赛排名
                $ranks = Score::getFootballScores($lid);
                $rank_array = [];
                foreach ($ranks as $rank) {
                    $rank_array[] = ['tid'=>$rank->tid, 'sport'=>$sport, 'lid'=>$rank->lid,
                        'name'=>$rank->tname, 'win'=>$rank->win, 'draw'=>$rank->draw, 'lose'=>$rank->lose
                        , 'score'=>$rank->score, 'rank'=>$rank->rank];
                }
                $result['ranks'] = $rank_array;//排名
            } else {//杯赛积分
                //获取最新的小组赛
                $result['ranks'] = Score::footballCupScores($lid);
            }
        } else {//篮球排名 暂时只有NBA / CBA
            if ($lid == 1) {//NBA 排名分东西部
                $west = BasketScore::getScoresByLid($lid, BasketScore::kZoneWest);
                $east = BasketScore::getScoresByLid($lid, BasketScore::kZoneEast);
                $result['ranks'] = ['west'=>$west, 'east'=>$east];
            } else {
                $result['ranks'] = BasketScore::getScoresByLid($lid);
            }
        }
        //联赛排名 结束

        //专题直播 开始
        $result['lives'] = $this->getSubjectLives($sl);
        //专题直播 结束

        //专题录像 开始
        $result['videos'] = SubjectVideo::newVideos($slid, $isMobile);
        //专题录像 结束

        //专题集锦 开始
        $result['specimens'] = SubjectSpecimen::getNewSpecimens($slid, $isMobile);
        //专题集锦 结束

        $result['subject'] = ['name'=>$sl->name, 'icon'=>$sl->icon, 'content'=>$sl->content, 'sport'=>$sl->sport, 'type'=>$sl->type, 'lid'=>$sl->lid];
        $result['title'] = '['.$sl->name.'直播]'.$sl->name.'免费在线直播观看_哪里可以看'.$sl->name.'直播网址-爱看球直播';
        $result['h1'] = $sl->name.'直播';
        return $result;
    }

    /**
     * 录像终端数据
     * @param $vid
     * @param bool $isMobile
     * @return array
     */
    public function subjectVideo($vid, $isMobile = false) {
        $video = SubjectVideo::query()->find($vid);
        if (!isset($video)) {
            return null;
        }
        $array = SubjectVideo::video2Array($video, $isMobile);
        if (count($array['channels']) == 0) {
            return null;
        }
        return $array;
    }

    /**
     * 集锦终端
     * @param $sid
     * @param $isMobile
     * @return array
     */
    public function subjectSpecimen($sid, $isMobile = false) {
        $result = [];
        $specimen = SubjectSpecimen::query()->find($sid);
        if (!isset($specimen)) {
            return null;
        }
        $platform = $specimen->platform;
        if ($isMobile && ($platform != MatchLive::kPlatformAll || $platform != MatchLive::kPlatformPhone) ) {
            return null;
        }
        $result['hname'] = $specimen->title;
        $result['aname'] = '';
        $result['channels'][] = ['id'=>$specimen->id, 'title'=>'集锦', 'player'=>$specimen->player, 'platform'=>$platform];
        $sl = SubjectLeague::query()->find($specimen->s_lid);
        if (isset($sl)) {
            $result['lname'] = $sl->name;
        }
        return $result;
    }

    /**
     * 获取集锦数据
     * @param $id
     * @param bool $isMobile
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectSpecimenData($id, $isMobile = false) {
        $specimen = SubjectSpecimen::query()->find($id);
        $platform = $specimen->platform;
        if ($isMobile && ($platform != MatchLive::kPlatformAll || $platform != MatchLive::kPlatformPhone) ) {
            return null;
        }
        $result['hname'] = $specimen->title;
        $result['aname'] = '';
        $result['channels'][] = ['id'=>$specimen->id, 'title'=>'集锦', 'player'=>$specimen->player, 'platform'=>$platform];
        $sl = SubjectLeague::query()->find($specimen->s_lid);
        if (isset($sl)) {
            $result['lname'] = $sl->name;
        }
        return $result;
    }

    /**
     * 录像线路json
     * @param Request $request
     * @param $cid
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideoChannelJson(Request $request, $cid) {
        $isMobile = $request->input('isMobile') == 1;
        $channel = SubjectVideoChannels::query()->find($cid);
        if (!isset($channel)) {
            return response()->json(['code'=>-1, 'message'=>'线路不存在']);
        }
        $result = ['code'=>0, 'playurl'=>$channel->content, 'player'=>$channel->player, 'platform'=>$channel->platform];
        return response()->json($result);
    }

    /**
     * 集锦线路json
     * @param Request $request
     * @param $cid
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectSpecimenChannelJson(Request $request, $cid) {
        $isMobile = $request->input('isMobile') == 1;
        $result = $this->subjectSpecimenChannelJsonData($isMobile, $cid);
        if (!isset($result)) {
            return response()->json(['code'=>-1, 'message'=>'线路不存在']);
        }
        return response()->json($result);
    }

    public function subjectSpecimenChannelJsonData($isMobile, $cid) {
        $specimen = SubjectSpecimen::query()->find($cid);
        if (!isset($specimen)) {
            return null;
        }
        $result = ['code'=>0, 'playurl'=>$specimen->link, 'player'=>$specimen->player, 'platform'=>$specimen->platform];
        return $result;
    }

    /**
     * 获取专题直播赛事
     * @param SubjectLeague $sl
     * @param bool $isMobile
     * @param int $count
     * @return array
     */
    protected function getSubjectLives(SubjectLeague $sl, $isMobile = false, $count = 15) {

        $sport = $sl->sport;
        $lid = $sl->lid;
        $type = $sl->type;
        if (!in_array($sport, [MatchLive::kSportFootball, MatchLive::kSportBasketball])) {
            return [];
        }
        if ($sport == MatchLive::kSportFootball) {
            $query = Match::query();
            //$tableName = 'matches';
        } else {
            $query = BasketMatch::query();
            //$tableName = 'basket_matches';
        }
        $query->where('lid', $lid);
        $query->where('time', '>=', date('Y-m-d H:i', strtotime('-3 hours')));
        $query->where('status', '>=', 0);
        $query->orderBy('time');
        $query->selectRaw('*, id as mid');
        $matches = $query->take($count)->get();

        $array = [];
        foreach ($matches as $match) {
            $time = strtotime($match->time);
            $day = strtotime(date('Y-m-d', $time));
            $mid = $match->mid;
            if ($sport == MatchLive::kSportFootball && $type == 2) {//1联赛 2杯赛
                $stage_id = $match->stage;
                $stage = Stage::query()->find($stage_id);
                if (!isset($stage)) {
                    $round = null;
                } else {
                    $round = $stage->name . $match->group;
                }
            } else {
                $round = $match->round;
            }
            $obj = ['time'=>$time, 'hname'=>$match->hname, 'aname'=>$match->aname, 'status'=>$match->status,
                'hid'=>$match->hid, 'aid'=>$match->aid, 'lid'=>$match->lid,
                'hscore'=>$match->hscore, 'ascore'=>$match->ascore, 'mid'=>$mid, 'sport'=>$sport, 'round'=>$round];
            $liveQuery = MatchLive::query()->where('match_id', $mid)->where('sport',$sport);
            $live = $liveQuery->first();
            $channels = [];
            if (isset($live)) {
                $channels = $isMobile ? $live->mAiKqChannels() : $live->kAiKqChannels();
            }
            $obj['channels'] = $channels;
            $array[$day][] = $obj;
        }
        return $array;
    }
    //============================================================================================================================================================//

    /****************** 世界杯 *******************/
    /**
     * 热门比赛
     * @param Request $request
     * @return mixed
     */
    public function fifaHotMatch(Request $request){
        $mids = FifaHotMatch::where('fifa_hot_matches.is_hot',FifaHotMatch::kHot)->get();
        $tmp = array();
        foreach ($mids as $mid){
            $tmp[] = $mid['mid'];
        }
        $matches = Match::from("matches as m")
            ->whereIn('m.id',$tmp)
            ->leftJoin("teams as home", "m.hid", "home.id")
            ->leftJoin("teams as away", "m.aid", "away.id")
            ->select('m.*')
            ->addSelect("home.icon as hicon", "away.icon as aicon")
            ->addSelect("m.id as mid")
            ->orderby('time','asc')
            ->get();
        ;

        $tmp = array();
        foreach ($matches as $match){
            $match->time = strtotime($match->time);
            $match->timehalf = strtotime($match->timehalf);
            $tmp[] = $match;
        }

        return Response::json($tmp);
    }

    /**
     * 淘汰赛赛程
     * @param Request $request
     * @return mixed
     */
    public function getFIFASchedule(Request $request){
        //全部球队的id
        $tids = array();
        $ids = WorldCupController::tids;
        foreach ($ids as $item){
            $tids[] = $item['tid'];
        }
        $teams = Team::whereIn('id',$tids)->get();
        $t = array();
        foreach ($teams as $team){
            $t[$team['id']] = $team;
        }

        $keys = array();
        for ($i = 0 ; $i < 8 ; $i++){
            $keys[] = array('key'=>'fifa_16_'.$i);
        }
        for ($i = 0 ; $i < 4 ; $i++){
            $keys[] = array('key'=>'fifa_8_'.$i);
        }
        for ($i = 0 ; $i < 2 ; $i++){
            $keys[] = array('key'=>'fifa_4_'.$i);
        }
        //3 4 名
        for ($i = 0 ; $i < 1 ; $i++){
            $keys[] = array('key'=>'fifa_2_'.$i);
        }
        for ($i = 0 ; $i < 1 ; $i++){
            $keys[] = array('key'=>'fifa_1_'.$i);
        }
        $tmp = AkqConf::whereIn('key',$keys)->orderby('key','desc')->get();
        foreach ($tmp as $item){
            for ($i = 0 ; $i < count($keys) ; $i++){
                $key = $keys[$i];
                if ($key['key'] == $item['key']){
                    $key['value'] = $item['value'];
                    if (isset($key['value']) && count(explode('-',$key['value'])) == 3){
                        $hid = explode('-',$key['value'])[0];
                        $aid = explode('-',$key['value'])[1];
                        $mid = explode('-',$key['value'])[2];
                        $key['hteam'] = isset($t[$hid])?$t[$hid]:null;
                        $key['ateam'] = isset($t[$aid])?$t[$aid]:null;
                        $match = Match::find($mid);
                        $key['match'] = $match;
                    }
                }
                $keys[$i] = $key;
            }
        }
        //按16强之类整理一次
        $a16 = array();
        $a8 = array();
        $a4 = array();
        $a2 = array();
        $a1 = array();
        $has16 = false;
        $has8 = false;
        $has4 = false;
        $has2 = false;
        $has1 = false;
        foreach ($keys as $key){
            if (str_contains($key['key'],'fifa_16')){
                if (isset($key['hteam']) || isset($key['ateam'])){
                    $has16 = true;
                }
                $a16[] = $key;
            }
            else if (str_contains($key['key'],'fifa_8')){
                if (isset($key['hteam']) || isset($key['ateam'])){
                    $has8 = true;
                }
                $a8[] = $key;
            }
            else if (str_contains($key['key'],'fifa_4')){
                if (isset($key['hteam']) || isset($key['ateam'])){
                    $has4 = true;
                }
                $a4[] = $key;
            }
            else if (str_contains($key['key'],'fifa_2')){
                if (isset($key['hteam']) || isset($key['ateam'])){
                    $has2 = true;
                }
                $a2[] = $key;
            }
            else if (str_contains($key['key'],'fifa_1')){
                if (isset($key['hteam']) || isset($key['ateam'])){
                    $has1 = true;
                }
                $a1[] = $key;
            }
        }
        $json = array(
            '16'=>$a16,
            '8'=>$a8,
            '4'=>$a4,
            '2'=>$a2,
            '1'=>$a1,
        );
        return Response::json($json);
    }

    /**
     * 根据channel id获取url
     * @param Request $request
     * @param $channelId
     * @param $mobile
     * @return mixed
     */
    public function getLiveUrl(Request $request, $channelId, $mobile = false){
        $channel = MatchLiveChannel::query()->find($channelId);
        if (is_null($channel) || is_null($channel->content)){
            return response()->json(array('code'=>-1,'message'=>'no channel'));
        }
        //$breakTTZB = $request->input('breakTTZB');//是否使用破解的天天直播，天天直播未给白名单，所以使用破解版。
        $live = $channel->matchLive;
        if (isset($live)) {
            //比赛数据
            $match = $live->getMatch();
        }
        if (!isset($match)){
            return response()->json(array('code'=>-1,'message'=>'no match'));
        }

        if ($live->sport == MatchLive::kSportSelfMatch) {//自建赛事返回

        }

        $show_live = false;
        if (!$show_live) {//比赛中
            $show_live = $match->status > 0;
        }
        if (!$show_live) {
            $matchTime = strtotime($match->time);
            if ($match->status == 0) {//未开始
                $show_live = (($matchTime - time()) / 60) <= 60;//赛前五分钟
            } else if ($match->status == -1) {//已结束
                if (isset($match->timehalf))
                    $time = strtotime($match->timehalf);
                else
                    $time = strtotime($match->time);
                if (isset($match->timehalf)) {
                    $show_live = ((time() - $time) / 60) <= (45 + 60);//赛后十分钟
                }
                else {
                    $show_live = ((time() - $time) / 60) <= (120 + 60);//赛后十分钟
                }
            }
        }

        $tmp = array('sport'=>$live['sport'], 'lid'=>isset($match['lid']) ? $match['lid'] : '',
            'status'=>$match->status,'show_live'=>$show_live, 'time'=>strtotime($match['time']));
        if ($match['status'] == 0 && !$show_live){
            $matchTime = strtotime($match['time']);
            $now = strtotime(date('Y-m-d H:i:s'));
            $hour = floor(($matchTime - $now) / (60 * 60));
            $minute = floor(($matchTime - $now - ($hour * 60 * 60)) / 60);
            $second = $matchTime - $now - ($hour * 60 * 60) - ($minute * 60);
            $hour_html = $hour > 0 ? '<i id="hour">' . $hour . '</i>小时' : '';
            $minute_html = $hour > 0 ? '<i id="minute">' . $minute . '</i>分钟' : (($minute > 0 ? '<i id="minute">' . $minute . '</i>分钟' : ''));
            $second_html = '<i id="second">' . $second . '</i>秒';
            $tmp['hour_html'] = $hour_html;
            $tmp['minute_html'] = $minute_html;
            $tmp['second_html'] = $second_html;
        }

        $match = $tmp;

        switch ($channel->type){
            case MatchLiveChannel::kTypeSS365:
                return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeSS365,'player'=>$channel->player,'cid'=>$channel->id,'id'=>$channel->content,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
                break;
            case MatchLiveChannel::kTypeBallBar:
                $playurl = $channel->content;
                if (!stristr($playurl,'https://www.ballbar.cc/live/')){
                    $playurl = 'https://www.ballbar.cc/live/' . $playurl;
                }
                return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeBallBar,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$playurl,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
                break;
            case MatchLiveChannel::kTypeTTZB:
                    if (self::isMobile($request)|| $request->input('isMobile',$mobile) ) {
                        $playurl = $this->ttzbLiveRTMPUrl($channel->content);
                        if (isset($playurl))
                            return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeTTZB,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$playurl,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
                        else
                            return response()->json(array('code'=>-1,'type'=>MatchLiveChannel::kTypeTTZB,'message'=>'error','match'=>$match, 'platform'=>$channel->platform ));
                    } else{
                        $playurl = $this->ttzbLiveRTMPUrl($channel->content);
                        if (isset($playurl))
                            return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeTTZB,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$playurl,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
                        else
                            return response()->json(array('code'=>-1,'type'=>MatchLiveChannel::kTypeTTZB,'message'=>'error','match'=>$match, 'platform'=>$channel->platform ));
                    }
                break;
            case MatchLiveChannel::kTypeWCJ:
//                $url = $this->wcjLiveUrl($channel->content,self::isMobile($request)||$request->input('isMobile',0));
//                if (isset($url))
//                    return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeWCJ,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$url,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
//                else
//                    return response()->json(array('code'=>-1,'type'=>MatchLiveChannel::kTypeWCJ,'message'=>'error','match'=>$match, 'platform'=>$channel->platform ));
                break;
            case MatchLiveChannel::kTypeDDK:
//                $data = $this->ddkLiveData($channel->content, self::isMobile($request) || $request->input('isMobile',0));
//                $url = $data['url'];
//                $js = $data['js'];
//                if (!empty($url)) {
//                    return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeDDK,'player'=>$channel->player,'cid'=>$channel->id, 'playurl'=>$url,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
//                } else if (!empty($js)) {
//                    return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeDDK,'player'=>$channel->player,'cid'=>$channel->id, 'js'=>$js, 'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
//                } else {
//                    return response()->json(array('code'=>-1,'type'=>MatchLiveChannel::kTypeDDK, 'message'=>'error','match'=>$match, 'platform'=>$channel->platform ));
//                }
                break;
            case MatchLiveChannel::kTypeKBS:
//                $url = $channel->getKBSLink();
//                if (isset($url))
//                    return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeDDK,'player'=>$channel->player,'cid'=>$channel->id, 'playurl'=>$url,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
//                else
//                    return response()->json(array('code'=>-1,'type'=>MatchLiveChannel::kTypeDDK, 'message'=>'error','match'=>$match, 'platform'=>$channel->platform ));
                break;
            case MatchLiveChannel::kTypeQQ:
                //$url = $channel->content;//$this->qqSportData($channel->content,self::isMobile($request)||$request->input('isMobile',0));
                //if ($url)
                //    return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeQQ,'player'=>MatchLiveChannel::kPlayerQQSport,'cid'=>$channel->id,'playurl'=>$url,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad  ));
                //else
                //    return response()->json(array('code'=>-1,'type'=>MatchLiveChannel::kTypeQQ, 'message'=>'error','match'=>$match, 'platform'=>$channel->platform ));
                break;
            case MatchLiveChannel::kTypeLZ:
            case MatchLiveChannel::kTypeOther:
                return response()->json(array('code'=>0,'type'=>$channel->type,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$channel->content,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
                break;
            case MatchLiveChannel::kTypeCCTVAPP:
                //$url = $this->getCCTVUrl($request,$channel->content);
                //return response()->json(array('code'=>0,'type'=>MatchLiveChannel::kTypeCCTVAPP,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$url,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
                break;
            case MatchLiveChannel::kTypeCode:
                $array = ['code'=>0, 'type'=>$channel->type, 'player'=>$channel->player, 'cid'=>$channel->id, 'platform'=>$channel->platform, 'ad'=>$channel->ad];
                $array['playurl'] = $channel->content;
                $array['h_playurl'] = $channel->h_content;
                $array['match'] = $match;
                return response()->json($array);
                break;
            default:
                return response()->json(array('code'=>0,'type'=>$channel->type,'player'=>$channel->player,'cid'=>$channel->id,'playurl'=>$channel->content,'match'=>$match, 'platform'=>$channel->platform, 'ad'=>$channel->ad ));
        }
    }


    /**
     * 获取天天直播 rtmp url
     * @param $key
     * @return mixed|null|string
     */
    private function ttzbLiveRTMPUrl($key){
        $key = explode('-',$key);
        if (count($key) > 2)
            $url = 'http://www.04stream.tv/player.html?ch='.$key[1].'&p=dn&v='.$key[2];
        else
            return null;
        return $url;
    }

    public static function isMobile(Request $request) {
        $userAgent = $request->header('user_agent', '');
        if ($userAgent) {
            $userAgent = $request->header('user_agent', '');
            if (preg_match("/(iPad).*OS\s([\d_]+)/", $userAgent)) {
                return true;
            }
            else if (preg_match("/(iPhone\sOS)\s([\d_]+)/", $userAgent)){
                return true;
            }
            else if (preg_match("/(Android)\s+([\d.]+)/", $userAgent)){
                return true;
            }
        }
        return false;
    }

    /**
     * 根据match id获取url
     * @param Request $request
     * @param $mid
     * @param $isMobile
     * @param $sport
     * @return mixed
     */
    public function getLiveUrlMatch(Request $request, $mid, $isMobile = false, $sport = 1){
        $use = $request->input('use',MatchLiveChannel::kUseLg310);
        $sport = $request->input('sport', $sport);
        $live = MatchLive::query()->where('match_id', $mid)
            ->where('sport',$sport)
            ->first();

        if (is_null($live)){
            $match = $this->getMatch($sport, $mid);
            return response()->json(array('code'=>-1,'message'=>'no channel','match'=>$match));
        } else{
            if ($isMobile || self::isMobile($request) || $request->input('isMobile',0)) {
                switch ($use){
                    case MatchLiveChannel::kUseLg310:
                        $channels = $live->mChannels();
                        break;
                    case MatchLiveChannel::kUseAiKQ:
                        $channels = $live->mAiKqChannels();
                        break;
                    default:
                        $channels = array();
                        break;
                }
                if (count($channels) == 0){
                    $match = $this->getMatch($sport, $mid);
                    return response()->json(array('code'=>-1,'message'=>'no channel','match'=>$match));
                }
                $channelId = $channels[0]['id'];
            }
            else{
                switch ($use){
                    case MatchLiveChannel::kUseLg310:
                        $channels = $live->kChannels();
                        break;
                    case MatchLiveChannel::kUseAiKQ:
                        $channels = $live->kAiKqChannels();
                        break;
                    default:
                        $channels = array();
                        break;
                }
                if (count($channels) == 0){
                    $match = $this->getMatch($sport, $mid);
                    return response()->json(array('code'=>-1,'message'=>'no channel','match'=>$match));
                }
                $channelId = $channels[0]['id'];
            }
        }
        return $this->getLiveUrl($request, $channelId);
//        return \Illuminate\Support\Facades\Response::json(array('code'=>0,'cid'=>$channelId,'type'=>$channels[0]['type'],'player'=>$channels[0]['player'],'link'=>$channels[0]['link']));
    }

    /**
     * 获取返回的比赛。
     * @param $sport
     * @param $mid
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    protected function getMatch($sport, $mid) {
        if ($sport == MatchLive::kSportFootball) {
            $match = Match::query()->find($mid);
        } else {
            $match = BasketMatch::query()->find($mid);
        }
        if (!isset($match)) return null;
        $show_live = false;
        if (!$show_live) {//比赛中
            $show_live = $match->status > 0;
        }
        if (!$show_live) {
            $matchTime = strtotime($match->time);
            if ($match->status == 0) {//未开始
                $show_live = (($matchTime - time()) / 60) <= 60;//赛前五分钟
            } else if ($match->status == -1) {//已结束
                if (isset($match->timehalf))
                    $time = strtotime($match->timehalf);
                else
                    $time = strtotime($match->time);
                if (isset($match->timehalf)) {
                    $show_live = ((time() - $time) / 60) <= (45 + 60);//赛后十分钟
                }
                else {
                    $show_live = ((time() - $time) / 60) <= (120 + 60);//赛后十分钟
                }
            }
        }
        $tmp = array('status'=>$match->status,'show_live'=>$show_live);
        $match = $tmp;
        return $match;
    }


    public static function matchDetailArray($id, $sport, $isMobile) {
        $request = new Request();
        $akqCon = new AikanQController();
        if ($sport == 1) {
            $jsonStr = $akqCon->detailJson($request, $id, $isMobile)->getData();
            $jsonStr = json_encode($jsonStr);
            $json = json_decode($jsonStr, true);
        } else if ($sport == 2) {
            $jsonStr = $akqCon->basketDetailJson($request, $id, $isMobile)->getData();
            $jsonStr = json_encode($jsonStr);
            $json = json_decode($jsonStr, true);
        } else {
            $jsonStr = $akqCon->otherDetailJson($request, $id, $isMobile)->getData();
            $jsonStr = json_encode($jsonStr);
            $json = json_decode($jsonStr, true);
        }
        return $json;
    }


    /**
     * 赛事转为数组
     * @param $match
     * @param $isMobile
     * @param $sport
     * @return array
     */
    public function match2Array($match, $isMobile, $sport) {
        $obj = ['hname'=>$match->hname, 'aname'=>$match->aname, 'sport'=>$sport ];
        $obj['league_name'] = $match->getLeagueName();
        $obj['lid'] = $match->lid;
        $obj['hid'] = $match->hid;
        $obj['aid'] = $match->aid;
        $obj['isMatching'] = ($match->status > 0);
        $obj['host_icon'] = $match->getTeamIcon(true);
        $obj['away_icon'] = $match->getTeamIcon(false);
        $obj['mid'] = $match['mid'];
        $obj['ascore'] = $match->ascore;
        $obj['hscore'] = $match->hscore;
        $obj['betting_num'] = $match->betting_num;
        $obj['time'] = $match->time;
        $obj['status'] = $match->status;
        $obj['genre'] = isset($match['genre'])?$match['genre']:0;

        //是否有专家推荐
        if ($isMobile){
            $live = MatchLive::query()->find($match['live_id']);
            $array = $this->getMobileChannels($live);
        }
        else{
            $array = MatchLive::query()->find($match['live_id'])->kAiKqChannels();
        }
        $obj['channels'] = $array;
        return $obj;
    }

    //==============================赛事积分排名============================================
    public static function getLeagueBySport($sport, $lid) {
        $league = null;
        if ($sport == MatchLive::kSportFootball) {
            $league = \App\Models\LgMatch\League::find($lid);
        } else if ($sport == MatchLive::kSportBasketball) {
            $league = \App\Models\LgMatch\BasketLeague::find($lid);
        }
        return $league;
    }

    public static function leagueRankData($sport, $lid) {
        $league = self::getLeagueBySport($sport, $lid);
        if ($league == null) return null;
        //联赛排名 开始
        if ($sport == MatchLive::kSportFootball) {//联赛才出
            if ($league->type == 1) {//联赛排名
                $ranks = Score::getFootballScores($lid);
                $rank_array = [];
                foreach ($ranks as $rank) {
                    $rank_array[] = ['tid'=>$rank->tid, 'lid'=>$lid, 'sport'=>$sport,
                        'name'=>$rank->tname, 'win'=>$rank->win, 'draw'=>$rank->draw, 'lose'=>$rank->lose
                        , 'score'=>$rank->score, 'rank'=>$rank->rank];
                }
                $ranks = $rank_array;//排名
            } else {//杯赛积分
                //获取最新的小组赛
                $ranks = Score::footballCupScores($lid);
            }
        } else {//篮球排名 暂时只有NBA / CBA
            if ($lid == 1) {//NBA 排名分东西部
                $west = BasketScore::getScoresByLid($lid, BasketScore::kZoneWest);
                $east = BasketScore::getScoresByLid($lid, BasketScore::kZoneEast);
                $ranks = ['west'=>$west, 'east'=>$east];
            } else {
                $ranks = BasketScore::getScoresByLid($lid);
            }
        }
        return $ranks;
    }

    public static function leagueRankStatic($sport, $lid) {
        $rankData = self::leagueRankData($sport, $lid);
        $staticData = self::getStaticLeagueRank($sport, $lid);
        //如果数据库返回的数据为空，而静态化的数据不为空，则不覆盖
        if ($rankData == null && $staticData != null) return;

        Storage::disk("public")->put("/static/json/pc/rank/$sport/$lid.json", json_encode($rankData));

        //同时把rank的html也静态化了
        $leagueData = self::getLeagueDataByLid($sport, $lid);
        $html = view('pc.team.detail_rank_cell', ['ranks'=>$rankData, 'subject'=>$leagueData]);
        Storage::disk("public")->put("/static/json/pc/rank/$sport/$lid.html", $html);
    }

    public static function getStaticLeagueRank($sport, $lid) {
        try {
            $data = Storage::get("/public/static/json/pc/rank/$sport/$lid.json");
            if (isset($data) && strlen($data) > 0) {
                return json_decode($data, true);
            }
        } catch (\Exception $e) {

        }
        return null;
    }

    /**
     * 根据球队id，获取联赛信息
     */
    public static function getLeagueDataByLid($sport, $lid) {
        $league = self::getLeagueBySport($sport, $lid);
        $leagueData = [];
        if (isset($league)) {
            $leagueData['lid'] = $lid;
            $leagueData['type'] = $league->type;
            $leagueData['sport'] = $sport;
            $leagueData['name'] = $league->name;
            $subject = SubjectLeague::getSubjectLeagueByLid($sport, $lid);
            if (isset($subject)) {
                $leagueData['name_en'] = $subject->name_en;
            }
        }
        return $leagueData;
    }

    //==============================球队终端==========================================
    /**
     * 根据球队id，获取联赛id
     */
    public static function getLeagueByTid($sport, $tid) {
        if ($sport == MatchLive::kSportBasketball) {
            $score = BasketScore::query()->join('basket_leagues as l', function ($join){
                $join->on('l.id', '=', 'basket_scores.lid')
                    ->where('l.type', '=', 1);
            })->where('tid', $tid)->orderBy('season', 'desc')->first();
        } else {
            $score = Score::query()->join('leagues as l', function ($join){
                $join->on('l.id', '=', 'scores.lid')
                    ->where('l.type', '=', 1);
            })->where('tid', $tid)->orderBy('season', 'desc')->first();
        }
//        dump($score);
        if (isset($score)) {
            return $score->lid;
        }
        return "";
    }
    /**
     * 球队终端页面数据
     */
    public static function teamDetailData($sport, $lid, $tid, $isMobile = false) {
        $teamData = self::teamData($sport, $tid);
        $result = [];
        if (!isset($teamData)) {
            return null;
        }

        $teamName = isset($teamData['shortName']) ? $teamData['shortName'] : $teamData['name'];
        if (!isset($lid) || strlen($lid) <= 0) {
            $lid = self::getLeagueByTid($sport, $tid);
        }

        $result['team'] = $teamData;
        $leagueData = self::getLeagueDataByLid($sport, $lid);
        $leagueName = "";
        if (count($leagueData) > 0) {
            $result['league'] = $leagueData;
            $leagueName = isset($leagueData['name']) ? $leagueData['name'] : "";
        }

        //专题资讯 开始
        $articles = PcArticle::liveRelationArticle([$teamName]);
        $article_array = [];
        foreach ($articles as $article) {
            $url = $article->url;
            if (is_null($url)){
                $url = $article->getUrl();
            }
            $article_array[] = ['title'=>$article->title, 'link'=>$url,'update_at'=>$article->publish_at, 'cover'=>$article->cover];
        }
        $result['articles'] = $article_array;
        //专题资讯 结束

        //联赛排名 开始
        $result['rank'] = self::getStaticLeagueRank($sport, $lid);
        //联赛排名 结束

        //球队直播 开始
        $result['lives'] = self::getTeamLives($sport, $lid, $tid, $leagueName, $teamName, $isMobile, 10);
        //球队直播 结束

        //球队录像 开始
        $result['videos'] = SubjectVideo::relationVideos($teamName, "");
        //球队录像 结束

        //球队集锦 开始
//        $result['specimens'] = SubjectSpecimen::getNewSpecimens($slid, $isMobile);
        //球队集锦 结束

        $result['title'] = "[".$teamName."]".$leagueName.$teamName."直播_".$teamName."赛程、球员阵容、新闻-爱看球直播";
        $result['h1'] = $teamData['name'].'直播';
        $result['ma_url'] = self::getMobileHttpUrl(CommonTool::getTeamDetailUrl($sport,  $lid, $tid));
        return $result;
    }

    /**
     * 球队终端数据
     */
    public static function teamData($sport, $tid) {
        //通过数据库请求
//        return self::getTeamDataByDB($sport, $tid);

        //通过接口请求
        $flag = 0;
        if (strlen($tid) > 2) {
            $flag = substr($tid, 0, 2);
        }
        $url = "http://match.liaogou168.com/static/team/$sport/$flag/$tid.json";
        $data = self::curlData($url);
        return $data;
    }

    private static function getTeamDataByDB($sport, $tid)
    {
        $result = array();
        if ($sport == MatchLive::kSportBasketball) {
            $result = self::basketTeamData($tid);
        } elseif ($sport == MatchLive::kSportFootball) {
            $result = self::footTeamData($tid);
        }
        return $result;
    }

    private static function footTeamData($tid)
    {
        $result = null;
        $team = Team::find($tid);
        if (isset($team)) {
            $result = [
                'id' => $team->id, 'name' => $team->name, 'icon' => Team::getIcon($team->icon), 'describe' => $team->describe,
                'city' => $team->city, 'establish' => $team->establish, 'gym' => $team->gym,
            ];
            $lineup = $team->lineup;
            if (isset($lineup) && strlen($lineup) > 0) {
                preg_match_all("/\\[(.*?)\\]/is", $lineup, $matches);
                if (isset($matches[1]) && count($matches[1]) > 0) {
                    $lineups = array();
                    foreach ($matches[1] as $str) {
                        list($pid, $num, $name, $nameBig, $status,
                            $birth, $height, $weight, $position, $nation,
                            $nationBig, $value, $contract, $firstCount, $firstGoal,
                            $secondCount, $secondGoal, $assist) = explode(",", $str, 18);

                        $lineups[] = [
                            'num' => trim($num), 'name' => trim($name), 'birth' => trim($birth), 'height' => trim($height),
                            'weight' => trim($weight), 'position' => trim($position), 'nation' => trim($nation),
                            'value' => trim($value), 'contract' => trim($contract), 'firstCount' => trim($firstCount),
                            'firstGoal' => trim($firstGoal), 'secondCount' => trim($secondCount),
                            'secondGoal' => trim($secondGoal), 'assist' => trim($assist)
                        ];
                    }
                    $result['lineup'] = $lineups;
                }
            }
        }
        return $result;
    }

    private static function basketTeamData($tid)
    {
        $result = null;
        $team = BasketTeam::find($tid);
        if (isset($team)) {
            $result = [
                'id' => $team->id, 'name' => $team->name_china, 'nameEn' => $team->name_en, 'shortName' => $team->name_china_short,
                'icon' => BasketTeam::getIcon($team->icon), 'describe' => $team->describe, 'link' => $team->link,
                'city' => $team->city, 'establish' => $team->establish, 'gym' => $team->gym,
            ];
            $lineup = $team->lineup;
            if (isset($lineup) && strlen($lineup) > 0) {
                preg_match_all("/\\[(.*?)\\]/is", $lineup, $matches);
                if (isset($matches[1]) && count($matches[1]) > 0) {
                    $lineups = array();
                    foreach ($matches[1] as $str) {
                        list($pid, $name, $nameBig, $nameEn, $shortName,
                            $shortNameBig, $shortNameEn, $num, $position, $birth,
                            $height, $weight, $careerAge, $contract, $value,
                            $nation, $nationBig) = explode(",", $str, 17);

                        $lineups[] = [
                            'num' => trim($num), 'name' => trim($name), 'shortName' => trim($shortName),
                            'birth' => trim($birth), 'height' => trim($height), 'weight' => trim($weight),
                            'position' => trim($position), 'nation' => trim(str_replace('[', '', $nation)),
                            'value' => trim($value), 'contract' => trim($contract), 'careerAge' => trim($careerAge)
                        ];
                    }
                    $result['lineup'] = $lineups;
                }
            }
        }
        return $result;
    }

    protected static function curlData($url,$timeout = 5){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }

    /**
     * 获取球队的比赛列表
     */
    protected static function getTeamLives($sport, $lid, $tid, $lname, $tname, $isMobile = false, $count = 15) {
        if (!in_array($sport, [MatchLive::kSportFootball, MatchLive::kSportBasketball])) {
            return [];
        }
        if ($sport == MatchLive::kSportFootball) {
            $query = Match::query();
            //$tableName = 'matches';
        } else {
            $query = BasketMatch::query();
            //$tableName = 'basket_matches';
        }
        if (isset($lid) && strlen($lid) > 0) {
            $query->where('lid', $lid);
        }
        $query->where(function ($q) use ($tid, $tname){
            $q->where('hid', $tid)
                ->orWhere('aid', $tid)
                ->orWhere('hname', $tname)
                ->orWhere('aname', $tname);
        });
        $query->selectRaw('*, id as mid');

        $tempQuery = clone $query;

        $query->where('time', '>=', date('Y-m-d H:i', strtotime('-3 hours')));
        $query->where('status', '>=', 0);
        $query->orderBy('time');
        $matches = $query->take($count)->get();

        //历史比赛
        $recentMatches = $tempQuery->where('status', '-1')
            ->take($count)->orderBy('time', 'desc')->get();

        $array = [];
        foreach ($recentMatches as $match) {
            $array['recent'][] = self::onMatchItemConvert($sport, $match, $lname, $isMobile);
        }
        foreach ($matches as $match) {
           $array['schedule'][] = self::onMatchItemConvert($sport, $match, $lname, $isMobile);
        }
        return $array;
    }

    private static function onMatchItemConvert($sport, $match, $lname, $isMobile) {
        $mid = $match->mid;

        $obj = ['time'=>strtotime($match->time), 'lid'=>$match->lid,
            'lname'=>isset($match->lname) ? $match->lname : (isset($match['win_lname']) ? $match['win_lname'] : $lname),
            'hname'=>$match->hname, 'aname'=>$match->aname, 'status'=>$match->status,
            'hid'=>$match->hid, 'aid'=>$match->aid,
            'hscore'=>$match->hscore, 'ascore'=>$match->ascore, 'mid'=>$mid, 'sport'=>$sport];

        $liveQuery = MatchLive::query()->where('match_id', $mid)->where('sport',$sport);
        $live = $liveQuery->first();
        $channels = [];
        if (isset($live)) {
            $channels = $isMobile ? $live->mAiKqChannels() : $live->kAiKqChannels();
        }
        $obj['channels'] = $channels;
        return $obj;
    }
}