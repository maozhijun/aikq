<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/8
 * Time: 10:39
 */

namespace App\Console\Sync;


use App\Http\Controllers\Sync\BasketballController;
use App\Http\Controllers\Sync\FootballController;
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\Match;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class LiveSyncCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_live_matches:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步直播数据';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'fb':
                $nowDate = '2018-08-10';
                $startDate = '2018-05-07';
                $startTime = strtotime($startDate);
                $nowTime = strtotime($nowDate);
                while ($startTime < $nowTime) {
                    dump($startDate);
                    $this->syncFootball($startDate);
                    $startTime = $startTime + 24 * 60 * 60;
                    $startDate = date('Y-m-d', $startTime);
                    sleep(2);
                }
                break;
            case 'bb':
                $this->syncBasketball();
                break;
            case 'all':
                $this->syncFootball();
                $this->syncBasketball();
                break;
        }
    }


    public function syncFootball($date = '') {
        $date = empty($date) ? date('Y-m-d') : $date;
        $start = time();
        //同步取消
        //同步篮球数据
        $key = "LiveSyncCommand_ID_Football";
        $lastId = Redis::get($key);
        $lastId = empty($lastId) ? 1 : $lastId + 1;
        $query = Match::query()->whereBetween('time', [$date.' 00:00:00', $date.' 23:59:59']);//
        //$query->where('id', '>=', $lastId);
        $query->take(1500)->orderBy('id');
        $lgMatches = $query->get();
        $fCon = new FootballController();
        foreach ($lgMatches as $lgMatch) {
            $id = $lgMatch->id;

            $aMatch = \App\Models\Match\Match::query()->find($id);
            if (!isset($aMatch)) {
                $aMatch = new \App\Models\Match\Match();
                $aMatch->id = $id;
            }

            $fCon->copyMatch($lgMatch, $aMatch);

            $aMatch->save();
            $lastId = $lgMatch->id;
        }
        dump("足球更新 LAST_ID：" . $lastId . "，本次更新时间为：" . (time() - $start) . ",更新条数：" . count($lgMatches));
        Redis::set($key, $lastId);
    }


    public function syncBasketball() {
        $start = time();
        //同步取消
        //同步篮球数据
        $key = "LiveSyncCommand_ID_Basketball";
        $lastId = Redis::get($key);
        $lastId = empty($lastId) ? 1 : $lastId + 1;
        $query = BasketMatch::query()->where('id', '>=', $lastId);
        $query->take(1000)->orderBy('id');
        $lgMatches = $query->get();
        $bCon = new BasketballController();
        foreach ($lgMatches as $lgMatch) {
            $id = $lgMatch->id;

            $aMatch = \App\Models\Match\BasketMatch::query()->find($id);
            if (!isset($aMatch)) {
                $aMatch = new \App\Models\Match\BasketMatch();
                $aMatch->id = $id;
            }

            $bCon->copyMatch($lgMatch, $aMatch);

            $aMatch->save();
            $lastId = $lgMatch->id;
        }
        dump("篮球更新 LAST_ID：" . $lastId . "，本次更新时间为：" . (time() - $start) . ",更新条数：" . count($lgMatches));
        Redis::set($key, $lastId);
    }

}