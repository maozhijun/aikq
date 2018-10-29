<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\Subject;


use App\Console\SubjectVideo\VideoCommand;
use App\Console\SubjectVideo\SubjectVideoPageCommand;
use App\Http\Controllers\PC\Live\SubjectController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CoverCommand extends Command
{
    const COVER_TIME_KEY = 'Subject_CoverCommand_COVER_TIME_';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subject_cover_sync:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题封面同步';

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
        $coverUrl = env('LIAOGOU_URL')."aik/subjects/covers?time=" . $lastTime;
        $server_output = SubjectController::execUrl($coverUrl);
        $data = json_decode($server_output, true);
        $data = isset($data) ? $data : [];
        $covers = isset($data['covers']) ? $data['covers'] : [];
        $data_time = isset($data['last']) ? $data['last'] : '';
        foreach ($covers as $cover) {
            VideoCommand::syncImage($cover, 'live/subject');
        }
        $this->setLastSyncTime($data_time);
        echo "专题封面图同步任务耗时：" . (time() - $start) . " 秒，共同步" . count($covers) . "张图片。\n";
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