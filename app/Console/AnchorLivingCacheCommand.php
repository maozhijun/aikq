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
use Illuminate\Support\Facades\Storage;

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
//        $string = '';
        $tmp = array();
        foreach ($rooms as $room){
//            $match = $room->getLivingMatch();
//            if (isset($match)) {
//                $string[] = array(
//                    'room_id' => $room->id,
//                    'sport' => $match['sport'],
//                    'match_id' => $match['mid'],
//                );
//            }

            $model = $room->appModel(true);
            if ($room['status'] == AnchorRoom::kLiveStatusLiving){
                $model['statusStr'] = '直播中';
            }
            else{
                $model['statusStr'] = '';
            }
            $model['url'] = '';
            $tmp[] = $model;
        }
        $living_json = json_encode(['code'=>0, 'data'=>$tmp]);
        Storage::disk('public')->put('app/v110/anchor/living.json', $living_json);
//        Redis::set('redis_living_room',json_encode($string));
    }
}