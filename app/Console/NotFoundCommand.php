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
use Illuminate\Support\Facades\Storage;

class NotFoundCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'not_found_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '404页面,或者之前的页面';

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
        $html = view('mobile.404');
        if (isset($html) && strlen($html) > 0) {
            Storage::disk("public")->put("/m/404.html", $html);
            Storage::disk("public")->put("/mip/404.html", $html);
        }
        $html = view('pc.404');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/www/404.html", $html);
    }
}