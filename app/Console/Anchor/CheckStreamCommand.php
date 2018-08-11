<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/18
 * Time: 15:22
 */

namespace App\Console\Anchor;


use App\Models\Anchor\AnchorRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class CheckStreamCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_check_stream:run';

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
//        $query = AnchorRoom::query()->where('status', AnchorRoom::kStatusLiving);
//        $query->where('updated_at', '<=', date('Y-m-d H:i', strtotime('-4 minutes')));
        $query = AnchorRoom::query()->where("id", 27);
        $rooms = $query->get();

        foreach ($rooms as $room) {
            $stream = $room->live_flv;
            if (empty($stream)) {
                $stream = $room->live_m3u8;
            }
            if (empty($stream)) {
                $stream = $room->live_rtmp;
            }
            if (!empty($stream)) {
                $isLive = $this->rtmpStreamCheck($stream, $room->id);
            } else {
                $isLive = false;
            }
            $this->setUnLive($room, $isLive);
        }
    }

    //验证流是否正常
    public static function streamCheck($stream, $timeout = 2)
    {
        $isHttps = preg_match("/https:\/\//", $stream);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $stream);
        //curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // connect timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // curl timeout
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // curl timeout
        if ($isHttps) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $exc = curl_exec($ch);

        $status = false;
        if (TRUE === $exc) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) {
                $status = true;
            }
        }
        return $status;
    }

    protected function rtmpStreamCheck($stream, $room_id) {
        $path = '/public/cover/room/' . $room_id . '_test.jpg';
        $outPath = storage_path('app' . $path);
        StreamKeyFrameCommand::spiderRtmpKeyFrame($stream, $outPath);//设置关键帧
        try {
            Storage::get($path);//查看临时文件
            Storage::delete($path);//删除临时文件
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    protected function setUnLive(AnchorRoom $room, $isLive) {
        $key = "stream_un_live_".$room->id;
        $count = Redis::get($key);
        if ($count == null) {
            $count = 0;
        }
        if (!$isLive) {
            $count++;
        } else {
            Redis::del($key);
        }
        if ($count < 3) {
            Redis::setex($key, 60 * 4, $count);
            return;
        }

        $room->status = AnchorRoom::kStatusNormal;//设置不开播
        $room->url = null;
        $room->url_key = null;
        $room->live_flv = null;
        $room->live_rtmp = null;
        $room->live_m3u8 = null;
        $room->save();
    }

}