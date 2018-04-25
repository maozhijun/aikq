<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\SubjectVideo;

use App\Http\Controllers\PC\Live\SubjectController;
use App\Http\Controllers\PC\Live\SubjectVideoController;
use App\Http\Controllers\PC\Live\VideoController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SubjectVideoDetailCommand extends Command
{
    const PAGE_KEY_PREFIX = 'SubjectVideoDetailCommand_PAGE_KEY_PREFIX_';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subject_video_detail_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题录像终端静态化';

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
        $tid = "all";

        $videoIntF = new SubjectVideoController();
        $page = $videoIntF->getVideoPageMsg($tid);//获取分页信息
        if (!isset($page['lastPage'])) {
            echo "专题录像分页静态化执行消耗时间：" . (time() - $start) . " 秒。\n";
            return;
        }

        $lastPage = $page['lastPage'];
        $curPage = $this->getCurPage($tid);
        if (empty($curPage)) {
            $curPage = 1;
        } else if ($curPage >= $lastPage) {
            $curPage = 1;
        } else {
            $curPage++;
        }
        $this->staticSubjectVideo($tid, $curPage);
        $this->setCurPage($tid, $curPage);//每次最多静态化 3 页
        echo "专题录像终端静态化(第" . $curPage . "页)执行消耗时间：" . (time() - $start) . " 秒。\n";
    }

    /**
     * 调用url静态化分页
     * @param $type
     * @param $page
     */
    protected function staticSubjectVideo($type, $page) {
        $url = asset('/static/subject/videos/detail/' . $type . '/' . $page);
        SubjectController::execUrl($url);
    }

    /**
     * 静态化类型json
     */
    protected function staticTypes() {
        $url = asset('/static/subject-videos/leagues');
        SubjectController::execUrl($url);
    }

    /**
     * 获取静态化页数
     * @param $tid
     * @return int
     */
    protected function getCurPage($tid) {
        $key = self::PAGE_KEY_PREFIX . $tid;
        $cur = Redis::get($key);
        return $cur;
    }

    /**
     * 设置类型执行到的静态化页数
     * @param $tid
     * @param $cur
     */
    protected function setCurPage($tid, $cur) {
        $key = self::PAGE_KEY_PREFIX . $tid;
        Redis::set($key, $cur);
    }

}