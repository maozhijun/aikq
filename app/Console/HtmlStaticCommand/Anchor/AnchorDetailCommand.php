<?php

namespace App\Console\HtmlStaticCommand\Anchor;

use App\Console\HtmlStaticCommand\BaseCommand;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AnchorDetailCommand extends BaseCommand
{
    const ROOMS_CACHE_KEY = "ROOMS_CACHE_KEY";

    protected function command_name()
    {
        return "anchor_detail_cache";
    }

    protected function description()
    {
        return "主播终端/播放器静态化";
    }

    protected function onCommonHandler(Request $request)
    {
        $roomArray = $this->getCacheValidRooms(self::ROOMS_CACHE_KEY);
        $con = new \App\Http\Controllers\PC\Anchor\AnchorController();
        $mCon = new \App\Http\Controllers\Mobile\Anchor\AnchorController();
        $mipCon = new \App\Http\Controllers\Mip\Anchor\AnchorController();

        $request = new Request();
        foreach ($roomArray as $index=>$room) {
            if ($index >= 50) {
                break;
            }

            //终端静态化
            $room_id = $room['id'];
            $request->merge(['room_id'=>$room_id]);

            $html = $con->room($request, $room_id);
            if (!empty($html)) {
                Storage::disk('public')->put('www/anchor/room' . $room_id . '.html', $html);
            }

            $mCon->roomStatic($request);
            $mipCon->roomStatic($request);

            //播放器静态化
            $player = $con->player($request, $room_id);
            if (!empty($player)) {
                Storage::disk('public')->put('www/anchor/room/player/' . $room_id . '.html', $player);
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
            Redis::setEx($key, 1 * 60, json_encode($roomArray));
        }
        return $roomArray;
    }
}