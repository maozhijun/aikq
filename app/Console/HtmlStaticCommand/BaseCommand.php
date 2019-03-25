<?php

namespace App\Console\HtmlStaticCommand;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

abstract class BaseCommand extends Command
{
    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        $this->signature = $this->command_name().':run {type}';
        $this->description = $this->description();

        parent::__construct();
    }

    protected abstract function command_name();

    protected abstract function description();

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $request = new Request();

        $type = $this->argument('type');

        switch ($type) {
            case "pc":
                $this->onPcHandler($request);
                break;
            case "mobile":
                $this->onMobileHandler($request);
                break;
            case "mip":
                $this->onMipHandler($request);
                break;
            case "all":
                $this->onPcHandler($request);
                $this->onMobileHandler($request);
                $this->onMipHandler($request);
                break;
        }
        $this->onCommonHandler($request);
    }

    protected function onPcHandler(Request $request) {

    }

    protected function onMobileHandler(Request $request) {

    }

    protected function onMipHandler(Request $request){

    }

    protected function onCommonHandler(Request $request) {

    }

    /**
     * 获取url返回码
     * @param $url
     * @param int $timeout
     * @return mixed
     */
    public static function getUrlCode($url, $timeout = 5) {
        $isHttps = preg_match('/^https:/', $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//8秒超时

        // 返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
        curl_setopt($ch, CURLOPT_HEADER, true);
        // 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要
        //正文
        curl_setopt($ch, CURLOPT_NOBODY, true);
        if ($isHttps) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        return $code;
    }
}