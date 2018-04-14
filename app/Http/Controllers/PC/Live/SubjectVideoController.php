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

class SubjectVideoController extends Controller
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
        $leagues = $this->getLeagues();
        $videos = $this->getSubjectVideos($type, $page);
        return $this->videosHtml($type, $leagues, $videos);
    }

    /**
     * 录像列表
     * @param $type
     * @param $leagues
     * @param $videos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    protected function videosHtml($type, $leagues, $videos) {
        if (!isset($videos['videos'])) {
            return "";
        }
        $result['leagues'] = $leagues;
        $result['page'] = $videos['page'];
        $result['videos'] = $videos['videos'];
        $result['type'] = $type;
        $result['check'] = 'videos';
        return view('pc.subject_video.list', $result);
    }

    //=====================================页面内容 结束=====================================//


    //=====================================数据接口 开始=====================================//


    /**
     * 获取录像类型
     * @return array|mixed
     */
    public function getLeagues() {
        try {
            $leagues = Storage::get('public/live/subject/videos/leagues.json');
        } catch (\Exception $exception) {
            $leagues = "[]";
        }
        $json = json_decode($leagues, true);
        $json = isset($json) ? $json : [];
        return $json;
    }

    /**
     * 获取录像列表
     * @param $type
     * @param $page
     * @return array|mixed
     */
    public function getSubjectVideos($type, $page) {
        $url = env('LIAOGOU_URL')."aik/subjects/league/video/page/" . $type . '?page=' . $page;
        $server_output = SubjectController::execUrl($url);
        $videos = json_decode($server_output, true);
        $videos = isset($videos) ? $videos : [];
        return $videos;
    }

    /**
     * 获取热门录像分类的分页信息
     * @param $id
     * @param $isMobile
     * @return array|mixed
     */
    public function getVideoPageMsg($id, $isMobile = false) {
        $url = env('LIAOGOU_URL')."aik/subjects/league/video/page-msg/" . $id . ($isMobile ? '?isMobile=1' : '');
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
    public function staticVideoLeaguesJson(Request $request) {
        $url = env('LIAOGOU_URL')."aik/subjects/league/video/leagues";
        $server_output = SubjectController::execUrl($url);
        $types = json_decode($server_output, true);
        $types = isset($types) ? $types : [];
        $typesStr = json_encode($types);
        Storage::disk("public")->put('/live/subject/videos/leagues.json', $typesStr);
    }

    /**
     * 静态化专题录像列表
     * @param Request $request
     * @param $type
     * @param $page
     */
    public function staticSubjectVideosHtml(Request $request, $type, $page) {
        $data = $this->getSubjectVideos($type, $page);
        $patch = '/live/subject/videos/' . $type . '/' . $page . '.html';
        if (!isset($data['videos']) || !isset($data['page'])) {
            Storage::delete('public/' . $patch);
            return;
        }
        $leagues = $this->getLeagues();
        $html = $this->videosHtml($type, $leagues, $data);
        if (!empty($html)) {
            echo $type . ' patch : ' . $patch . "\n";
            Storage::disk("public")->put($patch, $html);//静态化热门录像分页列表
        }
    }
    //=====================================静态化 结束=====================================//
}