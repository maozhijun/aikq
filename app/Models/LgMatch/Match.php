<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Match extends Model
{
    public $connection = "match";

    const k_genre_all = 1;//全部
    const k_genre_yiji = 2;//一级
    const k_genre_zucai = 4;//足彩
    const k_genre_jingcai = 8;//竞彩
    const k_genre_beijing = 16;//北京单场

    const path_league_football_arrays = [
        '46'=>'zhongchao',
        '31'=>'yingchao',
        '26'=>'xijia',
        '29'=>'yijia',
        '11'=>'fajia',
        '8'=>'dejia',
//        '21'=>'j1',
//        '15'=>'k1',
//        '187'=>'aojia',
        '57'=>'worldcup',
        '73'=>'uefacl',
        '77'=>'uefael',
        '139'=>'afccl',
    ];

    const path_league_basketball_arrays = [
        '1'=>'nba',
        '4'=>'cba',
    ];

    public $timestamps = false;

    protected $hidden = ['id'];

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }

    /**
     * 获取比赛事件
     */
//    public function matchEvents() {
//        return $this->hasMany(MatchEvent::class, 'mid');
//    }

    /**
     * 获取联赛信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function league()
    {
        return $this->hasOne('App\Models\Match\League', 'id', 'lid');
    }

    public function subLeague()
    {
        return $this->hasOne('App\Models\Match\LeagueSub', 'id', 'subid');
    }

    public function matchData()
    {
        return $this->hasOne('App\Models\Match\MatchData', 'id', 'id');
    }

    public function oStage()
    {
        return $this->hasOne('App\Models\Match\Stage', 'id', 'stage');
    }

    public function home()
    {
        return $this->hasOne('App\Models\LgMatch\Team', 'id', 'hid');
    }

    public function away()
    {
        return $this->hasOne('App\Models\LgMatch\Team', 'id', 'aid');
    }

//    public function lineup() {
//        return $this->hasOne(MatchLineup::class, 'id', 'id');
//    }

    public function oddAsian()
    {
        if (!isset($this->odd_asian)) {
            $this->odd_asian = Odd::query()->where(['mid' => $this->id, 'cid' => Odd::default_banker_id, 'type' => 1])->first();
        }
        return $this->odd_asian;
    }

    public function oddOU()
    {
        if (!isset($this->odd_ou)) {
            $this->odd_ou = Odd::query()->where(['mid' => $this->id, 'cid' => Odd::default_banker_id, 'type' => 2])->first();
        }
        return $this->odd_ou;
    }

    public function oddEuro()
    {
        if (!isset($this->odd_euro)) {
            $this->odd_euro = Odd::query()->where(['mid' => $this->id, 'cid' => Odd::default_banker_id, 'type' => 3])->first();
        }
        return $this->odd_euro;
    }

    //判断是否有盘口数据
    public function isHasOdd(){
        $asiaOdd = Odd::query()->where(['mid' => $this->id, 'cid' => Odd::default_banker_id])->whereIn('type', [1,2])->first();
        if (isset($asiaOdd)){
            return true;
        }
        $sb = SportBetting::query()->where("mid", $this->id)->first();
        if (isset($sb) && isset($sb->asia_odd)){
            return true;
        } else if (isset($sb) && isset($sb->odd)){
            return true;
        }
        return false;
    }

    //判断比赛是否在中场休息前(包括未开始)
    public function isBeforeHalf() {
        return $this->status == 0 || $this->status == 1;
    }

    public function roundCN()
    {
        if (isset($this->league)) {
            if ($this->league->type == 1) {//联赛
                if (isset($this->lsid) && isset($this->subLeague)) {//有子联赛
                    if ($this->subLeague->type == 1) {
                        return $this->league->name . $this->subLeague->name . '第' . $this->round . '轮';
                    } else {
                        return $this->league->name . $this->subLeague->name;
                    }
                } else {
                    return $this->league->name . '第' . $this->round . '轮';
                }
            } else {
                if (isset($this->stage) && isset($this->oStage)) {
                    return $this->league->name . $this->oStage->name;
                }
            }
            return $this->league;
        }
        return '';
    }

    public function getStatusText()
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        $status = $this->status;
        return self::getStatusTextCn($status);
    }

    public static function getStatusTextCn($status) {
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return "上半场";
            case 2:
                return "中场";
            case 3:
                return "下半场";
            case 4:
                return "加时";
            case -1:
                return "已结束";
            case -14:
                return "推迟";
            case -11:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }

    /**
     * 比赛是否有比分
     * @return bool
     */
    public function isShowScore()
    {
        $status = $this->status;
        return ($status == 1 || $status == 2 || $status == 3 || $status == -1);
    }


    public static function findMatches($name, $now = null, $afterSevenDay = null, $findAll = false) {
        $rest = [];
        if (!empty($name) || $findAll) {
            if (!isset($now)) {
                $now = date("Y-m-d H:i:s", strtotime('+15 minutes'));
            }
            if (!isset($afterSevenDay)) {
                $afterSevenDay = date("Y-m-d H:i:s", strtotime("+3 days"));
            }
            $query = Match::query()
                ->where('matches.status','<>',-99)
                ->leftJoin(Odd::getDatabaseName().'.odds', function ($join){
                $join->on('odds.mid', '=', 'matches.id')
                    ->where(function ($odd_q) {
                        $odd_q->where('odds.cid', Odd::default_banker_id);//SB
                        $odd_q->orWhere('odds.cid', Odd::default_calculate_cid);//BET365
                        $odd_q->orWhere('odds.cid', Odd::default_king_cid);//金宝博
                    })
                    ->where(function($q){
                        $q->where('odds.type', Odd::k_odd_type_asian)
                            ->orwhere('odds.type', Odd::k_odd_type_ou);
                    });
            })->leftJoin(SportBetting::getDatabaseName().'.sport_bettings', function($join){
                $join->on('sport_bettings.mid', '=', 'matches.id')
                    ->where(function ($q){
                        $q->whereNotNull('sport_bettings.odd')
                            ->orwhereNotNull('sport_bettings.asia_odd');
                    });
            })->selectRaw('matches.id, matches.hname, matches.aname, matches.win_hname, matches.win_aname, matches.lname, matches.win_lname, matches.lid, matches.time, matches.betting_num, IFNULL(min(odds.cid), 0) cid')
                ->groupBy('matches.id','matches.hname', 'matches.aname', 'matches.win_hname', 'matches.win_aname', 'matches.win_lname', 'matches.lname', 'matches.lid', 'matches.time', 'matches.betting_num')
                ->where(function ($q){
                    $q->whereNotNull('odds.middle1')
                        ->orwhereNotNull('sport_bettings.odd')
                        ->orwhereNotNull('sport_bettings.asia_odd');
                })->whereBetween("matches.time", [$now, $afterSevenDay])->where(function ($query) use ($name) {
                    $query
                        ->where("matches.hname", "like", "%$name%")->orWhere("matches.aname", "like", "%$name%")
                        ->orwhere("matches.win_hname", "like", "%$name%")->orWhere("matches.win_aname", "like", "%$name%")
                        ->orwhere("matches.lname", "like", "%$name%")->orWhere("matches.win_lname", "like", "%$name%")
                        ->orwhere("matches.betting_num", "like", "%$name%");
                });
            $matches = $query->orderBy('matches.time', 'asc')->orderBy('matches.betting_num','asc')->orderBy("id")->get();
            $rest["matches"] = $matches;
        }
        return $rest;
    }

    /**
     * 搜索比赛文章
     * @param null $key
     * @return array
     */
    public static function searchArticles($key = null) {
        //$result = self::findMatches($key, date("Y-m-d H:i:s", strtotime('-1 days')) );
        $result = MatchesAfter::findMatches($key, -1, strtotime('-1 days'));
        $mid_array = [];
        $articles = [];//搜索结果
        if (isset($result["matches"])) {
            $matches = $result["matches"];
            foreach ($matches as $match) {
                $mid_array[] = $match->id;
            }
        }

        if (count($mid_array) > 0) {
            $query = GoodsArticles::query();
            $query->join('merchant_statistics', function ($join) {
                $join->on('merchant_statistics.id', '=', 'goods_articles.mch_id');
            });
            $query->where("goods_articles.sport", GoodsArticles::kSportFootball);
            $query->where("goods_articles.status", GoodsArticles::kStatusPublished)->whereIn("goods_articles.match_id", $mid_array);
            $query->where("goods_articles.hide_match", '<>', GoodsArticles::kHideMatch);//隐藏比赛信息的不能通过搜索出来

            $un_hit_query = clone $query;
            //未结束按照比赛时间正序，相同比赛按照专家盈利倒叙。
            $un_hit_query->where(function ($or_where) {
                $or_where->whereNull('goods_articles.hit');
                $or_where->orWhere("goods_articles.hit", 0);
            });
            $un_hit_query->select("goods_articles.*");
            $un_hit_query->orderByRaw("goods_articles.start_at, merchant_statistics.lately_odd desc, merchant_statistics.lately_max_profit_hit desc");

            //比赛已结算的按照比赛时间倒叙，
            $hit_query = clone $query;
            $hit_query->where("goods_articles.hit", ">", 0);
            $hit_query->select("goods_articles.*");
            $hit_query->orderBy("goods_articles.start_at", "desc");
            $hit_query->orderByRaw("goods_articles.start_at, merchant_statistics.lately_odd desc, merchant_statistics.lately_max_profit_hit desc");

            $un_hit_articles = $un_hit_query->get();
            $hit_articles = $hit_query->get();
            foreach ($un_hit_articles as $un) {
                $articles[] = $un;
            }
            foreach ($hit_articles as $hit) {
                $articles[] = $hit;
            }
        }
        return $articles;
    }


    /**
     * 获取今天比赛 开始结束日期
     * @return array
     */
    public static function getMatchTodayClock()
    {
        $todayTenHour = strtotime(date('Y-m-d') . " 10:00:00");
        $now = time();
        if ($now < $todayTenHour) {//凌晨时分
            $start = date('Y-m-d', strtotime('-1 days')) . " 10:00:00";
            $end = date('Y-m-d') . " 09:59:59";
        } else {
            $start = date('Y-m-d') . " 10:00:00";
            $end = date('Y-m-d', strtotime("+1 days")) . " 09:59:59";
        }
        return ["start" => $start, "end" => $end];
    }

    /**
     * 获取当天竞彩、中超的所有赛事
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public static function getTodayMatchLeagues($start_time, $end_time)
    {
        $league_redis_key = "hotMatch_leagues";
        $match_id_redis_key = "hotMatch_matches";

        $leagues_json = Redis::get($league_redis_key);
        $match_id_json = Redis::get($match_id_redis_key);

        $leagues = [];
        $match_id_array = [];

        if (!empty($leagues_json)) {
            $leagues = json_decode($leagues_json, true);
            $match_id_array = json_decode($match_id_json, true);
        } else {
            $query = self::query()->whereBetween("time", [$start_time, $end_time]);
            $query->where(function ($query) {
                $query->where("genre", "&", Match::k_genre_jingcai);
                $query->orWhere("genre", "&", Match::k_genre_beijing);
                $query->orWhere("lid", "46");//中超
                $query->orWhere("lid", "66");//中协杯
            });
            $matches = $query->get();
            //所有赛事id
            //所有联赛
            foreach ($matches as $match) {
                $match_id_array[] = $match->id;
                if (!isset($leagues[$match->lid])) {
                    $leagues[$match->lid] = ["lid" => $match->lid, "name" => $match->league->name, "matches" => [$match->id]];
                } else {
                    $matches = $leagues[$match->lid]["matches"];
                    $matches[] = $match->id;
                    $leagues[$match->lid]["matches"] = $matches;
                }
            }

            //echo dump(json_encode($leagues));
            //获取需要保存的时间
            //$expire = strtotime($end_time) - time();

            Redis::setEx($league_redis_key, 5 * 60, json_encode($leagues));
            Redis::setEx($match_id_redis_key, 5 * 60, json_encode($match_id_array));
        }
        return ["leagues" => $leagues, "match_id_array" => $match_id_array];
    }

    public function appModel(){
        $match = $this;
        $hicon = Team::where('id',$this->hid)->first()['icon'];
        $aicon = Team::where('id',$this->aid)->first()['icon'];
        $model = array(
            'url'=>'https://m.liaogou168.com/matches/data/match_detail/'.$match->id.'.html',
            'hname'=>$match->hname,
            'aname'=>$match->aname,
            'time'=>date_create($match->time)->getTimestamp(),
            'status'=>$match->status,
            'hscore'=>$match->hscore,
            'ascore'=>$match->ascore,
            'hscorehalf'=>$match->hscorehalf,
            'ascorehalf'=>$match->ascorehalf,
            'hicon'=>$hicon,
            'aicon'=>$aicon,
            'league'=>isset($match->league)?$match->league->name:'',
            'leagueStr'=>isset($match->league)?$match->league->name.(isset($match->round) ? '(第'.$match->round.'轮)' :''):'',
        );
        return $model;
    }

    public function getScoreText($withSpace = false) {
        $status = $this->status;
        if ($status == 0) {
            return "VS";
        } else if ($status == -1 || $status > 0) {
            if ($withSpace) {
                return $this->hscore . ' - ' . $this->ascore;
            } else {
                return $this->hscore . '-' . $this->ascore;
            }
        } else {
            return $this->getStatusText();
        }
    }

    public static function getScoreTextCn($status, $hscore, $ascore, $withSpace = false) {
        if ($status == 0) {
            return "VS";
        } else if ($status == -1 || $status > 0) {
            return $hscore.($withSpace ? ' - ' : '-').$ascore;
        } else {
            return self::getStatusTextCn($status);
        }
    }

    public static function getScheduleMatchesByTableName($tableName, $dateStr = "", $count = 12, $isHot = true, $withSelect = true) {
        $startDate2 = null;
        if (isset($dateStr) && strlen($dateStr) > 0) {
            $startDate = date('Y-m-d', strtotime($dateStr)). ' 10:00';
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($dateStr))). ' 10:00';
        } else {
            if (date('H') < 10) {
                $startDate = date('Y-m-d', strtotime('-1 day')). ' 10:00';
                $endDate = date('Y-m-d'). ' 10:00';
            } else {
                $startDate = date('Y-m-d'). ' 10:00';
                $endDate = date('Y-m-d', strtotime('+1 day')). ' 10:00';
                $startDate2 = date('Y-m-d', strtotime('-6 hour'));
            }
        }

        $query = DB::connection('match')->table($tableName)->leftJoin("leagues", "$tableName.lid", "leagues.id");
        if ($withSelect) {
            $query->select("$tableName.*", "$tableName.id as mid", "leagues.name as league_name");
        }
        if($dateStr == ''){
            //今天未完的还在
            $query->where(function ($q) use($startDate,$endDate,$startDate2){
                $q->where(function ($q2) use($startDate,$endDate){
                    $q2->where("time", ">=", $startDate)
                        ->where("time", "<", $endDate);
                })->orwhere(function ($q3) use($startDate,$startDate2){
                    if (isset($startDate2)) {
                        $q3->where('time', '>=', $startDate2)
                            ->where('time', '<', $startDate)
                            ->where('status', '>', 0);
                    }
                });
            });
        }
        else{
            $query->where("time", ">=", $startDate)
                ->where("time", "<", $endDate);
        }
        if ($isHot) {
            $query->orderBy('leagues.hot', 'desc');
        }
        $query->orderBy('status', 'desc');
        $query->orderBy('time', 'asc');

        if ($count > 0) {
            $query->take($count);
        }

        return $query;
    }

    public static function getScheduleMatches($dateStr = "", $count = 12, $isHot = true, $withSelect = true) {
        return self::getScheduleMatchesByTableName('matches', $dateStr, $count, $isHot, $withSelect);
    }

    //获取球队icon
    public function getTeamIcon($isHome = true) {
        $team = $isHome ? $this->home : $this->away;
        $icon = isset($team) ? $team->icon : '';
        return Team::getIcon($icon);
    }

    public static function getTeamIconCn($tid) {
        $team = Team::query()->find($tid);
        //$icon = isset($team) ? $team->icon : '';
        //dump($team);
        if (isset($team)) {
            $icon = $team->lg_icon;
            if (!empty($icon)) {
                return env('CDN_URL') . $icon;
            }
            $icon = $team->icon;
        } else {
            $icon = '';
        }
        return Team::getIcon($icon);
    }

    public function getLeagueName() {
        $league_name = $this->lname;
        if (empty($league_name)) {
            $league = $this->league;
            $league_name = isset($league) ? $league->name : '';
        }
        return $league_name;
    }

    public function getMatchTimeMin() {
        $time = strtotime(isset($this->timehalf)? $this->timehalf : $this->time);
        $timehalf = strtotime($this->timehalf);
        $now = strtotime(date('Y-m-d H:i:s'));
        $status = $this->status;
        if ($status < 0) {
            $matchTime = 90;
        } elseif ($status == 2) {
            $matchTime = 45;
        } elseif ($status == 4) {
            $matchTime = 90;
        }elseif ($this->status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            $matchTime = ((floor(($diff) % 86400 / 60)) > 45 ? 45 : (floor(($diff) % 86400 / 60)));
        } elseif ($this->status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            $matchTime = ((floor(($diff) % 86400 / 60)) > 45 ? 90 : (floor(($diff) % 86400 / 60) + 45));
        } else {
//            $matchTime = substr($match->time, 11, 5);
            $matchTime = -1;
        }
        return $matchTime;
    }

    public function getCurMatchTime($isApp = false) {
        return self::getMatchCurrentTime($this->time, $this->timehalf, $this->status, $isApp);
    }

    //获取足球比赛的即时时间
    public static function getMatchCurrentTime($time, $timehalf, $status, $isApp = false)
    {
        $time = strtotime(isset($timehalf)? $timehalf : $time);
        $timehalf = strtotime($timehalf);
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($status < 0 || $status == 2 || $status == 4) {
            $matchTime = self::getStatusTextCn($status);
        }elseif ($status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            if ($isApp) {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45\'+') : ((floor(($diff) % 86400 / 60)) . '\'');
            } else {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45+' . '<span class="minute">' . '\'') : ((floor(($diff) % 86400 / 60)) . '<span class="minute">' . '\'');
            }
        } elseif ($status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            if ($isApp) {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90\'+') : ((floor(($diff) % 86400 / 60) + 45) . '\'');
            } else {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90+' . '<span class="minute">' . '\'') : ((floor(($diff) % 86400 / 60) + 45) . '<span class="minute">' . '\'');
            }
        } else {
//            $matchTime = substr($match->time, 11, 5);
            $matchTime = '';
        }
        return $matchTime;
    }

    public static function getScheduleCup($lid, $stage, $group = null) {
        $query = self::query();
        $query->where("lid", $lid);
        $query->where("stage", $stage);
        if (!empty($group)) {
            $query->where("group", $group);
        }
        $query->orderBy("time")->orderBy("id");
        return $query->get();
    }

}
