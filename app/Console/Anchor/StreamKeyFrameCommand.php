<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/18
 * Time: 15:22
 */

namespace App\Console\Anchor;


use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Models\Anchor\AnchorRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class StreamKeyFrameCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_key_frame:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查主播房间直播流是否切断';

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
        //获取正在直播的主播房间
        $query = AnchorRoom::query()->where('status', AnchorRoom::kStatusValid);
//        $query->where('created_stream_at', '>', date('Y-m-d H:i', strtotime('-48 hours')));//推流地址48小时以内的比赛
        $query->orderBy('check_at', 'desc');
        $rooms = $query->get();
        foreach ($rooms as $room) {
            $stream = $room->live_flv;
//            if (empty($stream)) {
//                $stream = $room->live_m3u8;
//            }
            if (empty($stream)) {
                $stream = $room->live_rtmp;
            }

            if (!empty($stream)) {
                $outPath = storage_path('app/public/cover/room/' . $room->id . '.jpg');
                self::spiderRtmpKeyFrame($stream, $outPath);
                $room->live_cover = "/cover/room/" . $room->id . ".jpg?rd=" . date('YmdHi');
                $m = @filemtime($outPath);
                if ($m + 180 > time()) {//三分钟内刷新过cover
                    if ($room->live_status != AnchorRoom::kLiveStatusLiving) {//开播
                        $room->live_status = AnchorRoom::kLiveStatusLiving;
                        $room->start_at = date_create();
                    }
                } else {
                    if ($room->live_status != AnchorRoom::kLiveStatusOffline) {//关播
                        $room->live_status = AnchorRoom::kLiveStatusOffline;
                        $room->close_at = date_create();
                    }
                }
                $room->check_at = date_create();
                $room->save();
                $con = new AnchorController();
                $this->staticPlayerUrlApp($con, $room);
            }
        }
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

    public static function spiderKeyFrame($stream, $outPath)
    {
        shell_exec('nohup /usr/bin/ffmpeg -i "' . $stream . '" -y -vframes 1 -f image2 ' . $outPath . ' >> /tmp/ffmpeg.log  &');
    }

    public static function spiderRtmpKeyFrame($stream, $outPath)
    {
        shell_exec("nohup /usr/bin/ffmpeg -i \"$stream\" -f image2 -y -vframes 1 -s 220*135 $outPath >> /tmp/ffmpeg.log &");
    }

}