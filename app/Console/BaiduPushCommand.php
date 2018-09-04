<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console;


use Illuminate\Console\Command;

class BaiduPushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baidu_push:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '百度收录主动推送';

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
        $urls = array();
//        $jsonMatches = CommonTool::getLivesJson()["matches"];
//        foreach ($jsonMatches as $date=> $matches) {
//            if ($date == date("Y-m-d")) {
//                foreach ($matches as $match) {
//                    $isMatching = $match['status'] > 0 || (isset($match['isMatching']) && $match['isMatching']);
//                    if ($isMatching > 0) {
//                        $urls[] = "http://www.quanqiutiyu.cc" . UrlCommonTool::liveDetailUrl($match);
//                    }
//                }
//            }
//        }

        if (count($urls) <= 0) {
            echo "there is nothing to push! <br>";
            return;
        }

        $api = 'http://data.zz.baidu.com/urls?site=www.aikq.cc&token=JeIeGrFfPCqmMdRL';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
    }
}