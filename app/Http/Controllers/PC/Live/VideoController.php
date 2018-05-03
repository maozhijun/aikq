<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Live;


use App\Http\Controllers\PC\MatchTool;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

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
        return $this->videosHtml($type, $types, $videos);
    }

    /**
     * 录像列表
     * @param $type
     * @param $types
     * @param $videos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    protected function videosHtml($type, $types, $videos) {
        if (!isset($videos['videos'])) {
            return "";
        }
        $result['types'] = $types;
        $result['page'] = $videos['page'];
        $result['videos'] = $videos['videos'];
        $result['type'] = $type;
        $result['check'] = 'videos';
        return view('pc.video.list', $result);
    }

    /**
     * 录像终端
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoDetail(Request $request) {

        return view('pc.video.detail');
    }

    //=====================================页面内容 结束=====================================//


    //=====================================数据接口 开始=====================================//


    /**
     * 获取录像类型
     * @return array|mixed
     */
    public function getTypes() {
        try {
            $types = Storage::get('public/live/videos/types.json');
        } catch (\Exception $exception) {
            $types = "[]";
        }
        $json = json_decode($types, true);
        $json = isset($json) ? $json : [];
        return $json;
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

    /**
     * 获取录像终端json
     * @param $id
     * @param $isMobile
     * @return array|mixed
     */
    public function getVideoDetailJsonStr($id, $isMobile = false) {
        $url = env('LIAOGOU_URL')."aik/videos/" . $id . ($isMobile ? '?isMobile=1' : '');
        $server_output = SubjectController::execUrl($url);
        return $server_output;
    }


    /**
     * 获取热门录像分类的分页信息
     * @param $id
     * @param $isMobile
     * @return array|mixed
     */
    public function getVideoPageMsg($id, $isMobile = false) {
        $url = env('LIAOGOU_URL')."aik/videos/page-msg/" . $id . ($isMobile ? '?isMobile=1' : '');
        $server_output = SubjectController::execUrl($url);
        $page = json_decode($server_output, true);
        $page = isset($page) ? $page : [];
        return $page;
    }
    //=====================================数据接口 结束=====================================//



    //=====================================静态化 开始=====================================//

    /**
     * 静态化类型列表json
     * @param Request $request
     */
    public function staticVideoTypesJson(Request $request) {
        $url = env('LIAOGOU_URL')."aik/videos/types";
        $server_output = SubjectController::execUrl($url);
        $types = json_decode($server_output, true);
        $types = isset($types) ? $types : [];
        $typesStr = json_encode($types);
        Storage::disk("public")->put('/live/videos/types.json', $typesStr);
    }

    /**
     * 静态化类型列表json
     * @param Request $request
     */
    public function staticVideoDetail(Request $request) {
        $html = $this->videoDetail($request);
        Storage::disk("public")->put('/live/videos/detail.html', $html);
    }


    public function syncVideoImages(Request $request) {
        $save_patch = '/live/videos/cover';
        $url = '';
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $img = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "http_code: " . $http_code . "\n";
        curl_close($ch);
        if ($http_code >= 400) {
            echo "获取链接内容失败";
            return;
        }
        $list = explode("/", $url);
        $ext = $list[count($list) - 1];
        $list = explode('?', $ext);
        $fileName = $list[0];
        $file_patch = $save_patch . $fileName;
        Storage::disk('public')->put($file_patch, $img);

    }

    /**
     * 静态化录像列表
     * @param Request $request
     * @param $type
     * @param $page
     */
    public function staticVideosHtml(Request $request, $type, $page) {
        $data = $this->getVideos($type, $page);
        $patch = '/live/videos/' . $type . '/' . $page . '.html';
        if (!isset($data['videos']) || !isset($data['page'])) {
            Storage::delete('public/' . $patch);
            return;
        }
        $types = $this->getTypes();
        $html = $this->videosHtml($type, $types, $data);
        if (!empty($html)) {
            Storage::disk("public")->put($patch, $html);//静态化热门录像分页列表
        }
        $videos = $data['videos'];
        foreach ($videos as $video) {
            $vid = $video['id'];
            $cover = $video['cover'];
            $cover = str_replace('https://www.liaogou168.com', '', $cover);
            $cover = str_replace('http://www.liaogou168.com', '', $cover);
            $video['cover'] = $cover;
            $vJsonStr = json_encode($video);
            if (!empty($vJsonStr)) {
                $patch = MatchTool::hotVideoJsonLink($vid);
                Storage::disk("public")->put($patch, $vJsonStr);//静态化热门录像终端json
            }
        }
    }

    /**
     * 静态化录像终端/线路 信息
     * @param Request $request
     * @param $id
     */
    public function staticVideoJson(Request $request, $id) {
        //live/videos/channel/index/id.json
        $jsonStr = $this->getVideoDetailJsonStr($id);
        if (!empty($jsonStr)) {
            $patch = MatchTool::hotVideoJsonLink($id);
            Storage::disk("public")->put($patch, $jsonStr);
        }
    }
    //=====================================静态化 结束=====================================//
}