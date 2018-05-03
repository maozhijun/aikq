<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/3
 * Time: 11:56
 */

namespace App\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DBSpreadCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db_spread_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对外接口增加html缓存';

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
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/spread/api/matchList.html";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code >= 400 && $code <= 599) {
            return;
        }
        $server_output = str_replace('料狗','爱看球',$server_output);
        $server_output = str_replace('https://www.liaogou168.com/img/ico.ico','//static.cdn.dlfyb.com/img/pc/ico.ico',$server_output);

        Storage::disk("public")->put("/db/spread/matchList.html", $server_output);
    }

}