<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console\QiumiGo;


use Illuminate\Console\Command;
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
        //pc端的
        $url = 'http://cms.aikanqiu.com/json/lives.json?time=' . time();
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $lines_string = curl_exec($ch);
        curl_close($ch);

        if ($lines_string && strlen($lines_string) > 0) {
            Storage::disk("public")->put("/static/json/lives.json", $lines_string);
        }

        //web端的
        $url = 'http://cms.aikanqiu.com/m/json/lives.json?time=' . time();
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $lines_string = curl_exec($ch);
        curl_close($ch);

        if ($lines_string && strlen($lines_string) > 0) {
            Storage::disk("public")->put("/static/m/json/lives.json", $lines_string);
        }
    }
}