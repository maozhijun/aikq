<?php
/**
 * Created by PhpStorm.
 * User: yaya
 * Date: 2018/1/21
 * Time: 20:45
 */

namespace App\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteExpireFileCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除过期的视频终端、线路内容的静态文件';

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
        $del_time = strtotime('-1 days');//24小时前的文件删除

        $this->delStorageFiles('/public/live/basketball', $del_time);
        $this->delStorageFiles('/public/live/football', $del_time);
        $this->delStorageFiles('/public/match/live/url/channel', $del_time);
    }

    /**
     * 删除更新时间小于 ex_time 的文件
     * @param $patch
     * @param $ex_time
     */
    protected function delStorageFiles($patch, $ex_time) {
        $files = Storage::files($patch);
        if (is_array($files)) {
            foreach ($files as $file) {
                $time = Storage::lastModified($file);
                if ($time < $ex_time) {
                    Storage::delete($file);
                }
            }
        }
    }

}