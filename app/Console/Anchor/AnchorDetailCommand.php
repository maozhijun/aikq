<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/27
 * Time: 11:10
 */

namespace App\Console\Anchor;


use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AnchorDetailCommand extends Command
{

    const ROOMS_CACHE_KEY = "ROOMS_CACHE_KEY";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_detail_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '主播终端/播放器静态化';

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
    public function handle() {
        $roomArray = $this->getCacheValidRooms(self::ROOMS_CACHE_KEY);
        //Route::get("/anchor/room/{room_id}.html", "AnchorController@room");//房间
        //Route::get('/anchor/room/player/{room_id}.html',"AnchorController@player");//播放器 静态化
        $con = new AnchorController();
        $request = new Request();
        foreach ($roomArray as $index=>$room) {
            if ($index >= 50) {
                break;
            }
            //终端静态化
            $room_id = $room['id'];
            $html = $con->room($request, $room_id);
            if (!empty($html)) {
                Storage::disk('public')->put('static/anchor/room/' . $room_id . '.html', $html);
            }

            //播放器静态化
            $player = $con->player($request, $room_id);
            if (!empty($player)) {
                Storage::disk('public')->put('static/anchor/room/player/' . $room_id . '.html', $player);
            }
            unset($roomArray[$index]);
        }
        Redis::setEx(self::ROOMS_CACHE_KEY, 60 * 60, json_encode($roomArray));
    }

    /**
     * 获取有效的主播房间
     * @param $key
     * @return array|mixed
     */
    public static function getCacheValidRooms($key) {
        $cache = Redis::get($key);
        $roomArray = json_decode($cache, true);
        if (is_null($roomArray) || count($roomArray) == 0) {
            $rooms = AnchorRoom::validRooms();
            foreach ($rooms as $room) {
                $roomArray[] = ['id'=>$room->id];
            }
            Redis::setEx($key, 60 * 60, json_encode($roomArray));
        }
        return $roomArray;
    }

}