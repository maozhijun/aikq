<?php

namespace App\Console\HtmlStaticCommand;

use App\Models\Match\HotVideo;

class AllVideoStaticCommand extends BaseCommand
{

    protected function command_name()
    {
        return "all_video_static";
    }

    protected function description()
    {
        return "index缓存";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');

        list($type, $count, $offset) = explode('_', $type);

        $videos = HotVideo::query()->where('show', HotVideo::kShow)->orderBy('created_at', 'desc')->offset($offset)->take($count)->get();

        switch ($type) {
            case "pc":
                $this->onPcHandler2($videos);
                break;
            case "mobile":
                $this->onMobileHandler2($videos);
                break;
            case "mip":
                break;
            case "all":
                $this->onPcHandler2($videos);
                $this->onMobileHandler2($videos);
                break;
        }
    }

    protected function onPcHandler2($videos)
    {
        foreach ($videos as $video) {
            $url = "http://cms.aikanqiu.com/static/record/".$video->id;
            echo "$url<br>";
            $code = self::getUrlCode($url);
            if ($code != 200) {
                echo "static error for id = $video->id <br>";
            }
        }
    }

    protected function onMobileHandler2($videos)
    {
        foreach ($videos as $video) {
            $url = "http://cms.aikanqiu.com/m/static/record/".$video->id;
            echo "$url<br>";
            $code = self::getUrlCode($url);
            if ($code != 200) {
                echo "static error for id = $video->id <br>";
            }
        }
    }
}