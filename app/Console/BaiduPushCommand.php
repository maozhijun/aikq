<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 17:14
 */

namespace App\Console;


use App\Models\Article\PcArticle;
use Illuminate\Console\Command;

class BaiduPushCommand extends Command
{
    const EACH_PUSH_COUNT = 10; //每次提交链接的数量

    const WWW_OFFSET = 0;
    const M_OFFSET = 1;
    const MIP_OFFSET = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baidu_push:run {type}';

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
        $type = $this->argument('type');

        switch ($type) {
            case "pc":
                $this->onPcHandler();
                break;
            case "mobile":
                $this->onMobileHandler();
                break;
            case "mip":
                $this->onMipHandler();
                break;
            case "all":
                $this->onPcHandler();
                $this->onMobileHandler();
                $this->onMipHandler();
                break;
        }
    }

    protected function onPcHandler(){
        $this->onItemHandler(self::WWW_OFFSET);
    }

    protected function onMipHandler() {
        $this->onItemHandler(self::MIP_OFFSET);
    }

    protected function onMobileHandler() {
        $this->onItemHandler(self::M_OFFSET);
    }

    protected function onItemHandler($offset) {
        $urls = array();
        $articles = PcArticle::query()
            ->where('status', 1)
            ->whereRaw("!(is_baidu_push >> $offset & 1)")
            ->orderBy(PcArticle::CREATED_AT, 'desc')->take(self::EACH_PUSH_COUNT)->get();

        $host = $this->getHostByOffset($offset);
        foreach ($articles as $article) {
            $urls[] = $host.$article->getUrl();
        }

        dump($urls);
        if (count($urls) <= 0) {
            echo "$host : there is nothing to push<br>";
            return;
        }

        $resultCount = $this->onBaiduPush($urls, $offset);
//        $resultCount = 10;
        if ($resultCount > 0) {
            foreach ($articles as $article) {
                try {
                    $article->is_baidu_push = $article->is_baidu_push | 1 << $offset;
                    $article->save();

                    echo "$host : article_id = $article->id save success. <br>";
                } catch (\Exception $e) {
                    echo "$host : article_id = $article->id save failed. <br>";
                }
            }
        }
    }

    protected function onBaiduPush(array $urls, $offset) {
        $host = $this->getHostByOffset($offset);
        if (strlen($host) < 0 && !str_contains("http", $host)) {
            echo "host of $host is error! <br>";
            return -1;
        }
        $host = str_replace("https://", "", $host);
        $host = str_replace("http://", "", $host);
        if (count($urls) <= 0) {
            echo "the host of $host is nothing to push! <br>";
            return -1;
        }

        $token = $this->getBaiduTokenByOffset($offset);
        echo "host = $host; token = $token <br>";
        if ($offset == self::MIP_OFFSET) {
            $api = "http://data.zz.baidu.com/urls?site=$host&token=$token&type=mip";
        } else {
            $api = "http://data.zz.baidu.com/urls?site=$host&token=$token";
        }
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
        echo "baidu_push_result:".$result.'<br>';
        $data = json_decode($result, true);
        if (is_array($data) && (array_key_exists("success", $data) || array_key_exists("success_mip", $data))) {
            if (array_key_exists("success_mip", $data)) {
                return $data['success_mip'];
            }
            return $data['success'];
        }
        return -1;
    }

    protected function getHostByOffset($offset) {
        $host = "";
        switch ($offset) {
            case self::MIP_OFFSET:
                $host = env('MIP_URL');
                break;
            case self::M_OFFSET:
                $host = env('M_URL');
                break;
            case self::WWW_OFFSET:
                $host = env('WWW_URL');
                break;
        }
        return $host;
    }

    protected function getBaiduTokenByOffset($offset) {
        $token = "";
        switch ($offset) {
            case self::MIP_OFFSET:
                $token = env('MIP_BAIDU_PUSH_TOKEN');
                break;
            case self::M_OFFSET:
                $token = env('M_BAIDU_PUSH_TOKEN');
                break;
            case self::WWW_OFFSET:
                $token = env('WWW_BAIDU_PUSH_TOKEN');
                break;
        }
        return $token;
    }
}