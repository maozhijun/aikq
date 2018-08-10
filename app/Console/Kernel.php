<?php

namespace App\Console;

use App\Console\Anchor\AnchorDetailCommand;
use App\Console\Anchor\AnchorIndexCommand;
use App\Console\Anchor\AnchorJsonCommand;
use App\Console\Anchor\CheckStreamCommand;
use App\Console\Anchor\StreamKeyFrameCommand;
use App\Console\Article\ArticleLiveCellCommands;
use App\Console\Article\ArticlePageCommands;
use App\Console\HotVideo\VideoCoverCommand;
use App\Console\Subject\CoverCommand;
use App\Console\Subject\DetailCommand;
use App\Console\Subject\LeaguesJsonCommand;
use App\Console\Subject\PlayerCommand;
use App\Console\HotVideo\VideoPageCommand;
use App\Console\SubjectVideo\MobileSubjectVideoPageCommand;
use App\Console\SubjectVideo\SubjectVideoCoverCommand;
use App\Console\SubjectVideo\SubjectVideoDetailCommand;
use App\Console\SubjectVideo\SubjectVideoPageCommand;
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

        PlayerJsonCommand::class,//静态化赛前1小时和正在比赛的 线路         DB
        NoStartPlayerJsonCommand::class,//静态化 赛前1小时前未开始的 线路   DB

        DeleteExpireFileCommand::class,//删除过期文件

        LivesJsonCommand::class,//列表json静态化               DB
        DBSpreadCommand::class,//懂球、柠檬分享页
        TTzbPlayerJsonCommand::class,//天天直播 （开始前1小时 - 开始后 3小时的比赛） 线路静态化

        DetailCommand::class,//subject 专题终端静态化           DB
        LeaguesJsonCommand::class,//subject 专题列表json静态化  DB

        VideoPageCommand::class,//热门录像分页列表静态化        DB
        VideoCoverCommand::class,//热门录像封面图同步

        CoverCommand::class,//专题封面同步
        SubjectVideoCoverCommand::class,//专题录像 封面图同步到本机
        SubjectVideoPageCommand::class,//专题录像 静态化分页列表          DB
        PlayerCommand::class,//录像player静态化                           NO不需要修改
        MobileSubjectVideoPageCommand::class,//专题录像 wap 列表/终端/线路 json静态化    DB


        FIFACommand::class,//世界杯

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
        SocketScoreCacheCommand::class,
        AnchorLivingCacheCommand::class,
        AnchorIndexCommand::class,//主播首页定时任务
        AnchorDetailCommand::class,//主播终端定时任务
        AnchorJsonCommand::class,//主播播放json定时任务

        ArticlePageCommands::class,//文章分页定时任务
        ArticleLiveCellCommands::class,//文章直播单元静态化定时任务
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //足球、篮球比赛 数据同步 开始
        $schedule->command('sync_update_football_matches:run')->everyMinute();
        $schedule->command('sync_update_basketball_matches:run')->everyMinute();
        $schedule->command('sync_live_matches:run')->everyMinute();
        //足球、篮球比赛 数据同步 结束

        $schedule->command('live_json_cache:run')->everyMinute();//每分钟刷新一次赛事缓存
        $schedule->command('index_cache:run')->everyMinute();//每分钟刷新主页缓存
        $schedule->command('live_detail_cache:run')->everyFiveMinutes();//每5分钟刷新终端缓存

        $schedule->command('player_json_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次正在直播的比赛的线路内容
        $schedule->command('ns_player_json_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次未开始比赛的线路内容 一小时内执静态化所有的json

        $schedule->command('delete_cache:run')->dailyAt('07:00');//每天删除一次文件

        //$schedule->command('mobile_detail_cache:run')->everyMinute();//每五分钟刷新移动直播终端缓存

        $mController = new LiveController();
        $schedule->call(function() use($mController){
            $mController->matchLiveStatic(new Request());//每分钟刷新比赛状态数据
        })->everyMinute();

        $schedule->command('db_spread_cache:run')->everyTenMinutes();

        //$schedule->command('ttzb_player_json_cache:run')->cron('*/2 * * * *');//2分钟刷新一次天天直播的线路。

        //专题静态化
        //$schedule->command('subject_cover_sync:run')->everyFiveMinutes();//->everyMinute();//5分钟同步一次专题封面
        $schedule->command('subject_leagues_json:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题列表json
        $schedule->command('subject_detail_cache:run')->everyFiveMinutes();//->everyMinute();//10分钟刷新一次专题终端
        $schedule->command('subject_player_cache:run')->everyFiveMinutes();//5分钟刷新一次专题列表player.html

        //热门录像静态化
        //$schedule->command('hot_video_cover_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次热门视频封面同步
        //$schedule->command('hot_video_page_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次热门视频分页静态化

        //专题录像静态化
        //$schedule->command('subject_video_cover_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题视频封面同步
        $schedule->command('subject_video_page_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题视频分页列表
        $schedule->command('mobile_subject_video_page_cache:run')->everyFiveMinutes();//wap5分钟刷新一次专题视频分页列表

        //appsocket相关
        $schedule->command("socket_score_cache:run")->everyFiveMinutes();//每2分钟检查正在直播的比分变化
        $schedule->command("anchor_living_cache:run")->everyTenMinutes();//每分钟看看有多少主播在播

        //主播定时任务
        $schedule->command("anchor_index_cache:run")->everyMinute();//每分钟静态化主播主页
        $schedule->command("anchor_detail_cache:run")->everyTenMinutes();//每10分钟静态化主播终端页
        $schedule->command("anchor_json_cache:run")->everyMinute();//每分钟静态化主播播放链接

        $schedule->command("anchor_check_stream:run")->everyMinute();//每分钟检查主播的直播流是断开
        $schedule->command("anchor_key_frame:run")->everyFiveMinutes();//每5分钟获取直播的直播流的关键帧


        //文章静态化定时任务
        $schedule->command("article_lives:run")->everyMinute();
        $schedule->command("article_page:run")->everyFiveMinutes();
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
