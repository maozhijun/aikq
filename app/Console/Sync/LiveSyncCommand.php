<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/8
 * Time: 10:39
 */

namespace App\Console\Sync;


use App\Http\Controllers\Sync\BasketballController;
use App\Models\LgMatch\BasketMatch;
use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\Match\OtherMatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class LiveSyncCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_live_matches:run';

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
        $start = time();
        //同步取消
        //同步篮球数据
        $key = "LiveSyncCommand_ID";
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
        dump("LAST_ID：" . $lastId . "，本次更新时间为：" . (time() - $start) . ",更新条数：" . count($lgMatches));
        Redis::set($key, $lastId);
    }


}