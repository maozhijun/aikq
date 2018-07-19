<?php

namespace App\Console;

use App\Console\Anchor\CheckStreamCommand;
use App\Console\Anchor\StreamKeyFrameCommand;
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
        IndexCommand::class,//直播 列表静态化
        LiveDetailCommand::class,//PC终端、移动终端html缓存。

        PlayerJsonCommand::class,//静态化赛前1小时和正在比赛的 线路
        NoStartPlayerJsonCommand::class,//静态化 赛前1小时前未开始的 线路

        DeleteExpireFileCommand::class,//删除过期文件

        LivesJsonCommand::class,//列表json静态化
        DBSpreadCommand::class,
        TTzbPlayerJsonCommand::class,//天天直播 （开始前1小时 - 开始后 3小时的比赛） 线路静态化

        DetailCommand::class,//subject 专题终端静态化
        LeaguesJsonCommand::class,//subject 专题列表json静态化
        PlayerCommand::class,//subject 专题播放终端静态化

        VideoPageCommand::class,//热门录像分页列表静态化
        VideoCoverCommand::class,//热门录像封面图同步
        SubjectVideoDetailCommand::class,//专题录像终端静态化

        CoverCommand::class,//专题封面同步
        SubjectVideoCoverCommand::class,//专题录像 封面图同步到本机
        SubjectVideoPageCommand::class,//专题录像 静态化分页列表
        PlayerCommand::class,//录像player静态化
        MobileSubjectVideoPageCommand::class,//专题录像 wap 列表/终端/线路 json静态化
        FIFACommand::class,//世界杯

        //同步数据相关 开始
        FootballMatchCommand::class,//同步matches数据到爱看球
        FootballMatchUpdateCommand::class,//更新matches数据到爱看球

        BasketballMatchCommand::class,//同步basket_matches数据到爱看球
        BasketballUpdateMatchCommand::class,//更新basket_matches数据到爱看球
        //同步数据相关 结束

        //主播相关定时任务
        CheckStreamCommand::class,//检查主播流是否在直播，没直播的则修改为 结束直播状态
        StreamKeyFrameCommand::class,//获取正在直播的主播直播流的关键帧
        SocketScoreCacheCommand::class,
        AnchorLivingCacheCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
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

        $schedule->command('ttzb_player_json_cache:run')->cron('*/2 * * * *');//2分钟刷新一次天天直播的线路。

        //专题静态化
        $schedule->command('subject_cover_sync:run')->everyFiveMinutes();//->everyMinute();//5分钟同步一次专题封面
        $schedule->command('subject_leagues_json:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题列表json
        $schedule->command('subject_detail_cache:run')->everyFiveMinutes();//->everyMinute();//10分钟刷新一次专题终端
        $schedule->command('subject_player_cache:run')->everyFiveMinutes();//5分钟刷新一次专题列表player.html

        //热门录像静态化
        //$schedule->command('hot_video_cover_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次热门视频封面同步
        //$schedule->command('hot_video_page_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次热门视频分页静态化

        //专题录像静态化
        $schedule->command('subject_video_cover_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题视频封面同步
        $schedule->command('subject_video_page_cache:run')->everyFiveMinutes();//->everyMinute();//5分钟刷新一次专题视频分页列表
        $schedule->command('mobile_subject_video_page_cache:run')->everyFiveMinutes();//wap5分钟刷新一次专题视频分页列表

        //世界杯
        //$schedule->command('fifa_cache:run')->everyMinute();

        //主播定时任务
        $schedule->command("anchor_check_stream:run")->everyMinute();//每分钟检查主播的直播流是断开
        $schedule->command("anchor_key_frame:run")->everyFiveMinutes();//每5分钟获取直播的直播流的关键帧

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
