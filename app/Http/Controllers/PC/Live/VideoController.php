<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Live;


use Illuminate\Routing\Controller;

class VideoController extends Controller
{
    //=====================================页面内容 开始=====================================//


    //=====================================页面内容 结束=====================================//


    //=====================================数据接口 开始=====================================//



    public function getTypes() {
        $url = env('LIAOGOU_URL')."aik/videos/types";
        $server_output = SubjectController::execUrl($url);
        $types = json_decode($server_output, true);
        $types = isset($types) ? $types : [];
        return $types;
    }

    //=====================================数据接口 结束=====================================//
}