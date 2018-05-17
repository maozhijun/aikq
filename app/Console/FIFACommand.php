<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console;


use App\Http\Controllers\PC\FIFA\WorldCupController;
use App\Http\Controllers\PC\Live\LiveController;
use App\Http\Controllers\PC\RecommendsController;
use App\Http\Controllers\PC\TaskController;
use App\Http\Controllers\PC\TopicController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class FIFACommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fifa_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '世界杯首页,手机和pc都是这个,主要是index';

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
        $tids = [
            //A
            ['tid'=>578,'win_tid'=>735,'name'=>'埃及'],
            ['tid'=>586,'win_tid'=>746,'name'=>'俄罗斯'],
            ['tid'=>607,'win_tid'=>767,'name'=>'乌拉圭'],
            ['tid'=>716,'win_tid'=>891,'name'=>'沙地阿拉伯'],
            //B
            ['tid'=>605,'win_tid'=>765,'name'=>'葡萄牙'],
            ['tid'=>612,'win_tid'=>772,'name'=>'西班牙'],
            ['tid'=>622,'win_tid'=>783,'name'=>'伊朗'],
            ['tid'=>646,'win_tid'=>813,'name'=>'摩洛哥'],
            //C
            ['tid'=>509,'win_tid'=>638,'name'=>'丹麦'],
            ['tid'=>518,'win_tid'=>649,'name'=>'法国'],
            ['tid'=>614,'win_tid'=>774,'name'=>'秘鲁'],
            ['tid'=>732,'win_tid'=>913,'name'=>'澳大利亚'],
            //D
            ['tid'=>596,'win_tid'=>756,'name'=>'冰岛'],
            ['tid'=>606,'win_tid'=>766,'name'=>'阿根廷'],
            ['tid'=>608,'win_tid'=>768,'name'=>'克罗地亚'],
            ['tid'=>626,'win_tid'=>789,'name'=>'尼日利亚'],
            //E
            ['tid'=>512,'win_tid'=>642,'name'=>'塞尔维亚'],
            ['tid'=>517,'win_tid'=>648,'name'=>'瑞士'],
            ['tid'=>618,'win_tid'=>778,'name'=>'巴西'],
            ['tid'=>733,'win_tid'=>914,'name'=>'哥斯达黎加'],
            //F
            ['tid'=>513,'win_tid'=>644,'name'=>'瑞典'],
            ['tid'=>519,'win_tid'=>650,'name'=>'德国'],
            ['tid'=>651,'win_tid'=>819,'name'=>'墨西哥'],
            ['tid'=>721,'win_tid'=>898,'name'=>'韩国'],
            //G
            ['tid'=>514,'win_tid'=>645,'name'=>'比利时'],
            ['tid'=>584,'win_tid'=>744,'name'=>'英格兰'],
            ['tid'=>634,'win_tid'=>798,'name'=>'巴拿马'],
            ['tid'=>653,'win_tid'=>823,'name'=>'突尼斯'],
            //H
            ['tid'=>508,'win_tid'=>637,'name'=>'波兰'],
            ['tid'=>615,'win_tid'=>775,'name'=>'哥伦比亚'],
            ['tid'=>648,'win_tid'=>815,'name'=>'塞内加尔'],
            ['tid'=>726,'win_tid'=>903,'name'=>'日本'],
        ];

        //1小时一次wap的rank
        $min = date('i');
        $hour = date('H');

        if ($min == 51){
            $this->staticUrl(asset('/m/static/worldcup/rank'));
        }
        $this->staticUrl(asset('/m/static/worldcup/index'));
        $this->staticUrl(asset('/pc/static/worldcup/index'));
        if ($min%5 == 0) {
            //5分钟一次资讯
            $this->staticUrl(asset('/m/static/worldcup/topic'));
        }
        if (($hour > 21 || $hour < 6) && $min%15 == 0){
            //15分钟一次球队终端,一次8个,一小时刷新全部一次
            for ($i = 0 ; $i < 8 ;$i++){
                $team = $tids[$min/15 + $i];
                $this->staticUrl(asset('/m/static/worldcup/team/'.$team['tid']));
                $this->staticUrl(asset('/pc/static/worldcup/team/'.$team['tid']));
            }
        }
    }

    private function staticUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }
}