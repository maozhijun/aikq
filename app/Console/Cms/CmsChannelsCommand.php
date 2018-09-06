<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/9/6
 * Time: 14:26
 */

namespace App\Console\Cms;


use App\Http\Controllers\IntF\KanQiuMaController;
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
        $matches = isset($json['matches']) ? $json['matches'] : [];
        foreach ($matches as $time=>$matchArray) {
            foreach ($matchArray as $match) {
                $channels = $match['channels'];
                $mid = $match['mid'];
                $sport = $match['sport'];
                $path = "www/json/cms/channels/$mid/$sport.json";
                Storage::disk('public')->put($path, json_encode($channels));
            }
        }
    }

}