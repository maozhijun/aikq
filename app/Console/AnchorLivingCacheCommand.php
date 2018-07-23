<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:40
 */

namespace App\Console;


use App\Http\Controllers\PC\Live\LiveController;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AnchorLivingCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_living_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新正在直播';

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
        $rooms = AnchorRoom::getLivingRooms();
        $string = '';
        foreach ($rooms as $room){
            $match = $room->getLivingMatch();
            if (isset($match)) {
                $string[] = array(
                    'room_id' => $room->id,
                    'sport' => $match['sport'],
                    'match_id' => $match['mid'],
                );
            }
        }
        Redis::set('redis_living_room',json_encode($string));
    }
}