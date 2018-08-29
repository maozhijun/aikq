<?php

namespace App\Console\HtmlStaticCommand;

use App\Http\Controllers\PC\Live\LiveController;
use Illuminate\Http\Request;

class IndexCommand extends BaseCommand
{

    protected function command_name()
    {
        return "index_cache";
    }

    protected function description()
    {
        return "index缓存";
    }

    protected function onPcHandler(Request $request)
    {
        $home = new LiveController();
        $home->staticIndex($request);
    }

    protected function onMobileHandler(Request $request)
    {
        $home = new \App\Http\Controllers\Mobile\Live\LiveController();
        $home->staticIndex($request);
    }

    protected function onMipHandler(Request $request)
    {
        $home = new \App\Http\Controllers\Mip\Live\LiveController();
        $home->staticIndex($request);
    }
}