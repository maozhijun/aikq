<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/9/11
 * Time: 12:25
 */

namespace App\Console\Download;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download_html:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '下载页面静态化';

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
        $pcPath = public_path('download.html');
        $wapPath = public_path('downloadPhone.html');

        $pcContent = file_get_contents($pcPath);
        $wapContent = file_get_contents($wapPath);

        Storage::disk('public')->put('www/download/index.html', $pcContent);
        Storage::disk('public')->put('m/download/index.html', $wapContent);
        Storage::disk('public')->put('mip/download/index.html', $wapContent);
    }

}