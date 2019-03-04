<?php

namespace App\Console;

use App\Console\Anchor\AnchorJsonCommand;
use App\Console\Anchor\CheckStreamCommand;
use App\Console\Anchor\StreamKeyFrameCommand;
use App\Console\Article\ArticleLiveCellCommands;
//use App\Console\HotVideo\VideoCoverCommand;
use App\Console\Article\ArticlesCacheCommand;
use App\Console\Cms\CmsChannelsCommand;
use App\Console\Data\DataCommand;
use App\Console\Record\RecordCommand;
use App\Console\Download\DownloadCommand;
use App\Console\HtmlStaticCommand\Anchor\AnchorDetailCommand;
use App\Console\HtmlStaticCommand\Anchor\AnchorIndexCommand;
use App\Console\HtmlStaticCommand\Article\ArticleDetailCommand;
use App\Console\HtmlStaticCommand\Article\ArticlePageCommand;
use App\Console\HtmlStaticCommand\IndexCommand;
use App\Console\HtmlStaticCommand\LeHuChannelCommand;
use App\Console\HtmlStaticCommand\OtherPlayerCommand;
use App\Console\HtmlStaticCommand\Subject\DetailCommand;
use App\Console\HtmlStaticCommand\Team\TeamDetailCommand;
use App\Console\JustFun\JustFunStreamStaticCommand;
use App\Console\Shop\ShopLiveCommand;
use App\Console\Sitemap\GenerateSitemapCommand;
use App\Console\Spider\SpiderTTZBCommand;
use App\Console\LiveCheck\LiveCollectCommands;
//use App\Console\Subject\CoverCommand;
use App\Console\Subject\LeaguesJsonCommand;
use App\Console\Subject\PlayerCommand;
//use App\Console\HotVideo\VideoPageCommand;
use App\Console\SubjectVideo\MobileSubjectVideoPageCommand;
//use App\Console\SubjectVideo\SubjectVideoCoverCommand;
//use App\Console\SubjectVideo\SubjectVideoDetailCommand;
use App\Console\SubjectVideo\SubjectVideoPageCommand;
use App\Console\SubjectVideo\VideoCommand;
use App\Console\Sync\BasketballMatchCommand;
use App\Console\Sync\BasketballUpdateMatchCommand;
use App\Console\Sync\FootballMatchCommand;
use App\Console\Sync\FootballMatchUpdateCommand;
use App\Console\Sync\LiveSyncCommand;
use App\Http\Controllers\Mobile\Live\LiveController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        IndexCommand::class,//直播 列表静态化                  DB
        LiveDetailCommand::class,//PC终端、移动终端html缓存。  DB
        LeHuChannelCommand::class,//刷新乐虎线路播放链接

        PlayerJsonCommand::class,//静态化赛前1小时和正在比赛的 线路         DB
        NoStartPlayerJsonCommand::class,//静态化 赛前1小时前未开始的 线路   DB

        DeleteExpireFileCommand::class,//删除过期文件

        LivesJsonCommand::class,//列表json静态化               DB
        DBSpreadCommand::class,//懂球、柠檬分享页
        TTzbPlayerJsonCommand::class,//天天直播 （开始前1小时 - 开始后 3小时的比赛） 线路静态化

        DetailCommand::class,//subject 专题终端静态化           DB
        LeaguesJsonCommand::class,//subject 专题列表json静态化  DB

        //VideoPageCommand::class,//热门录像分页列表静态化        DB
        //VideoCoverCommand::class,//热门录像封面图同步

        //CoverCommand::class,//专题封面同步
        //SubjectVideoCoverCommand::class,//专题录像 封面图同步到本机
        SubjectVideoPageCommand::class,//专题录像 静态化分页列表、专题终端页             DB
        VideoCommand::class,//录像终端静态化
        PlayerCommand::class,//录像player静态化                                          不需要修改
        MobileSubjectVideoPageCommand::class,//专题录像 wap 列表/终端/线路 json静态化    DB


        //FIFACommand::class,//世界杯

        //同步数据相关 开始
        FootballMatchCommand::class,//同步matches数据到爱看球
        FootballMatchUpdateCommand::class,//更新matches数据到爱看球

        BasketballMatchCommand::class,//同步basket_matches数据到爱看球
        BasketballUpdateMatchCommand::class,//更新basket_matches数据到爱看球
        LiveSyncCommand::class,//同步更新 直播相关的数据
        //同步数据相关 结束

        //主播相关定时任务
        CheckStreamCommand::class,//检查主播流是否在直播，没直播的则修改为 结束直播状态
        StreamKeyFrameCommand::class,//获取正在直播的主播直播流的关键帧
        AnchorLivingCacheCommand::class,
        AnchorIndexCommand::class,//主播首页定时任务
        AnchorDetailCommand::class,//主播终端定时任务
        AnchorJsonCommand::class,//主播播放json定时任务

        ArticlePageCommand::class,//文章分页定时任务
        ArticleLiveCellCommands::class,//文章直播单元静态化定时任务
        ArticlesCacheCommand::class,//文章最新列表
        ArticleDetailCommand::class,//所有文章终端静态化

        SpiderTTZBCommand::class,//抓取天天直播的源
        NotFoundCommand::class,//404页面

        ShopLiveCommand::class,//shop 安卓app直播页面
        CmsChannelsCommand::class,//cms 直播终端线路静态化

        BaiduPushCommand::class,//百度主动推送

        GenerateSitemapCommand::class, //sitemap生成器

        DownloadCommand::class,//静态化下载页面

        OtherPlayerCommand::class,//其他静态播放器静态化

        JustFunStreamStaticCommand::class,//抓饭流静态化

        TeamDetailCommand::class, //球队终端静态化

        DataCommand::class,//数据静态化

        RecordCommand::class,//录像静态化
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('record_cache:run index')->everyFiveMinutes();//录像终端静态化,静态化完他就不会执行逻辑了
        $schedule->command('record_cache:run league')->everyThirtyMinutes();
        $schedule->command('record_cache:run detail')->everyFiveMinutes();

        //足球、篮球比赛 数据同步 开始
        $schedule->command('sync_update_football_matches:run')->everyMinute();
        $schedule->command('sync_update_basketball_matches:run')->everyMinute();
        //$schedule->command('sync_live_matches:run')->everyMinute();
        //足球、篮球比赛 数据同步 结束

        $schedule->command('live_json_cache:run')->everyMinute();//每分钟刷新一次赛事缓存
        $schedule->command('index_cache:run all')->everyMinute();//每分钟刷新主页缓存

        //$schedule->command('live_detail_cache:run')->everyFiveMinutes();//每5分钟刷新终端缓存  在保存的时候静态化
        $schedule->command('lh_channel:run all')->everyFiveMinutes();//刷新乐虎直播线路

        $schedule->command('player_json_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次正在直播的比赛的线路内容 在保存的时候静态化
        //$schedule->command('ns_player_json_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次未开始比赛的线路内容 一小时内执静态化所有的json 在保存的时候静态化

        $schedule->command('delete_cache:run')->dailyAt('07:00');//每天删除一次文件

        //$schedule->command('mobile_detail_cache:run')->everyMinute();//每五分钟刷新移动直播终端缓存

        $mController = new LiveController();
        $schedule->call(function () use ($mController) {
            $mController->matchLiveStatic(new Request());//每分钟刷新比赛状态数据
        })->everyMinute();

        //appsocket相关
        //静态化app主播接口  /app/v120/anchor/index.json , /app/v120/anchor/room/url/26.json , /app/v120/anchor/living.json
        //$schedule->command("anchor_living_cache:run")->everyMinute();
        
        //主播定时任务
        //$schedule->command("anchor_index_cache:run all")->everyMinute();//每分钟静态化主播主页
//        $schedule->command("anchor_detail_cache:run all")->everyTenMinutes();//每10分钟静态化主播终端页
        //$schedule->command("anchor_json_cache:run")->everyMinute();//每分钟静态化队列里面的内容

//        $schedule->command("anchor_check_stream:run")->everyMinute();//每分钟检查主播的直播流是断开
        //$schedule->command("anchor_key_frame:run")->everyMinute();//每分钟获取直播的直播流的关键帧并且检测流是否存在


        //文章静态化定时任务
        $schedule->command("article_lives:run")->everyMinute();
        $schedule->command("article_page:run all")->everyFiveMinutes();
        $schedule->command("article_cache:run")->everyTenMinutes();

        $schedule->command('spider_ttzb:run')->hourlyAt(10);

        //shop接口
        $schedule->command('shop_living_json:run')->everyMinute();

        //百度主动推送，一小时一次
        $schedule->command('baidu_push:run all')->hourlyAt(20);

        $schedule->command('db_spread_cache:run')->everyTenMinutes();

        //$schedule->command('ttzb_player_json_cache:run')->cron('*/2 * * * *');//2分钟刷新一次天天直播的线路。

        //专题静态化
        //$schedule->command('subject_cover_sync:run')->everyFiveMinutes();//->everyMinute();//5分钟同步一次专题封面                               待优化
        //$schedule->command('subject_leagues_json:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题列表json           待优化
        $schedule->command('subject_detail_cache:run all')->everyFiveMinutes();//->everyMinute();//10分钟刷新一次专题终端              待优化
        $schedule->command('subject_player_cache:run')->everyFiveMinutes();//5分钟刷新一次专题列表player.html                      待优化

        //热门录像静态化
        //$schedule->command('hot_video_cover_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次热门视频封面同步
        //$schedule->command('hot_video_page_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次热门视频分页静态化

        //专题录像静态化
        //$schedule->command('subject_video_page_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题视频分页列表
        //$schedule->command('mobile_subject_video_page_cache:run')->everyFiveMinutes();//wap5分钟刷新一次专题视频分页列表

        //百度sitemap生成器，一天两次
        $schedule->command('generate:sitemap')->twiceDaily(1, 18);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
