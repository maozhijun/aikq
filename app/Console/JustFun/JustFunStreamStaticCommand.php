<?php
namespace App\Console\JustFun;

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/17
 * Time: 10:57
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class JustFunStreamStaticCommand extends Command
{
    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        $this->signature = 'justfun_static:stream {cid}';
        $this->description = "根据房间号静态化播放流地址";

        parent::__construct();
    }

    /**
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        $cid = $this->argument('cid');

        $url = "http://m.justfun.live/tv/$cid";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if ($code >= 400) {
            echo "url=$url 获取链接内容失败\n";
            return;
        }

        //m3u8播放地址
        preg_match("/<video _src='(.*?)' class=/is", $server_output, $matches);
        if (isset($matches) && isset($matches[1])) {
            $m3u8Url =  $matches[1];
            dump($m3u8Url);
        }

        //获取实际房间号
        preg_match("/fengyuncid: \"(.*?)\",/is", $server_output, $matches);
        if (isset($matches) && isset($matches[1])) {
            $fengyunCid =  $matches[1];

            $url = "http://www.justfun.live/live-channel-info/channel/info?cid=$fengyunCid";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $server_output = curl_exec ($ch);
            curl_close ($ch);

            $data = json_decode($server_output, true);

            $vipInfo = $this->base64Decode($data['vipPlayInfo']);
            while (!ends_with($vipInfo, "}")) {
                $vipInfo = substr($vipInfo, 0, strlen($vipInfo) - 1);
            }
            if (strlen($vipInfo) > 0) {
                $vipInfo = json_decode($vipInfo, true);
                dump($vipInfo);
            }
        }
    }

    private function base64Decode($vipInfo) {
        $testInfo = base64_decode($vipInfo);

        $testArray = [];
        for ($i = 0; $i < strlen($testInfo); $i++) {
            $testArray[$i] = ord(substr($testInfo, $i, 1));
        }
        $testArray = $this->decrypt($testArray);

        $testStr = "";
        for ($i = 0; $i < count($testArray); $i++) {
            $testStr .= chr($testArray[$i]);
        }
        return $testStr;
    }

    private function decrypt($arg1)
    {
        $loc1 =null;
        $loc2 =null;
        $loc3 =null;
        $loc4 =null;
        $loc5 =0;
        $loc6 =null;
        $loc7 =0;
        $loc8 =count($arg1);
        if (count($arg1) > 12)
        {
            if ($arg1[0] == 255 && $arg1[1] == 255 && $arg1[2] == 255 && $arg1[3] == 254)
            {
                $loc1 = $arg1[4];
                $loc2 = $arg1[5];
                $loc3 = $arg1[6];
                $loc4 = $arg1[7];
                if (($loc5 = ($arg1[$loc3 + 8] & 255 ^ $loc1) << 24 | ($arg1[$loc3 + 9] & 255 ^ $loc2) << 16 | ($arg1[$loc3 + 10] & 255 ^ $loc1) << 8 | $arg1[$loc3 + 11] & 255 ^ $loc2) == count($arg1) - 12 - $loc3 - $loc4)
                {
                    $loc6 = [];
                    $loc7 = $loc3 + 12;
                    --$loc8;
                    while ($loc8 >= 0)
                    {
                        if ($loc7 + $loc8 < count($arg1)) {
                            $loc6[$loc8] = $arg1[$loc7 + $loc8] ^ (($loc8 & 1) != 0 ? $loc2 : $loc1);
                        }
                        --$loc8;
                    }
                    return $loc6;
                }
            }
        }
        return [];
    }
}