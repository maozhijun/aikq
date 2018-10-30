<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/27
 * Time: 12:35
 */

namespace App\Console\Anchor;


use App\Http\Controllers\PC\Anchor\AnchorController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AnchorJsonCommand extends Command
{

    const ROOMS_CACHE_KEY = "AnchorJsonCommand_ROOMS_CACHE_KEY";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_json_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '主播终端播放url静态化';

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
        //做队列
        $cache = Redis::get('akq_service_static_url');
        $urls = [];//json_decode($cache, true);
        for ($i = 0 ; $i < min(10,count($urls)) ; $i++){
            $url = asset($urls[$i]);
            echo $url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
            curl_exec ($ch);
            curl_close ($ch);
        }
        return;

        $roomArray = AnchorDetailCommand::getCacheValidRooms(self::ROOMS_CACHE_KEY);
        $con = new AnchorController();
        $request = new Request();
        //Route::get('/anchor/room/url/{room_id}.json',"AnchorController@playerUrl");//直播链接
        foreach ($roomArray as $index=>$room) {
            if ($index >= 80) {
                break;
            }
            $room_id = $room['id'];
            $json = $con->playerUrl($request, $room_id)->getData();
            $json = json_encode($json);
            if (!empty($json)) {
                Storage::disk('public')->put('static/json/pc/anchor/room/url/' . $room_id . '.json', $json);
            }
            unset($roomArray[$index]);
        }
        Redis::setEx(self::ROOMS_CACHE_KEY, 60 * 60, json_encode($roomArray));
    }
}