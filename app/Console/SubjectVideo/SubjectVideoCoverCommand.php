<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\SubjectVideo;

use App\Http\Controllers\PC\Live\SubjectController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class SubjectVideoCoverCommand extends Command
{

    const COVER_TIME_KEY = 'SubjectVideoCoverCommand_COVER_TIME_';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subject_video_cover_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题录像封面图同步';

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
        $lastTime = $this->getLastSyncTime();
        $coverUrl = env('LIAOGOU_URL')."aik/subjects/league/video/covers?time=" . $lastTime;
        $server_output = SubjectController::execUrl($coverUrl);
        $data = json_decode($server_output, true);
        $data = isset($data) ? $data : [];
        $covers = isset($data['covers']) ? $data['covers'] : [];
        $data_time = isset($data['last']) ? $data['last'] : '';
        foreach ($covers as $cover) {
            self::syncImage($cover, 'live/subject/videos');
        }
        $this->setLastSyncTime($data_time);
        echo "专题录像封面图同步任务耗时：" . (time() - $start) . " 秒，共同步" . count($covers) . "张图片。\n";
    }

    /**
     * 同步图片到本地服务器
     * @param $imageUrl
     * @param $savePatch
     */
    public static function syncImage($imageUrl, $savePatch) {
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
                Storage::disk('public')->put($savePatch . $patch, $img);
            }
        }
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