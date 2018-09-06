<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/9/6
 * Time: 14:26
 */

namespace App\Console\Cms;


use App\Http\Controllers\IntF\KanQiuMaController;
use App\Http\Controllers\PC\Live\LiveController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsChannelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms_channels_json:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "正在直播的json列表";

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
        $kqmCon = new KanQiuMaController();
        $json = $kqmCon->livesJson(new Request())->getData();
        $json = json_encode($json);
        $json = json_decode($json, true);
//        $cache = Storage::get('/public/static/json/lives.json');
//        $json = json_decode($cache, true);
//        if (is_null($json)){
//            dump("无数据");
//            return;
//        }
        $matches = isset($json['matches']) ? $json['matches'] : [];
        dump(count($matches));

        foreach ($matches as $time=>$matchArray) {
            foreach ($matchArray as $match) {
                $channels = $match['channels'];
                $mid = $match['mid'];
                $sport = $match['sport'];
                $path = "www/json/cms/channels/$mid/$sport.json";
                dump($path);
                Storage::disk('public')->put($path, json_encode(['code'=>0, 'channels'=>$channels]));
            }
        }
    }

}