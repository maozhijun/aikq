<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\HotVideo;

use App\Http\Controllers\PC\Live\SubjectController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class VideoCoverCommand extends Command
{
    const COVER_TIME_KEY = 'VideoCoverCommand_COVER_TIME_';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hot_video_cover_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '热门录像封面图静态化';

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
        $start = time();
        $this->staticDetail();
        $lastTime = $this->getLastSyncTime();
        $coverUrl = env('LIAOGOU_URL')."aik/videos/covers?time=" . $lastTime;
        $server_output = SubjectController::execUrl($coverUrl);
        $data = json_decode($server_output, true);
        $data = isset($data) ? $data : [];
        $covers = isset($data['covers']) ? $data['covers'] : [];
        $data_time = isset($data['last']) ? $data['last'] : '';
        foreach ($covers as $cover) {
            $this->syncImage($cover);
        }
        $this->setLastSyncTime($data_time);
        echo "热门录像同步图片任务耗时：" . (time() - $start) . " 秒，共同步" . count($covers) . "张图片。\n";
    }

    /**
     * 同步图片到本地服务器
     * @param $imageUrl
     */
    protected function syncImage($imageUrl) {
        $timeout = 5;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $imageUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
        if (starts_with($imageUrl, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        $img = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_code >= 400) {
            echo $imageUrl . " 获取链接内容失败\n";
            return;
        }
        if (!empty($img)) {
            $patch = str_replace('https://www.liaogou168.com', '', $imageUrl);
            $patch = str_replace('http://www.liaogou168.com', '', $patch);
            if (starts_with($patch, '/')) {
                Storage::disk('public')->put("live/videos" . $patch, $img);
            }
        }
    }

    /**
     * 静态化热门录像终端html
     */
    protected function staticDetail() {
        $url = asset('/static/videos/detail');
        SubjectController::execUrl($url);
    }

    /**
     * 获取同步图片批次中最大更新时间
     * @return string
     */
    protected function getLastSyncTime() {
        $key = self::COVER_TIME_KEY;
        $time = Redis::get($key);
        return empty($time) ? '' : $time;
    }

    /**
     * 设置同步图片批次中最大更新时间
     * @param $time
     */
    protected function setLastSyncTime($time) {
        if (!empty($time)) {
            Redis::set(self::COVER_TIME_KEY, $time);
        }
    }

}