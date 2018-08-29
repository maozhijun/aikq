<?php

namespace App\Console\HtmlStaticCommand\Anchor;

use App\Console\HtmlStaticCommand\BaseCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnchorIndexCommand extends BaseCommand
{
    protected function command_name()
    {
        return "anchor_index_cache";
    }

    protected function description()
    {
        return "主播列表静态化";
    }

    protected function onPcHandler(Request $request)
    {
        $con = new \App\Http\Controllers\PC\Anchor\AnchorController();
        $html = $con->index(new Request());
        if (!empty($html)) {
            Storage::disk('public')->put('www/anchor/index.html', $html);
        }
    }

    protected function onMobileHandler(Request $request)
    {
        $wapCon = new \App\Http\Controllers\Mobile\Anchor\AnchorController();
        $wapCon->indexStatic($request);

        //app正在直播主播列表
        $controller = new \App\Http\Controllers\PC\HomeController();
        $data = $controller->appConfivV110p();
        $data = json_encode($data);
        if ($data && strlen($data) > 0){
            Storage::disk('public')->put('app/v110/config.json', $data);
        }

        //app正在直播主播列表
        $data = $controller->appConfivV120p();
        $data = json_encode($data);
        if ($data && strlen($data) > 0){
            Storage::disk('public')->put('app/v120/config.json', $data);
        }
    }

    protected function onMipHandler(Request $request)
    {
        $home = new \App\Http\Controllers\Mip\Anchor\AnchorController();
        $home->indexStatic($request);
    }
}