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

        $type = $this->argument('type');
        $key = self::CACHE_KEY.$type;
        $lastId = Redis::get($key);
dump("last = ".$lastId);
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
            case "all":
                foreach ($channels as $channel) {
                    $con->staticSubjectVideoDetailPc($channel);
                    $con->staticSubjectVideoDetailM($channel);
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