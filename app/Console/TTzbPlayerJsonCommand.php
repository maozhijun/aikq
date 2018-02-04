<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/4
 * Time: 17:44
 */

namespace App\Console;


use App\Http\Controllers\PC\Live\LiveController;
use App\Models\Match\MatchLiveChannel;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TTzbPlayerJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ttzb_player_json_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷新天天直播接口';

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
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json)){
            //return abort(404);
            return;
        }
        if (!isset($json['matches'])) return;
        $matches = $json['matches'];
        $con = new LiveController();
        foreach ($matches as $time=>$match_array) {
            foreach ($match_array as $match) {
                if (!isset($match) || !isset($match['time'])) {
                    continue;
                }
                $status = $match['status'];
                $m_time = strtotime($match['time']);
                $now = time();

                if ($status > 0 || ($now >= $m_time && $now - $m_time <= (60 * 60) ) || ($now < $m_time && $m_time - $now <= (3 * 60 * 60) ) ) {//1小时-3小时内的赛事的天天源做静态化。
                    if (isset($match['channels'])) {
                        $channels = $match['channels'];
                        foreach ($channels as $channel) {
                            $ch_id = $channel['id'];
                            if ($channel['type'] == MatchLiveChannel::kTypeTTZB) {
                                $con->staticLiveUrl(new Request(), $ch_id, true);
                                echo 'done ' . $ch_id . ';';
                                usleep(100);
                            }
                        }
                    }
                }
            }
        }
    }

}