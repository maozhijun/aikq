<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console;


use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Live\LiveController;
use App\Http\Controllers\PC\RecommendsController;
use App\Http\Controllers\PC\TaskController;
use App\Http\Controllers\PC\TopicController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivesJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live_json_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '直播赛事缓存';

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
        $home = new LiveController();
        $home->allLiveJsonStatic(new Request());


        //wap json 静态化
        $aiCon = new AikanQController();
        $jsonObj = $aiCon->livesJson(new Request(), true)->getData();
        $server_output = json_encode($jsonObj);
        Storage::disk("public")->put("static/json/m/lives.json", $server_output);
    }
}