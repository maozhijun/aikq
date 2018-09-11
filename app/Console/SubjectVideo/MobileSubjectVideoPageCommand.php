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
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MobileSubjectVideoPageCommand extends Command
{
    const PAGE_KEY_PREFIX = 'MobileSubjectVideoPageCommand_PAGE_KEY_PREFIX_';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobile_subject_video_page_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '手机站 专题录像分页列表静态化';

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
        $videoIntF = new SubjectVideoController();
        $leagues = $videoIntF->getLeagues();
        foreach ($leagues as $tid=>$league) {//暂时只静态化 all
            if (isset($league['count']) && $league['count'] == 0) {
                continue;
            }
            $page = $videoIntF->getVideoPageMsg($tid);//获取分页信息
            if (!isset($page['lastPage'])) {
                continue;
            }
            $lastPage = $page['lastPage'];
            $curPage = $this->getCurPage($tid);
            if ($curPage > $lastPage) {
                $curPage = 1;
                $this->setCurPage($tid, $curPage);
            }
            $forPage = min($lastPage, $curPage + 6);//每次静态化6页
            for (;$curPage <= $forPage; $curPage++) {
                $this->staticPage($tid, $curPage);//静态化 终端页/json
            }
            usleep(100);//等待100毫秒
            $this->setCurPage($tid, $curPage);//每次最多静态化 6 页
        }
        echo "专题录像分页静态化执行消耗时间：" . (time() - $start) . " 秒。\n";
    }

    /**
     * 调用url静态化分页
     * @param $type
     * @param $page
     */
    protected function staticPage($type, $page) {
        $sub = new SubjectVideoController();
        $sub->staticSubjectVideosHtml(new Request(), $type, $page, true);
    }

    /**
     * 获取静态化页数
     * @param $tid
     * @return int
     */
    protected function getCurPage($tid) {
        $key = self::PAGE_KEY_PREFIX . $tid;
        $cur = Redis::get($key);
        if (empty($cur)) {
            $cur = 1;
        }
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