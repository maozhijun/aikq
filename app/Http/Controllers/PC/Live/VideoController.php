<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Live;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VideoController extends Controller
{
    //=====================================页面内容 开始=====================================//

    /**
     * 录像列表
     * @param Request $request
     * @param $type
     * @param $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videos(Request $request, $type, $page = 1) {
        $types = $this->getTypes();
        $videos = $this->getVideos($type, $page);
        if (!isset($videos['videos'])) {
            return "";
        }
        $result['types'] = $types;
        $result['page'] = $videos['page'];
        $result['videos'] = $videos['videos'];
        $result['type'] = $type;
        return view('pc.video.list', $result);
    }

    //=====================================页面内容 结束=====================================//


    //=====================================数据接口 开始=====================================//



    public function getTypes() {
        $url = env('LIAOGOU_URL')."aik/videos/types";
        $server_output = SubjectController::execUrl($url);
        $types = json_decode($server_output, true);
        $types = isset($types) ? $types : [];
        return $types;
    }

    /**
     * 获取录像列表
     * @param $type
     * @param $page
     * @return array|mixed
     */
    public function getVideos($type, $page) {
        $url = env('LIAOGOU_URL')."aik/videos/page/" . $type . '?page=' . $page;
        $server_output = SubjectController::execUrl($url);
        $videos = json_decode($server_output, true);
        $videos = isset($videos) ? $videos : [];
        return $videos;
    }

    //=====================================数据接口 结束=====================================//
}