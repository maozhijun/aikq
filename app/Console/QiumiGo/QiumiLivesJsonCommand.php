<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console\QiumiGo;


use App\Http\Controllers\IntF\AikanQController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QiumiLivesJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qiumi_live_json_cache:run';

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
        $aiCon = new AikanQController();
        $jsonObj = $aiCon->livesJson(new Request())->getData();
        $server_output = json_encode($jsonObj);
        Storage::disk("public")->put("/static/json/lives.json", $server_output);
    }
}