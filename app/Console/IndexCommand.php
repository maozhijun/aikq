<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console;


use App\Http\Controllers\PC\Live\LiveController;
use App\Http\Controllers\PC\RecommendsController;
use App\Http\Controllers\PC\TaskController;
use App\Http\Controllers\PC\TopicController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class IndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index_cache:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'index缓存';

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
//        $home = new LiveController();
//        $home->staticIndex(new Request());
        $request = new Request();

        $type = $this->argument('type');
        switch ($type) {
            case "pc":
                break;
            case "mobile":
                $home = new \App\Http\Controllers\Mobile\Live\LiveController();
                $home->staticIndex($request);
                break;
            case "mip":
                $home = new \App\Http\Controllers\Mip\Live\LiveController();
                $home->staticIndex($request);
                break;
        }
    }
}