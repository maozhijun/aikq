<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/9
 * Time: 15:02
 */

namespace App\Http\Controllers\Mobile\Subject;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\MatchTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * 爱看球专题
 * Class SubjectController
 * @package App\Http\Controllers\PC\Subject
 */
class SubjectController extends Controller
{
    /**
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request) {
        $path = $request->path();
        $name = str_replace("m/", "", $path);
        if (!array_key_exists($name, self::SUBJECT_NAME_IDS)) {
            return abort(404);
        }
        $s_lid = self::SUBJECT_NAME_IDS[$name];

        $result = $this->getSubjectDetail($s_lid);
        if (!isset($result) || !isset($result['subject'])) {
            return abort(404);
        }

        //处理专题内容
        $subject = $result['subject'];
        $content = $subject['content'];
        $content_array = explode("\r", $content);
        $new_content = '';
        foreach ($content_array as $c) {
            $new_content .= '<p>' . $c. '</p>';
        }
        $subject['content'] = $new_content;
        $icon = $subject['icon'];
        $icon = str_replace('https://www.liaogou168.com', '', $icon);
        $icon = str_replace('http://www.liaogou168.com', '', $icon);
        if (!starts_with('http', $icon)) {
            $icon = env('CDN_URL') . '/live/subject' . $icon;
        }
        $subject['icon'] = $icon;
        $result['subject'] = $subject;
        //处理专题内容

        $result['subjects'] = self::getSubjects();//所有专题
        $result['weekCnArray'] = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        $result['hasLeft'] = (isset($result['articles']) && count($result['articles']) > 0) || (isset($result['ranks']) && count($result['ranks']) > 0);
        $hasRound = false;//是否有轮次
        if (isset($result['lives']) && count($result['lives']) > 0) {
            $lives = $result['lives'];
            foreach ($lives as $day=>$matches) {
                $hasRound = isset($matches[0]['round']);
                break;
            }
        }
        $subjectName = $subject['name'];
        $result['hasRound'] = $hasRound;
        $result['slid'] = $s_lid;
        $result['title'] = $subjectName . '直播_' . $subjectName . '决赛直播_' . $subjectName . '录像_爱看球';
//        dump($result);
        return view('mobile.subject.detail', $result);
    }

    /**
     * 录像播放终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $vid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectVideo(Request $request,$first, $second, $vid) {
        //录像 播放页面
        $video = $this->getSubjectVideo($vid);
        if (isset($video['error'])) {
            return "";
        }
        $result['match'] = $video;
        $result['type'] = 'video';

        $lname = $video['lname'];
        $hname = $video['hname'];
        $aname = $video['aname'];

        $match_title = $hname . "VS" . $aname;
        $result['title'] = $match_title . "全场回放_" . $match_title . "高清录像_" . $lname . "录像_爱看球";
        $result['keywords'] = '爱看球,' . $lname . ',' . $match_title . ',' . $hname . ',' . $aname;
        return view('pc.subject.video', $result);
    }

    /**
     * 专题录像终端HTML
     * @param $video
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function subjectVideoHtml($video) {
        $result['match'] = $video;
        $result['type'] = 'video';

        $lname = $video['lname'];
        $hname = $video['hname'];
        $aname = $video['aname'];

        $match_title = $hname . "VS" . $aname;
        $result['title'] = $match_title . "全场回放_" . $match_title . "高清录像_" . $lname . "录像_爱看球";
        $result['keywords'] = '爱看球,' . $lname . ',' . $match_title . ',' . $hname . ',' . $aname;
        return view('pc.subject.video', $result);
    }

    /**
     *
     * @param Request $request
     * @param $first
     * @param $second
     * @param $sid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectSpecimen(Request $request, $first, $second, $sid) {
        $specimen = $this->getSubjectSpecimen($sid);
        if (isset($specimen['error'])) {
            return "";
        }
        $result['match'] = $specimen;
        $result['type'] = 'specimen';
        return view('pc.subject.video', $result);
    }

    /**
     * 线路播放json
     * @param Request $request
     * @param $first
     * @param $second
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideoChannelJson(Request $request, $first, $second, $id) {
        $json = $this->getSubjectVideoChannel($id);
        return response()->json($json);
    }

    /**
     * 线路播放json
     * @param Request $request
     * @param $first
     * @param $second
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectSpecimenChannelJson(Request $request,$first, $second, $id) {
        $json = $this->getSubjectSpecimenChannel($id);
        return response()->json($json);
    }

    /**
     * 播放 线路的页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectPlayer(Request $request) {
        return view('pc.subject.player', ['cdn'=>env('CDN_URL')]);
    }
    //=====================================================//

    /**
     * 通过接口获取专题列表内容
     */
    public static function getSubjects() {
        //先从文件获取内容
        try {
            $server_output = Storage::get('public/static/json/subject/leagues.json');
        } catch (\Exception $exception) {
            $server_output = "";
        }
//        $url = env('LIAOGOU_URL')."aik/subjects";
//        $server_output = self::execUrl($url);
        $subjects = json_decode($server_output, true);
        $subjects = isset($subjects) ? $subjects : [];
        return $subjects;
    }

    /**
     * 通过接口获取专题终端内容
     * @param $id
     * @return array|mixed|void
     */
    public function getSubjectDetail($id) {
//        $url = env('LIAOGOU_URL')."aik/subjects/detail/" . $id;
//        $server_output = $this->execUrl($url);
//        $subjects = json_decode($server_output, true);
//        $subjects = isset($subjects) ? $subjects : [];

        $aiCon = new AikanQController();
        $data = $aiCon->subjectDetail(new Request(), $id)->getData();
        $data = json_encode($data);
        $subjects = json_decode($data, true);
        return $subjects;
    }

    /**
     * 通过接口获取录像信息
     * @param $id
     * @return array|mixed
     */
    public function getSubjectVideo($id) {
        $url = env('LIAOGOU_URL')."aik/subjects/video/" . $id;
        $server_output = $this->execUrl($url);
        $video = json_decode($server_output, true);
        $video = isset($video) ? $video : [];
        return $video;
    }

    /**
     * 通过接口获取集锦信息
     * @param $id
     * @return array|mixed
     */
    public function getSubjectSpecimen($id) {
        $url = env('LIAOGOU_URL')."aik/subjects/specimen/" . $id;
        $server_output = $this->execUrl($url);
        $specimen = json_decode($server_output, true);
        $specimen = isset($specimen) ? $specimen : [];
        return $specimen;
    }

    /**
     * 获取录像线路信息
     * @param $cid
     * @return array|mixed
     */
    public function getSubjectVideoChannel($cid) {
        $url = env('LIAOGOU_URL')."aik/subjects/video/channel/" . $cid;
        $server_output = $this->execUrl($url);
        $channel = json_decode($server_output, true);
        $channel = isset($channel) ? $channel : ['code'=>-1];
        return $channel;
    }

    /**
     * 获取录像线路信息
     * @param $cid
     * @return array|mixed
     */
    public function getSubjectSpecimenChannel($cid) {
        $url = env('LIAOGOU_URL')."aik/subjects/specimen/channel/" . $cid;
        $server_output = $this->execUrl($url);
        $specimen = json_decode($server_output, true);
        $specimen = isset($specimen) ? $specimen : ['code'=>-1];
        return $specimen;
    }
    //================================================静态化================================================//

    /**
     * 静态化专题列表json
     * @param Request $request
     */
    public function staticSubjectLeagues(Request $request) {
//        $url = env('LIAOGOU_URL')."aik/subjects";
//        $server_output = self::execUrl($url);

        $aiCon = new AikanQController();
        $data = $aiCon->subjects(new Request())->getData();
        $server_output = json_encode($data);
        if (!empty($server_output)) {
            Storage::disk("public")->put("/static/json/subject/leagues.json", $server_output);
        }
    }

    /**
     * 静态化专题终端json数据
     * @param Request $request
     * @param $slid
     */
    public function staticSubjectDetailJson(Request $request, $slid) {
        $url = env('LIAOGOU_URL')."aik/subjects/detail/" . $slid;
        $server_output = $this->execUrl($url);
        if (!empty($server_output)) {
            Storage::disk("public")->put("/static/json/subject/" . $slid . ".json", $server_output);
        }
    }

    /**
     * 静态化专题终端页
     * @param Request $request
     * @param $slid
     */
    public function staticSubjectHtml(Request $request, $slid) {
        $html = $this->detail($request, $slid);
        if (!empty($html)) {
            Storage::disk("public")->put("/live/subject/" . $slid . ".html", $html);
        }
    }

    /**
     * 静态化录像html
     * @param Request $request
     * @param $vid
     */
    public function staticSubjectVideoHtml(Request $request, $vid) {
        $html = $this->subjectVideo($request, '', '', $vid);
        if (!empty($html)) {//静态化录像终端
            $patch = MatchTool::subjectLink($vid, 'video');
            Storage::disk("public")->put($patch, $html);
        }
    }

    /**
     * 获取列表中所有录像并静态化其终端
     * @param Request $request
     * @param $type
     * @param $page
     */
    public function staticSubjectVideoHtmlFromVideos(Request $request, $type, $page) {
        $videoIntF = new SubjectVideoController();
        $data = $videoIntF->getSubjectVideos($type, $page);
        if (!isset($data['videos'])) {
            echo "专题录像终端静态化无数据，执行完毕。\n";
            return;
        }
        $videos = $data['videos'];
        foreach ($videos as $video) {
            $html = $this->subjectVideoHtml($video);
            if (!empty($html)) {//静态化录像终端
                $vid = $video['id'];
                $patch = MatchTool::subjectLink($vid, 'video');
                Storage::disk("public")->put($patch, $html);
            }
        }
    }

    /**
     * 静态化录像线路json
     * @param Request $request
     * @param $ch_id
     */
    public function staticSubjectVideoChannelJson(Request $request, $ch_id) {
        $json = $this->getSubjectVideoChannel($ch_id);//$this->getSubjectVideo($ch_id);//
        $jsonStr = json_encode($json);
        if (!empty($jsonStr)) {
            $patch = MatchTool::subjectChannelLink($ch_id, 'video');
            Storage::disk("public")->put($patch, $jsonStr);
            $patch = MatchTool::subjectChannelLink($ch_id, 'video', true);
            Storage::disk("public")->put($patch, $jsonStr);
        }
    }

    /**
     * 静态化集锦 html 线路json
     * @param Request $request
     * @param $sid
     */
    public function staticSubjectSpecimenHtml(Request $request, $sid) {
        $html = $this->subjectSpecimen($request, '', '', $sid);
        if (!empty($html)) {
            $patch = MatchTool::subjectLink($sid, 'specimen');
            Storage::disk("public")->put($patch, $html);
        }
        $json = $this->getSubjectSpecimenChannel($sid);
        $jsonStr = json_encode($json);
        if (!empty($jsonStr)) {
            $patch = MatchTool::subjectChannelLink($sid, 'specimen');
            Storage::disk("public")->put($patch, $jsonStr);
        }
    }

    /**
     * 静态化player页面
     * @param Request $request
     */
    public function staticPlayer(Request $request) {
        $html = $this->subjectPlayer($request);
        if (!empty($html)) {
            Storage::disk("public")->put("/live/subject/player.html", $html);
        }
    }
}