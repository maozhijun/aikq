<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Live;


use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\MatchTool;
use App\Models\Subject\SubjectLeague;
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
        if (!isset($videos['videos'])) {
            return abort(404);
        }
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
        $items = $videos['videos'];
        $matches = [];
        foreach ($items as $item) {
            $day = strtotime(date('Y-m-d', $item['time']));
            $matches[$day][] = $item;
        }
        $result['leagues'] = $leagues;
        $result['page'] = $videos['page'];
        $result['matches'] = $matches;
        $result['type'] = $type;
        $result['check'] = 'videos';
        $result['week_array'] = ['星期天', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        $result['subjects'] = [];
        if ($type == 'all') {
            $result['title'] = '爱看球录像_收集最全的NBA录像、英超录像、西甲录像、中超录像供你看_爱看球直播';
            $result['keywords'] = '爱看球,NBA录像,英超录像,西甲录像,中超录像,德甲录像,意甲录像,法甲录像';
        } else {
            if (isset($leagues[$type])) {
                $typeCn = $leagues[$type]['name'];
                $result['title'] =  $typeCn . '录像_' . $typeCn . '高清录像_' . $typeCn . '全场回放_爱看球直播';
                $result['keywords'] = '爱看球,' . $typeCn . ',' . $typeCn . '高清录像,' . $typeCn . ',全场回放';
            }
        }
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
     * @param $isMobile
     * @return array|mixed
     */
    public function getSubjectVideos($type, $page, $isMobile = false) {
        $svCon = new \App\Http\Controllers\IntF\SubjectVideoController();
        $server_output = $svCon->subjectVideos(new Request(), $type, $page)->getData();
        $server_output = json_encode($server_output);
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
        $con = new \App\Http\Controllers\IntF\SubjectVideoController();
        $data = $con->subjectVideosPage(new Request(), $id, $isMobile);
        $str = $data->getData();
        $page = json_encode($str);
        $page = json_decode($page, true);

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
        $aiCon = new \App\Http\Controllers\IntF\SubjectVideoController();
        $data = $aiCon->subjectVideoTypes(new Request())->getData();
        $typesStr = json_encode($data);
        $types = json_decode($typesStr, true);
        Storage::disk("public")->put('/live/subject/videos/leagues.json', $typesStr);
        $data = array();
        foreach ($types as $key=>$item){
            $item['type'] = $key;
            $data[] = $item;
        }
        Storage::disk("public")->put("/app/v101/subject/videos/leagues.json", json_encode($data));
        Storage::disk("public")->put("/app/v110/subject/videos/leagues.json", json_encode($data));
    }

    /**
     * 静态化专题录像列表
     * @param Request $request
     * @param $type
     * @param $page
     * @param $mobile
     */
    public function staticSubjectVideosHtml(Request $request, $type, $page, $mobile = false) {
        $isMobile = $request->input('isMobile', 0) || $mobile;
        $data = $this->getSubjectVideos($type, $page, $isMobile);
        $sub_name_en = "";
        if ($type != "all") {
            if($type == 999) {
                $sub_name_en = "/other";
            } else {
                $subjectLeague = SubjectLeague::query()->find($type);
                if (isset($subjectLeague)) {
                    $sub_name_en = "/" . $subjectLeague->name_en;
                }
            }
        }
        dump($type . ' ' . $sub_name_en . ' page : ' . $page);
        if ($isMobile) {
            $msCon = new \App\Http\Controllers\Mobile\Live\LiveController();
            $m_html = $msCon->subjectVideosHtml($data);
            if (!empty($m_html)) {
                $patch = 'm/live/subject/videos/' . $type . '/' . $page . '.html';
                Storage::disk("public")->put('static/'.$patch, $m_html);//兼容静态化地址
                Storage::disk("public")->put($patch, $m_html);//新的静态化地址
            }

            //静态化json
            if (isset($data['page']) && isset($data['videos'])) {
                $json = $msCon->subjectVideoData2Json($data);
                $patch = 'm/live/subject/videos/' . $type . '/' . $page . '.json';
                Storage::disk("public")->put('static/'.$patch, json_encode($json));//兼容静态化地址
                Storage::disk("public")->put($patch, json_encode($json));//新的静态化地址
            }

            //静态化终端html
//            $videos = isset($data['videos']) ? $data['videos'] : [];
//            foreach ($videos as $video) {
//                $m_detail_html = $msCon->subjectVideoDetailHtml($video);
//                if (!empty($m_detail_html)) {
//                    $m_patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $video['id']);
//                    $m_patch = '/m' . $m_patch;
//                    dump($m_patch);
//                    Storage::disk("public")->put($m_patch, $m_detail_html);
//                }
//            }
        } else {
            $patch = '/live/subject/videos/' . $type . '/' . $page . '.html';
            if ($page == 1) {
                $newPatch = '/www'.$sub_name_en.'/videos/index.html';
            } else {
                $newPatch = '/www'.$sub_name_en.'/videos/index'.$page.'.html';
            }
            dump('newPatch：' . $newPatch);
            if (!isset($data['videos']) || !isset($data['page'])) {
                Storage::delete('public' . $patch);
                Storage::delete('public' . $newPatch);
                return;
            }
            $leagues = $this->getLeagues();
            $html = $this->videosHtml($type, $leagues, $data);
            Storage::disk("public")->put("/app/v101/subject/videos/" . $type . '/' . $page . ".json", json_encode($data));
            Storage::disk("public")->put("/app/v110/subject/videos/" . $type . '/' . $page . ".json", json_encode($data));
            if (!empty($html)) {
                //echo $type . ' patch : ' . $patch . "\n";
                Storage::disk("public")->put($patch, $html);//静态化热门录像分页列表
                Storage::disk("public")->put($newPatch, $html);//静态化热门录像分页列表
            }

            //静态化终端html
            $videos = isset($data['videos']) ? $data['videos'] : [];
            $con = new SubjectController();
            foreach ($videos as $video) {
                $con->staticSubjectVideo($video);
                $channels = isset($video['channels']) ? $video['channels'] : [];
                foreach ($channels as $channel) {
                    $con->staticSVideoChannelJson($channel, $channel['id']);
                }
            }
        }
    }
    //=====================================静态化 结束=====================================//
}