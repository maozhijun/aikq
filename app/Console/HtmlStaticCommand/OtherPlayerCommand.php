<?php

namespace App\Console\HtmlStaticCommand;

use Illuminate\Support\Facades\Storage;

class OtherPlayerCommand extends BaseCommand
{

    protected function command_name()
    {
        return "other_player";
    }

    protected function description()
    {
        return "其他播放器";
    }

    public function handle()
    {
        $type = $this->argument('type');
        $player = "";
        switch ($type) {
            case "justfun":
                $player = $this->getJustFunPlayer();
                break;
            case "android":
                $player = $this->getJustFunAndroidUtil();
                break;
        }
        if (strlen($player) > 0) {
            Storage::disk("public")->put("/www/live/player/$type.html", $player);
        }
    }

    private function getJustFunPlayer()
    {
        return view('pc.live.justfun_player', array('cdn' => env('CDN_URL'), 'host' => 'www.aikanqiu.com'));
    }

    private function getJustFunAndroidUtil()
    {
        return view('pc.live.justfun_android', array('cdn' => env('CDN_URL'), 'host' => 'www.aikanqiu.com'));
    }
}