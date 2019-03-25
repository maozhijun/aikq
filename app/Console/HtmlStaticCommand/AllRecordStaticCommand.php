<?php

namespace App\Console\HtmlStaticCommand;

use App\Models\Subject\SubjectVideo;

class AllRecordStaticCommand extends BaseCommand
{

    protected function command_name()
    {
        return "all_record_static";
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

        $records = SubjectVideo::query()->orderBy('created_at', 'desc')->offset($offset)->take($count)->get();

        switch ($type) {
            case "pc":
                $this->onPcHandler2($records);
                break;
            case "mobile":
                $this->onMobileHandler2($records);
                break;
            case "mip":
                break;
            case "all":
                $this->onPcHandler2($records);
                $this->onMobileHandler2($records);
                break;
        }
    }

    protected function onPcHandler2($records)
    {
        foreach ($records as $video) {
            $url = "http://cms.aikanqiu.com/static/record/".$video->id;
            echo "$url<br>";
            $code = self::getUrlCode($url);
            if ($code != 200) {
                echo "static error for id = $video->id <br>";
            }
        }
    }

    protected function onMobileHandler2($records)
    {
        foreach ($records as $video) {
            $url = "http://cms.aikanqiu.com/m/static/record/".$video->id;
            echo "$url<br>";
            $code = self::getUrlCode($url);
            if ($code != 200) {
                echo "static error for id = $video->id <br>";
            }
        }
    }
}