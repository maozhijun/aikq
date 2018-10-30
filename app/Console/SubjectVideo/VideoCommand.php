<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\SubjectVideo;

use App\Console\HtmlStaticCommand\BaseCommand;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Models\Match\BasketMatch;
use App\Models\Match\Match;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Support\Facades\Redis;

class VideoCommand extends BaseCommand
{
    const CACHE_KEY = "VideoCommand_CACHE_KEY";

    protected function command_name()
    {
        return "subject_video_cache";
    }

    protected function description()
    {
        return "其他播放器";
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '静态化专题录像终端';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        $videos = SubjectVideo::query()->where(function ($orQuery) {
//            $orQuery->whereNull("hid");
//            $orQuery->orWhereNull("aid");
//        })->get();
//        foreach ($videos as $video) {
//            $sport = $video['sport'];
//            $mid = $video['mid'];
//
//            if ($sport == 1) {
//                $match = \App\Models\LgMatch\Match::query()->find($mid);
//            } else if ($sport == 2) {
//                $match = \App\Models\LgMatch\BasketMatch::query()->find($mid);
//            }
//            if (isset($match)) {
//                $hid = $match->hid;
//                $aid = $match->aid;
//                $video->hid = $hid;
//                $video->aid = $aid;
//                $video->save();
//            }
//            dump($video->id);
//        }
//        return;
        $type = $this->argument('type');
        $key = self::CACHE_KEY.$type;
        $lastId = Redis::get($key);
        $query = SubjectVideoChannels::query();
        $query->orderBy('id');
        if (!empty($lastId)) {
            $query->where('id', '>', $lastId);
        }
        $channels = $query->take(100)->get();

        $con = new SubjectController();
        switch ($type) {
            case "pc":
                foreach ($channels as $channel) {
                    $con->staticSubjectVideoDetailPc($channel);
                    $lastId = $channel->id;
                    dump("Id ：" . $lastId);
                }
                break;
            case "mobile":
                foreach ($channels as $channel) {
                    $con->staticSubjectVideoDetailM($channel);
                    $lastId = $channel->id;
                    dump("Id ：" . $lastId);
                }
                break;
            case "mip":
                foreach ($channels as $channel) {
                    $con->staticSubjectVideoDetailMip($channel);
                    $lastId = $channel->id;
                    dump("Id ：" . $lastId);
                }
                break;
            case "all":
                foreach ($channels as $channel) {
                    $con->staticSubjectVideoDetailPc($channel);
                    $con->staticSubjectVideoDetailM($channel);
                    $con->staticSubjectVideoDetailMip($channel);
                    $lastId = $channel->id;
                    dump("Id ：" . $lastId);
                }
                break;
        }

        if (count($channels) == 0) {
            $lastId = "";
        }
        Redis::set($key, $lastId);
        dump("last id ：".$lastId . " count ：".count($channels));
    }



}