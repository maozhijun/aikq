<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/3
 * Time: 11:56
 */

namespace App\Console;


use App\Http\Controllers\IntF\DongQiuZhiBoController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
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
        $dqCon = new DongQiuZhiBoController();
        $server_output = $dqCon->matchList(new Request());
        if (!empty($server_output))
            Storage::disk("public")->put("/db/spread/matchList.html", $server_output);

        //json版
        $server_output = $dqCon->matchListJson(new Request())->getData();
        $server_output = json_encode($server_output);
        if (!empty($server_output))
            Storage::disk("public")->put("/db/spread/matchList.json", $server_output);
    }

}