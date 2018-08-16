<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/11/29
 * Time: 下午3:16
 */

namespace App\Http\Controllers\IntF;

use App\Models\LgMatch\LiaogouAlias;
use App\Models\LgMatch\LiveAlias;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use QL\QueryList;

class SpiderTTZBController extends Controller{

    const basketball_private_array = [1, 3, 4, 5];//NBA, NBA明星赛, CBA, CBA明星赛
    const football_private_array = [8,11,26,29,31,46,73,77, 139];//足球 英超、意甲、德甲、法甲、西甲、欧冠、欧联、中超、亚冠

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderTTZBController->$action()'";
        }
    }

    /**
     * 爬直播
     */
    public function spiderLiveData() {
        $ql = QueryList::get('http://www.tiantianzhibo.com/zuqiuzhibo/');
        $divs = $ql->find('div.listcontent')->eq(0);

        $datas = $divs->children()->map(function ($item){
            //用is判断节点类型
            if ($item->is('div')) {
                //切割时间 2017年12月12日
                $timeStr = $item->text();
                $time = date_format(date_create_from_format('Y年m月d日',explode(' ',$timeStr)[0]),'Y-m-d');
                return array('type'=>'div','data'=>$time);
            } elseif ($item->is('ul')) {
                //切割时间 主客队
                return array('type'=>'ul','data'=>$item);
            }
        });

        $timeStr = 'error';
        $result = array();
        foreach ($datas as $data){
            if ($data['type'] == 'div'){
                $timeStr = $data['data'];
                $result[$timeStr] = array();
            }
            if ($data['type'] == 'ul'){
                $result[$timeStr][] = $data['data'];
            }
        }
//        dump($result);
        $countNotMatch = 0;

        foreach ($result as $key=>$uls){
            foreach ($uls as $ul){
                $timeStr = $ul->find('li.t1')->eq(0)->text();
                $teamStr = $ul->find('li.t4')->eq(0)->text();
                $leagueStr = $ul->find('li.t3')->eq(0)->text();
                $liveStr = $ul->find('li.t5 a')->eq(0)->href;
                $liveStr = explode('/',$liveStr);
                $liveStr = $liveStr[count($liveStr) - 1];
                $liveStr = explode('.',$liveStr)[0];
//                $liveStr = explode('-',$liveStr);
//                if (count($liveStr) == 3){
//                    $liveStr = $liveStr[1];
//                }
//                else{
//                    $liveStr = '';
//                }

                if (strstr($liveStr,'ttzb')){

                }else{
                    $liveStr = '';
                }

                if (strlen($timeStr) > 0 && strlen($teamStr) > 0 && strlen($liveStr) > 0){
                    $timeStr = $key .' '. $timeStr;

                    if (!self::isCanAddToAfter($timeStr)){
                        break;
                    }

                    $teamStr = explode(' VS ',$teamStr);
                    if (count($teamStr) == 2) {
                        $host = $teamStr[0];
                        $away = $teamStr[1];
                        $match = Match::query()->where('time', $timeStr)
                            ->where(function ($q) use($host,$away){
                                $q->where(function ($q2) use($host, $away){
                                    $q2->where('hname','=',$host)
                                        ->orwhere('aname','=',$away);
                                })->orwhere(function ($q2) use($host, $away){
                                    $q2->where('win_hname','=',$host)
                                        ->orwhere('win_aname','=',$away);
                                });
                            })
                            ->first();
                        //别名搜一次
                        if (is_null($match)){
                            $team = LiaogouAlias::getAliasByName($host,1,LiaogouAlias::kFromTTZB);
                            $hid = 0;
                            if (isset($team)){
                                $hid = $team->lg_id;
                            }
                            $team = LiaogouAlias::getAliasByName($away,1,LiaogouAlias::kFromTTZB);
                            $aid = 0;
                            if (isset($team)){
                                $aid = $team->lg_id;
                            }
                            if ($hid > 0 || $aid > 0) {
                                $query = Match::query()->where('time', $timeStr);
                                if ($hid > 0)
                                    $query->where('hid', $hid);
                                if ($aid > 0)
                                    $query->where('aid', $aid);
                                $match = $query->first();
                            }
                        }

                        if (isset($match)){
                            $matchLive = MatchLive::query()->where('match_id',$match->id)->first();
                            if (is_null($matchLive)){
                                $matchLive = new MatchLive();
                                $matchLive->match_id = $match->id;
                                $matchLive->sport = 1;
                            }
                            $matchLive->save();
                            $isPrivate = in_array($match->lid, self::football_private_array);
                            $show = $isPrivate ? MatchLiveChannel::kHide : MatchLiveChannel::kShow;
                            $private = MatchLiveChannel::kNotPrivate;//$isPrivate ? MatchLiveChannel::kPrivate : MatchLiveChannel::kNotPrivate;
                            MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportFootball,MatchLiveChannel::kTypeTTZB,$liveStr,12,MatchLiveChannel::kPlatformPC,MatchLiveChannel::kPlayerIFrame,'赛事直播', $show, $private, MatchLiveChannel::kUseAiKQ);
                            echo 'match ' . $host . ' vs ' .$away . ' 频道 '.$liveStr.'</br>';
                        }
                        else{
                            $alise = LiveAlias::getAliasByName($host,LiveAlias::kTypeTeam,LiveAlias::kFromTTZB);
                            if (is_null($alise)){
                                $alise = LiaogouAlias::query()->where('from',LiaogouAlias::kFromTTZB)
                                    ->where('type',LiaogouAlias::kTypeTeam)
                                    ->where('sport',LiaogouAlias::kSportTypeFootball)
                                    ->where('target_name',$host)
                                    ->first();
                                if (is_null($alise)){
                                    $alise = new LiveAlias();
                                    $alise->from = LiveAlias::kFromTTZB;
                                    $alise->type = LiveAlias::kTypeTeam;
                                    $alise->name = $host;
                                    $alise->content = '天天直播 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                                    dump($alise);
                                    $alise->save();
                                }
                            }
                            $alise = LiveAlias::getAliasByName($away,LiveAlias::kTypeTeam,LiveAlias::kFromTTZB);
                            if (is_null($alise)){
                                $alise = LiaogouAlias::query()->where('from',LiaogouAlias::kFromTTZB)
                                    ->where('type',LiaogouAlias::kTypeTeam)
                                    ->where('target_name',$away)
                                    ->where('sport',LiaogouAlias::kSportTypeFootball)
                                    ->first();
                                if (is_null($alise)){
                                    $alise = new LiveAlias();
                                    $alise->from = LiveAlias::kFromTTZB;
                                    $alise->type = LiveAlias::kTypeTeam;
                                    $alise->name = $away;
                                    $alise->content = '天天直播 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                                    $alise->save();
                                }
                            }
                            echo 'not match ' . $host . ' vs ' .$away . '</br>';
                            $countNotMatch++;
                        }
                    }
                }
            }
        }
        echo 'not match ' . $countNotMatch . '场';
    }

    /**
     * 爬直播
     */
    public function spiderBasketLiveData() {
//        $time = date_create_from_format('Y年m月d日','2017年12月12日');
//        dump($time);
//        return;
        $ql = QueryList::get('http://www.tiantianzhibo.com/lanqiuzhibo/');
        $divs = $ql->find('div.listcontent')->eq(0);

        $datas = $divs->children()->map(function ($item){
            //用is判断节点类型
            if ($item->is('div')) {
                //切割时间 2017年12月12日
                $timeStr = $item->text();
                $time = date_format(date_create_from_format('Y年m月d日',explode(' ',$timeStr)[0]),'Y-m-d');
                return array('type'=>'div','data'=>$time);
            } elseif ($item->is('ul')) {
                //切割时间 主客队
                return array('type'=>'ul','data'=>$item);
            }
        });

        $timeStr = 'error';
        $result = array();
        foreach ($datas as $data){
            if ($data['type'] == 'div'){
                $timeStr = $data['data'];
                $result[$timeStr] = array();
            }
            if ($data['type'] == 'ul'){
                $result[$timeStr][] = $data['data'];
            }
        }
//        dump($result);
        $countNotMatch = 0;

        foreach ($result as $key=>$uls){
            foreach ($uls as $ul){
                $timeStr = $ul->find('li.t1')->eq(0)->text();
                $teamStr = $ul->find('li.t4')->eq(0)->text();
                $leagueStr = $ul->find('li.t3')->eq(0)->text();
                $liveStr = $ul->find('li.t5 a')->eq(0)->href;
                $liveStr = explode('/',$liveStr);
                $liveStr = $liveStr[count($liveStr) - 1];
                $liveStr = explode('.',$liveStr)[0];

                if (strstr($liveStr,'ttzb')){

                }else{
                    $liveStr = '';
                }

                if (strlen($timeStr) > 0 && strlen($teamStr) > 0 && strlen($liveStr) > 0){
                    $timeStr = $key .' '. $timeStr;

                    if (!self::isCanAddToAfter($timeStr)){
                        break;
                    }

                    $teamStr = explode(' VS ',$teamStr);
                    if (count($teamStr) == 2) {
                        $host = $teamStr[0];
                        $away = $teamStr[1];
                        $match = BasketMatch::query()->where('time', $timeStr)
                            ->where(function ($q) use($host,$away){
                                $q->where(function ($q2) use($host, $away){
                                    $q2->where('hname','=',$host)
                                        ->where('aname','=',$away);
                                })
                                    ->orwhere(function ($q2) use($host, $away){
                                    $q2->where('aname','=',$host)
                                        ->where('hname','=',$away);
                                });
                            })
                            ->first();
                        //别名搜一次
                        if (is_null($match)){
                            $team = LiaogouAlias::getAliasByName($host,1,LiaogouAlias::kFromTTZB,LiaogouAlias::kSportTypeBasket);
                            $hid = 0;
                            if (isset($team)){
                                $hid = $team->lg_id;
                            }
                            $team = LiaogouAlias::getAliasByName($away,1,LiaogouAlias::kFromTTZB,LiaogouAlias::kSportTypeBasket);
                            $aid = 0;
                            if (isset($team)){
                                $aid = $team->lg_id;
                            }
                            if ($hid > 0 || $aid > 0) {
                                $query = BasketMatch::query()->where('time', $timeStr);
                                if ($hid > 0)
                                    $query->where(function ($q) use($hid){
                                        $q->where('hid', $hid)
                                            ->orwhere('aid',$hid);
                                    });
                                if ($aid > 0)
                                    $query->where(function ($q) use($aid){
                                        $q->where('hid', $aid)
                                            ->orwhere('aid',$aid);
                                    });
                                $match = $query->first();
                            }
                        }

                        if (isset($match)){
                            $matchLive = MatchLive::query()->where('match_id',$match->id)
                                ->where('sport',MatchLive::kSportBasketball)
                                ->first();
                            if (is_null($matchLive)){
                                $matchLive = new MatchLive();
                                $matchLive->match_id = $match->id;
                                $matchLive->sport = MatchLive::kSportBasketball;
                            }
                            $matchLive->save();
                            $isPrivate = in_array($match->lid, self::basketball_private_array);
                            $show = $isPrivate ? MatchLiveChannel::kHide : MatchLiveChannel::kShow;
                            $private = MatchLiveChannel::kNotPrivate;//$isPrivate ? MatchLiveChannel::kPrivate : MatchLiveChannel::kNotPrivate;
                            MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportBasketball,MatchLiveChannel::kTypeTTZB,$liveStr,12,MatchLiveChannel::kPlatformPC,MatchLiveChannel::kPlayerIFrame,'赛事直播', $show, $private, MatchLiveChannel::kUseAiKQ);
                            echo 'match ' . $host . ' vs ' .$away . ' 频道 '.$liveStr.'</br>';
                        }
                        else{
                            $alise = LiveAlias::getAliasByName($host,LiveAlias::kTypeTeam,LiveAlias::kFromTTZB,LiaogouAlias::kSportTypeBasket);
                            if (is_null($alise)){
                                $alise = LiaogouAlias::query()->where('from',LiaogouAlias::kFromTTZB)
                                    ->where('type',LiaogouAlias::kTypeTeam)
                                    ->where('sport',LiaogouAlias::kSportTypeBasket)
                                    ->where('target_name',$host)
                                    ->first();
                                if (is_null($alise)){
                                    $alise = new LiveAlias();
                                    $alise->from = LiveAlias::kFromTTZB;
                                    $alise->type = LiveAlias::kTypeTeam;
                                    $alise->name = $host;
                                    $alise->sport = LiaogouAlias::kSportTypeBasket;
                                    $alise->content = '天天直播 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                                    dump($alise);
                                    $alise->save();
                                }
                            }
                            $alise = LiveAlias::getAliasByName($away,LiveAlias::kTypeTeam,LiveAlias::kFromTTZB,LiaogouAlias::kSportTypeBasket);
                            if (is_null($alise)){
                                $alise = LiaogouAlias::query()->where('from',LiaogouAlias::kFromTTZB)
                                    ->where('type',LiaogouAlias::kTypeTeam)
                                    ->where('target_name',$away)
                                    ->where('sport',LiaogouAlias::kSportTypeBasket)
                                    ->first();
                                if (is_null($alise)){
                                    $alise = new LiveAlias();
                                    $alise->from = LiveAlias::kFromTTZB;
                                    $alise->type = LiveAlias::kTypeTeam;
                                    $alise->name = $away;
                                    $alise->sport = LiaogouAlias::kSportTypeBasket;
                                    $alise->content = '天天直播 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                                    $alise->save();
                                }
                            }
                            echo 'not match ' . $host . ' vs ' .$away . '</br>';
                            $countNotMatch++;
                        }
                    }
                }
            }
        }
        echo 'not match ' . $countNotMatch . '场';
    }

    private function isCanAddToAfter($matchTime)
    {
        if (is_string($matchTime)) {
            $time = strtotime($matchTime);
        } else {
            $time = date_timestamp_get($matchTime);
        }
        return ($time > date_create("-2 hours")->getTimestamp() && $time < date_create("+2 days")->getTimestamp());
    }
}