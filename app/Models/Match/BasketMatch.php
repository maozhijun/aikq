<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 19:29
 */

namespace App\Models\Match;


use App\Models\LgMatch\BasketTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class BasketMatch extends Model
{
//    public $connection = "match";

    const kStatusNoStart = 0, kStatusFirst = 1, kStatusSecond = 2, kStatusThird = 3, kStatusFourth = 4, kStatusEnd = -1;//比赛状态(0未开始,1第一节,2第二节,3第三节,4第四节,-1已结束

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }

    public function league() {
        return $this->hasOne(BasketLeague::class, 'id', 'lid');
    }

    public function home()
    {
        return $this->hasOne('App\Models\LgMatch\BasketTeam', 'id', 'hid');
    }

    public function away()
    {
        return $this->hasOne('App\Models\LgMatch\BasketTeam', 'id', 'aid');
    }

    //判断比赛是否在中场休息前(包括未开始)
    public function isBeforeHalf() {
        return $this->status == 0 || $this->status == 1 || $this->status == 2;
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
                return "第一节";
            case 2:
                return "第二节";
            case 3:
                return "第三节";
            case 4:
                return "第四节";
            case 5:
                return "加时1";
            case 6:
                return "加时2";
            case 7:
                return "加时3";
            case 50:
                return "中场";
            case -1:
                return "已结束";
            case -5:
                return "推迟";
            case -2:
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

    public function getLeagueName() {
        $league_name = $this->lname;
        if (empty($league_name)) {
            $league_name = $this->win_lname;
        }
        if (empty($league_name)) {
            $league = $this->league;
            $league_name = isset($league) ? $league->name : '';
        }
        return $league_name;
    }

    //获取球队icon
    public function getTeamIcon($isHome = true) {
        $team = $isHome ? $this->home : $this->away;
        if (isset($team)) {
            $icon = $team->lg_icon;
            if (!empty($icon)) {
                return env('LG_CDN') . $icon;
            }
            $icon = $team->icon;
        } else {
            $icon = '';
        }
        return BasketTeam::getIcon($icon);
    }

    //获取球队icon
    public static function getTeamIconCn($tid) {
        $team = BasketTeam::query()->find($tid);
        if (isset($team)) {
            $icon = $team->lg_icon;
            if (!empty($icon)) {
                return env('CDN_URL') . $icon;
            }
            $icon = $team->icon;
        } else {
            $icon = '';
        }
        return BasketTeam::getIcon($icon);
    }

    /**
     * 比赛是否有比分
     * @return bool
     */
    public function isShowScore()
    {
        $status = $this->status;
        return ($status > 0 || $status == -1);
    }

    public function appModel(){
        $match = $this;
//        $hicon = Team::where('id',$this->hid)->first()['icon'];
//        $aicon = Team::where('id',$this->aid)->first()['icon'];
        $model = array(
//            'url'=>'https://m.liaogou168.com/matches/data/match_detail/'.$match->id.'.html',
            'hname'=>$match->hname,
            'aname'=>$match->aname,
            'time'=>date_create($match->time)->getTimestamp(),
            'status'=>$match->status,
            'statusStr'=>$match->getStatusText(),
            'hscore'=>$match->hscore,
            'ascore'=>$match->ascore,
//            'hscorehalf'=>$match->hscorehalf,
//            'ascorehalf'=>$match->ascorehalf,
//            'hicon'=>$hicon,
//            'aicon'=>$aicon,
            'league'=>isset($match->league)?$match->league->name:$match['win_lname'],
            'leagueStr'=>isset($match->league)?$match->league->name.(isset($match->round) ? '(第'.$match->round.'轮)' :''):'',
        );
        return $model;
    }

    /**
     * 搜索比赛文章
     * @param null $key
     * @return array
     */
    public static function searchArticles($key = null) {
        $result = BasketMatchesAfter::findMatches($key, date("Y-m-d H:i:s", strtotime('-1 days')) );
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
            $query->where("goods_articles.sport", GoodsArticles::kSportBasketball);
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
     * 获取当天竞彩、中超的所有赛事
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public static function getTodayMatchLeagues($start_time, $end_time)
    {
        $league_redis_key = "hotMatch_leagues_basket";
        $match_id_redis_key = "hotMatch_matches_basket";

        $leagues_json = Redis::get($league_redis_key);
        $match_id_json = Redis::get($match_id_redis_key);

        $leagues = [];
        $match_id_array = [];

        if (!empty($leagues_json)) {
            $leagues = json_decode($leagues_json, true);
            $match_id_array = json_decode($match_id_json, true);
        } else {
            $query = self::query()->whereBetween("time", [$start_time, $end_time]);
            /*$query->where(function ($query) {
                $query->where("genre", "&", Match::k_genre_jingcai);
                $query->orWhere("genre", "&", Match::k_genre_beijing);
                $query->orWhere("lid", "46");//中超
                $query->orWhere("lid", "66");//中协杯
            });*/
            $matches = $query->get();
            //所有赛事id
            //所有联赛
            foreach ($matches as $match) {
                if (!isset($match->lid)) continue;
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

    public static function getScheduleMatchesByTableName($tableName, $dateStr = "", $count = 12, $isHot = true, $withSelect = false) {
        $startDate2 = null;
        if (isset($dateStr) && strlen($dateStr) > 0) {
            $startDate = date('Y-m-d', strtotime($dateStr)). ' 12:00';
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($dateStr))). ' 12:00';
        } else {
            if (date('H') < 12) {
                $startDate = date('Y-m-d', strtotime('-1 day')). ' 12:00';
                $endDate = date('Y-m-d'). ' 12:00';
            } else {
                $startDate = date('Y-m-d'). ' 12:00';
                $endDate = date('Y-m-d', strtotime('+1 day')). ' 12:00';
                $startDate2 = date('Y-m-d', strtotime('-6 hour'));
            }
        }

        $query = DB::connection('match')->table($tableName)->select("$tableName.*");
        if ($withSelect) {
            $query->leftJoin("basket_leagues as l", "$tableName.lid", "l.id");
            $query->addSelect("l.name as league_name", "l.system as system");
        }
        if($dateStr == ''){
            //今天未完的还在
            $query->where(function ($q) use($startDate,$endDate, $startDate2){
                $q->where(function ($q2) use($startDate,$endDate){
                    $q2->where("time", ">=", $startDate)
                        ->where("time", "<", $endDate);
                });
                $q->orwhere(function ($q3) use($startDate,$startDate2){
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
            $query->orderBy('l.hot', 'desc');
        }
        $query->orderBy('status', 'desc');
        $query->orderBy('time', 'asc')->orderBy('id');

        if ($count > 0) {
            $query->take($count);
        }

        return $query;
    }

    public static function getScheduleMatches($dateStr = "", $count = 12, $isHot = true, $withSelect = false) {
        return self::getScheduleMatchesByTableName('basket_matches', $dateStr, $count, $isHot, $withSelect);
    }

    //判断赛制是否是半全场
    public static function isHalfFormat($format) {
        return $format == 1;
    }
}