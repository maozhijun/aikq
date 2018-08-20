<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 19:40
 */

namespace App\Console;


use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Http\Controllers\PC\Live\LiveController;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Anchor\AnchorRoomTag;
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
        $con = new AnchorController();
        $rooms = AnchorRoom::getLivingRooms();
        $tmp = array();
        foreach ($rooms as $room){
            $con->livingRoomData($room, $tmp);
            $this->staticPlayerUrlApp($con, $room);
        }
        $living_json = json_encode(['code'=>0, 'data'=>$tmp]);
        Storage::disk('public')->put('app/v110/anchor/living.json', $living_json);
        Storage::disk('public')->put('app/v120/anchor/living.json', $living_json);
        $this->staticAppIndex($con, $rooms);
    }

    /**
     * 静态化app接口
     * @param AnchorController $con
     * @param $room
     */
    protected function staticPlayerUrlApp(AnchorController $con, $room) {
        $result = $con->playerUrlAppArray($room);
        $result = json_encode($result);
        $path = 'anchor/room/url/'.$room->id.'.json';
        Storage::disk('public')->put('app/v110/'.$path, $result);
        Storage::disk('public')->put('app/v120/'.$path, $result);
    }

    /**
     * 静态化app首页接口
     * @param AnchorController $con
     * @param $livingRooms
     */
    protected function staticAppIndex(AnchorController $con, $livingRooms) {
        $result = $con->appV110Array($livingRooms);
        $json = json_encode(array('code'=>0, 'data'=>$result));
        Storage::disk('public')->put('app/v110/anchor/index.json', $json);
        Storage::disk('public')->put('app/v120/anchor/index.json', $json);
    }

}