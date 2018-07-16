<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/12/14
 * Time: 下午4:33
 */
namespace App\Http\Controllers\IntF;


use App\Models\LgMatch\MatchData;
use App\Models\LgMatch\MatchEvent;
use App\Models\Match\OtherMatch;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\AdConf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class KanQiuMaController extends Controller
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

    /**
     * 足球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function livesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);
        $bet = $request->input('bet', 0);//0：非竞彩，1：竞彩
        //足球
        $footballMatches = $this->getMatchQuery(MatchLive::kSportFootball)->get();
        $footballArray = [];
        foreach ($footballMatches as $match){
            $footballArray[] = $this->match2Array($match, $isMobile, MatchLive::kSportFootball);
        }
        //$footballMatches = $footballMatches->toArray();

        //篮球
        $basketballMatches = $this->getMatchQuery(MatchLive::kSportBasketball)->get();
        $basketballArray = [];
        foreach ($basketballMatches as $match){
            $basketballArray[] = $this->match2Array($match, $isMobile, MatchLive::kSportBasketball);
        }
        //$basketballMatches = $basketballMatches->toArray();

        //自建赛事
        $otherMatches = $this->getMatchQuery(MatchLive::kSportSelfMatch)->get();
        foreach ($otherMatches as $match){
            $match['sport'] = MatchLive::kSportSelfMatch;
            $match['league_name'] = $match->getLeagueName();
            $match['isMatching'] = (time() >= strtotime($match->time));
            $match['host_icon'] = '';
            $match['away_icon'] = '';
            $match['betting_num'] = '';
            if ($isMobile){
                $array = MatchLive::query()->find($match['live_id'])->mChannels();
            } else{
                $array = MatchLive::query()->find($match['live_id'])->kChannels();
            }
            $match['channels'] = $array;
        }
        $otherMatches = $otherMatches->toArray();

        $matches = array_merge($footballArray, $basketballArray);
        $matches = array_merge($matches, $otherMatches);

        usort($matches, array($this,"usortTime"));

        foreach ($matches as $match) {
            if (count($match['channels']) == 0)
                continue;
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($match_array[$time])) {
                $match_array[$time][] = $match;
            } else {
                $match_array[$time] = [$match];
            }
        }
        $result = ['matches'=>$match_array];
        return Response::json($result);
    }

    public function getMatchQuery($sport, $bet = '') {
        $end = date('Y-m-d H:i:s', strtotime('+5 days'));
        if ($sport == MatchLive::kSportBasketball) {
            //篮球
            $start = date('Y-m-d H:i:s', strtotime('-3 hours'));
            $matchTable = 'basket_matches';
            $query = BasketMatch::query();
            $query->join('match_lives', $matchTable.'.id', '=', 'match_lives.match_id');
            $query->where($matchTable.'.status', '<>', -1);//已完结的赛事不显示
            $query->where('match_lives.sport', MatchLive::kSportBasketball);
            $query->whereBetween($matchTable.'.time', [$start, $end]);
            if ($bet == 1) {
                $query->whereNotNull('basket_matches.betting_num');
            }
            $query->select($matchTable.".*");
            $query->addSelect($matchTable.".id as mid");
            $query->addSelect("match_lives.id as live_id");
            $query->orderby('time','asc');
            return $query;
        } else if ($sport == MatchLive::kSportFootball) {
            $start = date('Y-m-d H:i:s', strtotime('-2 hours'));
            $query = Match::query();
            $query->join('match_lives', 'matches.id', '=', 'match_lives.match_id');
            $query->where('matches.status', '<>', -1);//已完结的赛事不显示
            $query->where('match_lives.sport', MatchLive::kSportFootball);
            $query->whereBetween('matches.time', [$start, $end]);
            if ($bet == 1) {
                $query->whereNotNull('matches.betting_num');
            }
            $query->select("matches.*");
            $query->addSelect("matches.id as mid");
            $query->addSelect("match_lives.id as live_id");
            $query->orderby('time', 'asc');
            return $query;
        } else if ($sport == MatchLive::kSportSelfMatch) {
            //获取未开始 或者 未结束的赛事
            $start = date('Y-m-d H:i:s', strtotime('-10 hours'));

            $query = OtherMatch::query();
            $query->join('match_lives', 'other_matches.id', '=', 'match_lives.match_id');
            $query->where('match_lives.sport', MatchLive::kSportSelfMatch);
            $query->whereBetween('other_matches.time', [$start, $end]);//未开始的赛事
            $query->where('other_matches.end_time', '>=', date('Y-m-d H:i'));//未结束
            $query->select("other_matches.*");
            $query->addSelect("other_matches.id as mid");
            $query->addSelect("match_lives.id as live_id");
            $query->orderby('other_matches.time','asc');
            return $query;
        }
        return null;
    }

    /**
     * 赛事转为数组
     * @param $match
     * @param $isMobile
     * @param $sport
     * @return array
     */
    public static function match2Array($match, $isMobile, $sport) {
        $obj = ['hname'=>$match->hname, 'aname'=>$match->aname, 'hscore'=>$match->hscore];
        $obj['mid'] = $match['mid'];
        $obj['ascore'] = $match->ascore;
        $obj['sport'] = $sport;
        $obj['betting_num'] = $match->betting_num;
        $obj['time'] = $match->time;
        $obj['status'] = $match->status;
        $obj['league_name'] = $match->getLeagueName();
        $obj['isMatching'] = ($match->status > 0 && $match->status <= 4);
        $obj['host_icon'] = $match->getTeamIcon(true);
        $obj['away_icon'] = $match->getTeamIcon(false);
        if ($isMobile){
            $array = MatchLive::query()->find($match['live_id'])->mChannels();
        } else{
            $array = MatchLive::query()->find($match['live_id'])->kChannels();
        }
        $obj['channels'] = $array;
        return $obj;
    }

    /**
     * 篮球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function basketballLivesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);
        $match_array = [];

        $basketballMatches = $this->getMatchQuery(MatchLive::kSportBasketball)->get();
        foreach ($basketballMatches as $match){
            $match_array[] = $this->match2Array($match, $isMobile, MatchLive::kSportBasketball);
        }

        $matches = [];
        foreach ($match_array as $match) {
            if (count($match['channels']) == 0)
                continue;
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($matches[$time])) {
                $matches[$time][] = $match;
            } else {
                $matches[$time] = [$match];
            }
        }
        $result = ['matches'=>$matches];
        return response()->json($result);
    }

    /**
     * 足球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function footballLivesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);
        $match_array = [];

        $footballMatches = $this->getMatchQuery(MatchLive::kSportFootball)->get();
        foreach ($footballMatches as $match){
            $match_array[] = $this->match2Array($match, $isMobile, MatchLive::kSportFootball);
        }

        $matches = [];
        foreach ($match_array as $match) {
            if (count($match['channels']) == 0)
                continue;
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($matches[$time])) {
                $matches[$time][] = $match;
            } else {
                $matches[$time] = [$match];
            }
        }
        $result = ['matches'=>$matches];
        return Response::json($result);
    }

    /**
     * 自建赛事列表
     * @param Request $request
     * @return mixed
     */
    public function otherLivesJson(Request $request) {
        $isMobile = $request->input('isMobile',0);
        $match_array = [];

        //自建赛事
        $otherMatches = $this->getMatchQuery(MatchLive::kSportSelfMatch)->get();
        foreach ($otherMatches as $match){
            $match['sport'] = MatchLive::kSportSelfMatch;
            $match['league_name'] = $match->getLeagueName();
            $match['isMatching'] = (time() >= strtotime($match->time));
            $match['host_icon'] = '';
            $match['away_icon'] = '';
            $match['betting_num'] = '';
            if ($isMobile){
                $array = MatchLive::query()->find($match['live_id'])->mChannels();
            }
            else{
                $array = MatchLive::query()->find($match['live_id'])->kChannels();
            }
            $match['channels'] = $array;
        }
        $otherMatches = $otherMatches->toArray();

        $matches = [];
        foreach ($otherMatches as $match) {
            if (count($match['channels']) == 0)
                continue;
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($matches[$time])) {
                $matches[$time][] = $match;
            } else {
                $matches[$time] = [$match];
            }
        }

        $result = ['matches'=>$matches];
        return Response::json($result);
    }

    /**
     * 篮球直播终端接口
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function basketDetailJson(Request $request,$id){
        $mobile = $request->input('isMobile',0);
        $match = BasketMatch::query()->select('basket_matches.*',"basket_matches.id as mid")->find($id);
        if (!isset($match)) {
            return Response::json(array('code'=>-1));
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
            if ($mobile)
                $live['channels'] = $live->mChannels();
            else
                $live['channels'] = $live->kChannels();
        }
        $result['live'] = $live;
        $result['host_icon'] = $match->getTeamIcon(true);
        $result['away_icon'] = $match->getTeamIcon(false);
        $result['show_live'] = $show_live;
        $result['data'] = array();
        return Response::json($result);
    }

    /**
     * 自建赛事直播终端接口
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function selfDetailJson(Request $request, $id){
        $mobile = $request->input('isMobile',0);
        $match = OtherMatch::query()->select('other_matches.*', "other_matches.id as mid")->find($id);
        if (!isset($match)) {
            return Response::json(array('code'=>-1));
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
            //$show_live = (($matchTime - time()) / 60) <= 60 && ($end_time >= time() - 30 * 60);
            //赛前60分钟/赛后30分钟
        }

        $match['sport'] = MatchLive::kSportSelfMatch;
        $result['match'] = $match;
        if (isset($live)) {
            if ($mobile)
                $live['channels'] = $live->mChannels();
            else
                $live['channels'] = $live->kChannels();
        }
        $result['live'] = $live;
        $result['host_icon'] = '';
        $result['away_icon'] = '';
        $result['show_live'] = $show_live;
        $result['data'] = array();
        return Response::json($result);
    }

    /**
     * 直播终端接口
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function detailJson(Request $request,$id){
        $mobile = $request->input('isMobile',0);
        $match = Match::query()->select('matches.*',"matches.id as mid")->find($id);
        if (!isset($match)) {
            return Response::json(array('code'=>-1));
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
                $live['channels'] = $live->mChannels();
            } else {
                $live['channels'] = $live->kChannels();
            }
        }
        $result['live'] = $live;
        $result['host_icon'] = $match->getTeamIcon(true);
        $result['away_icon'] = $match->getTeamIcon(false);
        $result['show_live'] = $show_live;
        $events = MatchEvent::query()->where('mid', $id)->orderBy('happen_time')->get();
        $data = MatchData::query()->find($id);

        if (isset($data)) {
            $data['hYellowPercent'] = $data->yellowPercent(true);
            $data['aYellowPercent'] = $data->yellowPercent(false);
            $data['hRedPercent'] = $data->redPercent(true);
            $data['aRedPercent'] = $data->redPercent(false);
            $data['hCornerPercent'] = $data->cornerPercent(true);
            $data['aCornerPercent'] = $data->cornerPercent(false);
            $data['hShootPercent'] = $data->shootPercent(true);
            $data['aShootPercent'] = $data->shootPercent(false);
            $data['hShootInTargetPercent'] = $data->shootInTargetPercent(true);
            $data['aShootInTargetPercent'] = $data->shootInTargetPercent(false);
            $data['hShootPercent'] = $data->hShootPercent();
            $data['aShootPercent'] = $data->aShootPercent();
            $data['hShootInTarget'] = $data->hShootInTarget();
            $data['aShootInTarget'] = $data->aShootInTarget();
            $data['hAttPercent'] = $data->hAttPercent();
            $data['aAttPercent'] = $data->aAttPercent();
            $data['hDangerAttPercent'] = $data->hDangerAttPercent();
            $data['aDangerAttPercent'] = $data->aDangerAttPercent();
        }
        else{
            $data = array();
        }
        $match['getMatchTimeMin'] = $match->getMatchTimeMin();
        $result['match'] = $match;
        $result['live'] = $live;
        $result['events'] = $events;
        $result['data'] = $data;
        $result['show_live'] = $show_live;
        $result['last_event_time'] = count($events) > 0 ? ($events[count($events) - 1]->happen_time) : 0;//最后的事件时间

        return Response::json($result);
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

    /**
     * 多视频页面
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiLive(Request $request, $param) {
        if (!preg_match('/^\d+(-\d+){0,3}$/', $param)) {
//            return Response::json(array('code'=>-1));
        }
        $id_array = explode('-', $param);
        $match_array = [];
        foreach ($id_array as $id) {
            if (stristr($id,'f')) {
                $id = substr($id,1);
                //足球
                $live = MatchLive::query()->where('match_id', $id)->where('sport', MatchLive::kSportFootball)->first();
                if (!isset($live)) {
                    continue;
                }
                $match = Match::query()
                    ->select('matches.*')
                    ->addSelect('matches.id as mid')
                    ->find($id);
                $match['sport'] = 1;
            }
            else if (stristr($id,'b')) {
                $id = substr($id,1);
                //篮球
                $live = MatchLive::query()->where('match_id', $id)->where('sport', MatchLive::kSportBasketball)->first();
                if (!isset($live)) {
                    continue;
                }
                $match = BasketMatch::query()
                    ->select('basket_matches.*')
                    ->addSelect('basket_matches.id as mid')
                    ->find($id);
                $match['sport'] = 2;
            }
            $live['channels'] = $live->kChannels();
            $match_array[] = ['match'=>$match, 'live'=>$live];
        }
        $result['matches'] = $match_array;
        $result['channel_cn'] = ['线路一', '线路二', '线路三'];
        $result['param'] = $param;
        return response()->json($result);
    }

    /**
     * 获取多视频页面的video div 代码
     * @param Request $request
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiVideoDiv(Request $request, $mid) {
        if (stristr($mid,'f')) {
            $mid = substr($mid,1);
            $match = Match::query()
                ->select('matches.*')
                ->addSelect('matches.id as mid')
                ->find($mid);
            $live = MatchLive::query()->where('match_id', $mid)->where('sport', MatchLive::kSportFootball)->first();
        }
        else if (stristr($mid,'b')) {
            $mid = substr($mid,1);
            $match = BasketMatch::query()
                ->select('basket_matches.*')
                ->addSelect('basket_matches.id as mid')
                ->find($mid);
            $live = MatchLive::query()->where('match_id', $mid)->where('sport', MatchLive::kSportBasketball)->first();
        }

        if (!isset($match) && !isset($live)) {
            return Response::json(array('code'=>-1));
        }
        $class = $request->input('class');
        if (strpos($class, 'bottom') !== false) {
            $class = 'bottom';
        } else {
            $class = 'top';
        }
        $match['sport'] = 1;
        $result['match'] = $match;
        $result['channels'] = $live->kChannels();
        $result['channel_cn'] = ["线路一", "线路二", "线路三", "线路四", "线路五", "线路六"];
        $result['class'] = $class;
        return Response::json($result);
    }

    /**
     * 多视频页面
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiBasketLive(Request $request, $param) {
        if (!preg_match('/^\d+(-\d+){0,3}$/', $param)) {
//            return Response::json(array('code'=>-1));
        }
        $id_array = explode('-', $param);
        $match_array = [];
        foreach ($id_array as $id) {
            $live = MatchLive::query()->where('match_id', $id)->where('sport', MatchLive::kSportBasketball)->first();
            if (!isset($live)) {
                continue;
            }
            $match = BasketMatch::query()
                ->select('matches.*')
                ->addSelect('matches.id as mid')
                ->find($id);

            $live['channels'] = $live->kChannels();

            $match['sport'] = 1;
            $match_array[] = ['match'=>$match, 'live'=>$live];
        }
        $result['matches'] = $match_array;
        $result['channel_cn'] = ['线路一', '线路二', '线路三'];
        $result['param'] = $param;
        return Response::json($result);
    }

    /**
     * 获取多视频页面的video div 代码
     * @param Request $request
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multiBasketVideoDiv(Request $request, $mid) {
        $match = BasketMatch::query()
            ->select('matches.*')
            ->addSelect('matches.id as mid')
            ->find($mid);
        $live = MatchLive::query()->where('match_id', $mid)->where('sport', MatchLive::kSportBasketball)->first();
        if (!isset($match) && !isset($live)) {
            return Response::json(array('code'=>-1));
        }
        $class = $request->input('class');
        if (strpos($class, 'bottom') !== false) {
            $class = 'bottom';
        } else {
            $class = 'top';
        }
        $match['sport'] = 1;
        $result['match'] = $match;
        $result['channels'] = $live->kChannels();
        $result['channel_cn'] = ["线路一", "线路二", "线路三"];
        $result['class'] = $class;
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
        return Response::json($result);
    }

    /**
     * 获取高清验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveCode(Request $request) {
        $code = AdConf::getValue(AdConf::CMS_SHD_CHANNEL_CODE_KEY);
        if (is_null($code)) {
            return response()->json(['code'=>-1 ,"msg"=>'未设置验证码']);
        }
        return response()->json(['code'=>200 ,"r_code"=>$code]);
    }

}