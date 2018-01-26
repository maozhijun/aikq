<?php

namespace App\Console;

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
        IndexCommand::class,
        LiveDetailCommand::class,
        PlayerJsonCommand::class,
        DeleteExpireFileCommand::class,
        MobileDetailCommand::class,
        LivesJsonCommand::class,
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
        $schedule->command('live_detail_cache:run')->everyFiveMinutes();//每五分钟刷新终端缓存
        $schedule->command('player_json_cache:run')->everyFiveMinutes();//五分钟刷新一次正在直播的比赛的线路内容
        $schedule->command('delete_cache:run')->dailyAt('07:00');//每天删除一次文件

        $schedule->command('mobile_detail_cache:run')->everyFiveMinutes();//每五分钟刷新移动直播终端缓存

        $mController = new LiveController();
        $schedule->call(function() use($mController){
            $mController->matchLiveStatic(new Request());//每分钟刷新比赛状态数据
        })->everyMinute();
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
