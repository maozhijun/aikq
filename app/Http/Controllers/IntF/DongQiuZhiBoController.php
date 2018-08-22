<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 12:17
 */

namespace App\Http\Controllers\IntF;


use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class DongQiuZhiBoController extends Controller
{
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
    /**
     * 足球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function matchList(Request $request) {
        $result = $this->_matchList($request);
        return view('pc.spread.dongqiuzhibo',$result);
    }

    public function matchListJson(Request $request){
        $result = $this->_matchList($request);
        $json = array();
        //重新构造json文件
        foreach ($result['matches'] as $time=>$matches){
            foreach ($matches as $match){
                $tmp = array();
                $tmp['league'] = isset($match['league'])?$match['league']['name']:$match['win_lname'];
                $tmp['hname'] = $match['hname'];
                $tmp['aname'] = $match['aname'];
                $tmp['mid'] = $match['mid'];
                $tmp['sport'] = $match['sport'];
                $tmp['time'] = $match['time'];
                $tmp['channels'] = null;
                foreach ($match['channels'] as $channel){
                    if (is_array($channel)){
                        if ($channel['player'] == 13 && str_contains($channel['link'],'.m3u8')){
                            $tmp['channels'] = $channel['link'];
                            break;
                        }
                    }
                }
                $preUrl = 'http://www.aikq.cc';
                //$url = $preUrl.'/live/match_channel.html?sport='.$match['sport'].'&mid='.$match['mid'];
                $url = $preUrl . '/live/spPlayer/player-'.$match['mid'].'-'.$match['sport'].'.html';
                $tmp['url'] = $url;
                $json[] = $tmp;
            }
        }
        return response()->json($json);
    }

    //给外链用,上面的是player播放器用
    public function matchListUrl(Request $request) {
        $result = $this->_matchList($request);
        return view('bd.dongqiuzhibo_url',$result);
    }

    private function _matchList(Request $request){
        //20180330增加韩K联、日职联、澳洲甲、美职业、墨西联
        $flid = [8,11,26,29,31,46,73,77,139, 15,18,21,100,187];
        $blid = [1,4];
        $isMobile = $request->input('isMobile',0);
        $bet = $request->input('bet', 0);//0：非竞彩，1：竞彩
        //足球
        $match_array = [];
        $start = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $end = date('Y-m-d H:i:s', strtotime('+2 days'));
        $query = Match::query();
//        $query->leftJoin('match_lives', 'matches.id', '=', 'match_lives.match_id');
        $query->leftJoin('match_lives', function ($join) {
            $join->on('match_lives.match_id', '=', 'matches.id');
            $join->where('match_lives.sport', MatchLive::kSportFootball);
        });
        $query->where('matches.status', '<>', -1);//已完结的赛事不显示
//        $query->where('match_lives.sport', MatchLive::kSportFootball);
        $query->whereBetween('matches.time', [$start, $end]);
        if ($bet == 1) {
            $query->whereNotNull('matches.betting_num');
        }
        $query->select("matches.*");
        $query->addSelect("matches.id as mid");
        $query->addSelect("matches.lid as lid");
        $query->addSelect("match_lives.id as live_id");
        $query->orderby('time','asc');
        $footballMatches = $query->get();
        $tmp = array();
        foreach ($footballMatches as $match){
            $match['sport'] = 1;
            $match['league_name'] = $match->getLeagueName();
            $match['isMatching'] = ($match->status > 0 && $match->status <= 4);
            $match['host_icon'] = $match->getTeamIcon(true);
            $match['away_icon'] = $match->getTeamIcon(false);
            if (in_array($match['lid'],$flid)){
                if ($match['live_id'] > 0) {
                    $array = MatchLive::query()->find($match['live_id'])->mChannels();
                }
                else{
                    $array = array();
                }
                if (count($array) == 0){
                    $match['channels'] = array($match->lid);
                }
                else{
                    $match['channels'] = $array;
                }
                $tmp[] = $match;
                continue;
            }
            $array = array();
            if ($isMobile){
                $array = MatchLive::query()->find($match['live_id'])->mChannels();
            }
            else{
                if ($match['live_id'] > 0)
                    $array = MatchLive::query()->find($match['live_id'])->dbChannels();
            }
            $match['channels'] = $array;
            $hasCommon = false;
            foreach ($array as $item){
                $hasWap = false;
                if ($item['platform'] == MatchLiveChannel::kPlatformAll){
                    $hasCommon = true;
                    break;
                }
                else {
                    if ($item['platform'] == MatchLiveChannel::kPlatformWAP){
                        $hasWap = true;
                    }
                }
                if ($hasWap){
                    $hasCommon = true;
                    break;
                }
            }
            if ($hasCommon){
                $tmp[] = $match;
            }
        }
        $footballMatches = $tmp;
//        $footballMatches = $footballMatches->toArray();

        //篮球
        $start = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $end = date('Y-m-d H:i:s', strtotime('+2 days'));
        $matchTable = 'basket_matches';
        $query = BasketMatch::query();
        $query->leftJoin('match_lives', function ($join) {
            $join->on('match_lives.match_id', '=', 'basket_matches.id');
            $join->where('match_lives.sport', MatchLive::kSportBasketball);
        });
        $query->where($matchTable.'.status', '<>', -1);//已完结的赛事不显示
        $query->whereBetween($matchTable.'.time', [$start, $end]);
        if ($bet == 1) {
            $query->whereNotNull('basket_matches.betting_num');
        }
        $query->select($matchTable.".*");
        $query->addSelect($matchTable.".id as mid");
        $query->addSelect($matchTable.".lid as lid");
        $query->addSelect("match_lives.id as live_id");
        $query->orderby('time','asc');
        $basketballMatches = $query->get();
        $tmp = array();
        foreach ($basketballMatches as $match){
            $match['sport'] = 2;
            $match['league_name'] = $match->getLeagueName();
            $match['isMatching'] = ($match->status > 0);
            $match['host_icon'] = $match->getTeamIcon(true);
            $match['away_icon'] = $match->getTeamIcon(false);
            if (in_array($match['lid'],$blid)){
                if ($match['live_id'] > 0) {
                    $array = MatchLive::query()->find($match['live_id'])->mChannels();
                }
                else{
                    $array = array();
                }
                if (count($array) == 0){
                    $match['channels'] = array($match->lid);
                }
                else{
                    $match['channels'] = $array;
                }
                $tmp[] = $match;
                continue;
            }
            $array = array();
            if ($isMobile){
                $array = MatchLive::query()->find($match['live_id'])->mChannels();
            }
            else{
                if ($match['live_id'] > 0)
                    $array = MatchLive::query()->find($match['live_id'])->kChannels();
            }
            $match['channels'] = $array;
            $hasCommon = false;
            foreach ($array as $item){
                $hasWap = false;
                if ($item['platform'] == MatchLiveChannel::kPlatformAll){
                    $hasCommon = true;
                    break;
                }
                else {
                    if ($item['platform'] == MatchLiveChannel::kPlatformWAP){
                        $hasWap = true;
                    }
                }
                if ($hasWap){
                    $hasCommon = true;
                    break;
                }
            }
            if ($hasCommon){
                $tmp[] = $match;
            }
        }
        $basketballMatches = $tmp;

        $matches = array_merge($footballMatches,$basketballMatches);
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
        return $result;
    }

    /*************** qiushengke接口 ********************/
    /**
     * 足球比赛列表接口
     * @param Request $request
     * @return mixed
     */
    public function matchList2(Request $request) {
        //20180330增加韩K联、日职联、澳洲甲、美职业、墨西联
        $flid = [8,11,26,29,31,46,73,77,139, 15,18,21,100,187];
        $blid = [1,4];
        $isMobile = $request->input('isMobile',0);
        $bet = $request->input('bet', 0);//0：非竞彩，1：竞彩
        //足球
        $match_array = [];
        $start = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $end = date('Y-m-d H:i:s', strtotime('+2 days'));
        $query = Match::query();
//        $query->leftJoin('match_lives', 'matches.id', '=', 'match_lives.match_id');
        $query->leftJoin('match_lives', function ($join) {
            $join->on('match_lives.match_id', '=', 'matches.id');
            $join->where('match_lives.sport', MatchLive::kSportFootball);
        });
        $query->where('matches.status', '<>', -1);//已完结的赛事不显示
//        $query->where('match_lives.sport', MatchLive::kSportFootball);
        $query->whereBetween('matches.time', [$start, $end]);
        if ($bet == 1) {
            $query->whereNotNull('matches.betting_num');
        }
        $query->select("matches.*");
        $query->addSelect("matches.id as mid");
        $query->addSelect("matches.lid as lid");
        $query->addSelect("match_lives.id as live_id");
        $query->orderby('time','asc');
        $footballMatches = $query->get();
        $tmp = array();
        foreach ($footballMatches as $match){
            $match['sport'] = 1;
            $match['mid'] = $match->id;
//            $match['league_name'] = $match->getLeagueName();
            $match['isMatching'] = ($match->status > 0 && $match->status <= 4);
//            $match['host_icon'] = $match->getTeamIcon(true);
//            $match['away_icon'] = $match->getTeamIcon(false);
            if (in_array($match['lid'],$flid)){
                $match['channels'] = array($match->lid);
                $tmp[] = $match;
                continue;
            }
            $array = array();
            if ($isMobile){
                $array = MatchLive::find($match['live_id'])->mChannels();
            }
            else{
                if ($match['live_id'] > 0)
                    $array = MatchLive::find($match['live_id'])->dbChannels();
            }
            $match['channels'] = $array;
            $hasCommon = false;
            foreach ($array as $item){
                $hasWap = false;
                if ($item['platform'] == MatchLiveChannel::kPlatformAll){
                    $hasCommon = true;
                    break;
                }
                else {
                    if ($item['platform'] == MatchLiveChannel::kPlatformWAP){
                        $hasWap = true;
                    }
                }
                if ($hasWap){
                    $hasCommon = true;
                    break;
                }
            }
            if ($hasCommon){
                $tmp[] = $match;
            }
        }
        $footballMatches = $tmp;
//        $footballMatches = $footballMatches->toArray();

        //篮球
        $start = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $end = date('Y-m-d H:i:s', strtotime('+2 days'));
        $matchTable = 'basket_matches';
        $query = BasketMatch::query();
        $query->leftJoin('match_lives', function ($join) {
            $join->on('match_lives.match_id', '=', 'basket_matches.id');
            $join->where('match_lives.sport', MatchLive::kSportBasketball);
        });
        $query->where($matchTable.'.status', '<>', -1);//已完结的赛事不显示
        $query->whereBetween($matchTable.'.time', [$start, $end]);
        if ($bet == 1) {
            $query->whereNotNull('basket_matches.betting_num');
        }
        $query->select($matchTable.".*");
        $query->addSelect($matchTable.".id as mid");
        $query->addSelect($matchTable.".lid as lid");
        $query->addSelect("match_lives.id as live_id");
        $query->orderby('time','asc');
        $basketballMatches = $query->get();
        $tmp = array();
        foreach ($basketballMatches as $match){
            $match['sport'] = 2;
            $match['mid'] = $match->id;
            $match['isMatching'] = ($match->status > 0);
            if (in_array($match['lid'],$blid)){
                $match['channels'] = array($match->lid);
                $tmp[] = $match;
                continue;
            }
            $array = array();
            if ($isMobile){
                $array = MatchLive::query()->find($match['live_id'])->mChannels();
            } else{
                if ($match['live_id'] > 0) {
                    $array = MatchLive::query()->find($match['live_id'])->dbChannels();
                }
            }
            $match['channels'] = $array;
            $hasCommon = false;
            foreach ($array as $item){
                $hasWap = false;
                if ($item['platform'] == MatchLiveChannel::kPlatformAll){
                    $hasCommon = true;
                    break;
                }
                else {
                    if ($item['platform'] == MatchLiveChannel::kPlatformWAP){
                        $hasWap = true;
                    }
                }
                if ($hasWap){
                    $hasCommon = true;
                    break;
                }
            }
            if ($hasCommon){
                $tmp[] = $match;
            }
        }
        $basketballMatches = $tmp;

        $matches = array_merge($footballMatches,$basketballMatches);
        usort($matches, array($this,"usortTime"));

        foreach ($matches as $match) {
            if (count($match['channels']) == 0)
                continue;
            $match['channel_url'] = 'http://www.aikq.cc/#/live/spPlayer/player-'.$match['mid'].'-'.$match['sport'].'.html';
            $time = date('Y-m-d', strtotime($match['time']));
            if (isset($match_array[$time])) {
                $match_array[$time][] = $match;
            } else {
                $match_array[$time] = [$match];
            }
        }
        $result = ['matches'=>$match_array];
        return response()->json(array('code'=>0,'data'=>$result));
    }


    public function staticMatchList(Request $request){
        echo '更新推广视频链接ing'.'</br>';
        $result = $this->_matchList($request);
        $html = view('bd.dongqiuzhibo',$result);
        $html2 = view('bd.dongqiuzhibo_url',$result);

        try {
            Storage::disk("public")->put("/spread/api/matchList.html", $html);
            Storage::disk("public")->put("/spread/api/matchListUrl.html", $html2);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        $json = array();
        //重新构造json文件
        foreach ($result['matches'] as $time=>$matches){
            foreach ($matches as $match){
                $tmp = array();
                $tmp['league'] = isset($match['league'])?$match['league']['name']:$match['win_lname'];
                $tmp['hname'] = $match['hname'];
                $tmp['aname'] = $match['aname'];
                $tmp['mid'] = $match['mid'];
                $tmp['sport'] = $match['sport'];
                $tmp['time'] = $match['time'];
                $tmp['channels'] = null;
                foreach ($match['channels'] as $channel){
                    if (is_array($channel)){
                        if ($channel['player'] == 13 && str_contains($channel['link'],'.m3u8')){
                            $tmp['channels'] = $channel['link'];
                            break;
                        }
                    }
                }
                $preUrl = 'http://www.aikq.cc';
                $url = $preUrl.'/live/match_channel.html?sport='.$match['sport'].'&mid='.$match['mid'];
                $tmp['url'] = $url;
                $json[] = $tmp;
            }
        }
        try {
            Storage::disk("public")->put("/spread/api/matchList.json", json_encode($json));
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        echo '更新完毕';
    }


    public function deleteMatchList(Request $request){
        try {
            Storage::disk("public")->delete("/spread/api/matchList.json");
            Storage::disk("public")->delete("/spread/api/matchList.html");
            Storage::disk("public")->delete("/spread/api/matchListUrl.html");
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

}