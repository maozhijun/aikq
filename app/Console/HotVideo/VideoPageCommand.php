<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\HotVideo;

use App\Http\Controllers\PC\Live\SubjectController;
use App\Http\Controllers\PC\Live\VideoController;
use App\Models\Match\HotVideo;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class VideoPageCommand extends Command
{
    const PAGE_KEY_PREFIX = 'VideoPageCommand_KEY_';
    const STATIC_PAGE_SIZE = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hot_video_page_cache:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '视频列表（tab、tag）静态化';

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
        //静态化策略：1.获取 上次静态化的最后页数，对比当前的 最后页数，如果 最后页数有变化，则静态化全部页面，否则只静态化首页

        $type = $this->argument('type');
        $timeout = env("DEBUG", "") == "1" ? 8 : 3;

        switch ($type) {
            case "tab":
                //右侧视频tab列表静态化
                $tabs = HotVideo::getVideoTabs();
                foreach ($tabs as $tab) {
                    echo " tag ：" . $tab . "\n";
                    $json = HotVideo::staticHotVideosHtml($tab, 1);

                    $array = json_decode($json, true);
                    if (is_null($array) || !isset($array["curPage"]) || !isset($array["lastPage"])) {
                        return;
                    }
                    $lastPage = $array["lastPage"];
                    $curPage = $this->getCurPage($tab);
                    $forPage = self::STATIC_PAGE_SIZE + $curPage;

                    for (; $curPage <= $forPage; $curPage++) {
                        if ($curPage > $lastPage) continue;
                        HotVideo::staticHotVideosHtml($tab, $curPage);
                    }

                    if ($curPage > $lastPage) {
                        $this->setCurPage($tab, 2);
                    } else {
                        $this->setCurPage($tab, $curPage);
                    }

                }
                break;
            case "league":
                //赛事、联赛 视频列表静态化
                $leagues = HotVideo::getVideoLeagues();
                foreach ($leagues as $league) {
                    $name_en = $league["name_en"];
                    echo " league name_en ：" . $name_en . "\n";
                    $json = HotVideo::staticHotVideosLeagueHtml($name_en, 1);

                    $array = json_decode($json, true);
                    if (is_null($array) || !isset($array["curPage"]) || !isset($array["lastPage"])) {
                        return;
                    }
                    $lastPage = $array["lastPage"];
                    $curPage = $this->getCurPage($name_en);
                    $forPage = self::STATIC_PAGE_SIZE + $curPage;

                    for (; $curPage <= $forPage; $curPage++) {
                        if ($curPage > $lastPage) continue;
                        HotVideo::staticHotVideosLeagueHtml($league["name_en"], $curPage);
                    }

                    if ($curPage > $lastPage) {
                        $this->setCurPage($name_en, 2);
                    } else {
                        $this->setCurPage($name_en, $curPage);
                    }
                }
                break;
            case "tag":
                //球星视频列表静态化
                $stars = HotVideo::getVideoStars();
                foreach ($stars as $star) {
                    echo " start tag_id ：" . $star["tag_id"] . " sport ：" . $star["sport"] . "\n";
                    $tagId = $star["tag_id"];
                    $json = HotVideo::staticHotVideosTagHtml($tagId, $star["sport"], 1);

                    $array = json_decode($json, true);
                    if (is_null($array) || !isset($array["curPage"]) || !isset($array["lastPage"])) {
                        return;
                    }
                    $lastPage = $array["lastPage"];
                    $curPage = $this->getCurPage($tagId);
                    $forPage = self::STATIC_PAGE_SIZE + $curPage;

                    for (; $curPage <= $forPage; $curPage++) {
                        if ($curPage > $lastPage) continue;
                        HotVideo::staticHotVideosTagHtml($star["tag_id"], $star["sport"], $curPage);
                    }

                    if ($curPage > $lastPage) {
                        $this->setCurPage($tagId, 2);
                    } else {
                        $this->setCurPage($tagId, $curPage);
                    }
                }
                break;
        }

        echo "视频 $type 静态化执行消耗时间：" . (time() - $start) . " 秒。\n";
    }

    protected function test($json, $type) {
        $array = json_decode($json, true);
        if (is_null($array) || !isset($array["curPage"]) || !isset($array["lastPage"])) {
            return;
        }
        $lastPage = $array["lastPage"];
        $curPage = $this->getCurPage($type);
        for (; $curPage <= $lastPage; $curPage++) {

        }
        if ($curPage > $lastPage) {
            $this->setCurPage($type, 2);
        } else {
            $this->setCurPage($type, $curPage);
        }
    }

    /**
     * 获取静态化页数
     * @param $type
     * @return int
     */
    protected function getCurPage($type) {
        $key = self::PAGE_KEY_PREFIX . $type;
        $cur = Redis::get($key);
        if (empty($cur)) {
            $cur = 2;
        }
        return $cur;
    }

    /**
     * 设置类型执行到的静态化页数
     * @param $type
     * @param $cur
     */
    protected function setCurPage($type, $cur) {
        $key = self::PAGE_KEY_PREFIX . $type;
        Redis::set($key, $cur);
    }

}