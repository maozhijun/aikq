<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/8
 * Time: 10:39
 */

namespace App\Console\Sync;


use App\Models\Match\MatchLive;
use App\Models\Match\MatchLiveChannel;
use App\Models\Match\OtherMatch;
use Illuminate\Console\Command;

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
        //同步 other_matches 开始
        $lastOtherUpdate = OtherMatch::query()->orderByDesc('updated_at')->first();
        $update_at = $lastOtherUpdate->updated_at;
        $query = \App\Models\LgMatch\OtherMatch::query();
        $others = $query->where('updated_at', '>', $update_at)->get();
        $otherCount = 0;
        foreach ($others as $other) {
            $flg = OtherMatch::copyLgOtherMatch($other);
            if ($flg) $otherCount++;
        }
        dump("match有变化的other_matchs数据条数：" . count($others) . "，本库更新成功条数：" . $otherCount);
        //同步 other_matches 结束

        //同步 match_lives 开始
        $lastMatchLiveUpdate = MatchLive::query()->orderByDesc('updated_at')->first();
        $update_at = $lastMatchLiveUpdate->updated_at;
        $query = \App\Models\LgMatch\MatchLive::query();
        $lives = $query->where('updated_at', '>', $update_at)->get();
        $liveCount = 0;
        foreach ($lives as $live) {
            $flg = MatchLive::copyLgMatchLive($live);
            if ($flg) $liveCount++;
        }
        dump("match有变化的match_lives数据条数：" . count($lives) . "，本库更新成功条数：" . $liveCount);
        //同步 match_lives 结束

        //同步 match_live_channels 开始
        $lastChannel = MatchLiveChannel::query()->orderByDesc('updated_at')->first();
        $update_at = $lastChannel->updated_at;
        $query = \App\Models\LgMatch\MatchLiveChannel::query();
        $channels = $query->where('updated_at', '>', $update_at)->get();
        $channelCount = 0;
        foreach ($channels as $channel) {
            $flg = MatchLiveChannel::copyLgMatchLiveChannel($channel);
            if ($flg) $channelCount++;
        }
        dump("match有变化的match_live_channels数据条数：" . count($channels) . "，本库更新成功条数：" . $channelCount);
        //同步 match_live_channels 结束
    }


}