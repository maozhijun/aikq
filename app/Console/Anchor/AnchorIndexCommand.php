<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/27
 * Time: 10:50
 */

namespace App\Console\Anchor;


use App\Http\Controllers\PC\Anchor\AnchorController;
use App\Http\Controllers\PC\HomeController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnchorIndexCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_index_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '主播列表静态化';

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
    public function handle() {
        $con = new AnchorController();
        $html = $con->index(new Request());
        if (!empty($html)) {
            Storage::disk('public')->put('static/anchor/index.html', $html);
        }

        $wapCon = new \App\Http\Controllers\Mobile\Anchor\AnchorController();
        $wapHtml = $wapCon->index(new Request());
        if (!empty($wapHtml)) {
            Storage::disk('public')->put('m/anchor/index.html', $wapHtml);
        }

        //app正在直播主播列表
        $controller = new HomeController();
        $data = $controller->appConfivV110p();
        $data = json_encode($data);
        if ($data && strlen($data) > 0){
            Storage::disk('public')->put('app/v110/config.json', $data);
        }

        //app正在直播主播列表
        $data = $controller->appConfivV120p();
        $data = json_encode($data);
        if ($data && strlen($data) > 0){
            Storage::disk('public')->put('app/v120/config.json', $data);
        }
    }

}