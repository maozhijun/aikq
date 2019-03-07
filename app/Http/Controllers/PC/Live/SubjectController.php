<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/9
 * Time: 15:02
 */

namespace App\Http\Controllers\PC\Live;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\MatchTool;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\Match\Match;
use App\Models\Match\MatchLive;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * @param $name_en
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $name_en) {
        $aiCon = new AikanQController();
        $subjectLeague = SubjectLeague::getSubjectLeagueByEn($name_en);
        $result = $aiCon->subjectDetailData(false, $subjectLeague);
        if (!isset($result) || !isset($result['subject'])) {
            return abort(404);
        }
        return self::subjectDetailHtml($result, $subjectLeague);
    }

    /**
     * 终端静态化
     * @param $result
     * @param SubjectLeague $sl
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function subjectDetailHtml($result, SubjectLeague $sl) {
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
        if (!preg_match("/^https?:\/\//", $icon)) {
            $icon = env('CDN_URL') . $icon;
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
        $result['slid'] = $sl['id'];
        $result['ma_url'] = self::getMobileHttpUrl("/".$sl['name_en']."/");
        return view('pc.subject.detail', $result);
    }


    public function detailV2(Request $request, $name_en) {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (!isset($sl)) {
            return "";
        }
        $sport = $sl->sport;
        $lid = $sl->lid;
        //赛季
        $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
        //赛季
        $year = $season["name"];
        //积分
        $scores = Score::getFootballScores($lid);
        //赛程
        //1.获取当前轮次
        $fMatch = Match::scheduleNearMatch($lid);
        $round = $fMatch["round"];
        $rounds = Match::getScheduleMatchLive($lid, $round);
        //数据榜单
        //右侧内容（球员信息）

        try {
            $comboData = CommonTool::getComboData($name_en);
            $result["comboData"] = $comboData;
        } catch (\Exception $exception) {
        }

        $result["sl"] = $sl;
        $result["season"] = $season;
        $result["round"] = $round;
        $result["scores"] = $scores;
        $result["rounds"] = $rounds;
        return view("pc.subject.v2.football_detail", $result);
    }

    //=====================================================//

    /**
     * 通过接口获取专题列表内容
     */
    public static function getSubjects() {
        //先从文件获取内容
        try {
            $server_output = Storage::get('public/static/json/pc/subject/leagues.json');
        } catch (\Exception $exception) {
            $server_output = "";
        }
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
        $aiCon = new AikanQController();
        $data = $aiCon->subjectDetail(new Request(), $id)->getData();
        $data = json_encode($data);
        $subjects = json_decode($data, true);
        return $subjects;
    }

    //================================================静态化================================================//

    /**
     * 静态化专题列表json
     * @param Request $request
     */
    public function staticSubjectLeagues(Request $request) {
        $aiCon = new AikanQController();
        $data = $aiCon->subjectsData();
        $server_output = json_encode($data);
        if (!empty($server_output)) {
            Storage::disk("public")->put("static/json/pc/subject/leagues.json", $server_output);
        }
    }

    /**
     * 静态化专题终端json数据
     * @param Request $request
     * @param $slid
     */
    public function staticSubjectDetailJson(Request $request, $slid) {
        $akqCon = new AikanQController();
        $json = $akqCon->subjectDetail($request, $slid)->getData();
        $server_output = json_encode($json);
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


    public function staticSubjectVideoDetailPc(SubjectVideoChannels $ch) {
        $video = $ch->video;
        $sl = SubjectLeague::query()->find($video['s_lid']);

        $html = $this->subjectVideoHtml($video, $ch, $sl);

        if (!empty($html)) {//静态化录像终端
            $patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $ch['id']);
            Storage::disk("public")->put('www/'.$patch, $html);
        }
    }

    public function staticSubjectVideoDetailM(SubjectVideoChannels $ch) {
        $video = $ch->video;
        $mCon = new \App\Http\Controllers\Mobile\Live\LiveController();
        $html = $mCon->subjectVideoDetailHtml($ch, $video);

        if (!empty($html)) {//静态化录像终端
            $patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $ch['id']);
            Storage::disk("public")->put('m/'.$patch, $html);
        }
    }

    public function staticSubjectVideoDetailMip(SubjectVideoChannels $ch) {
        $video = $ch->video;
        $mCon = new \App\Http\Controllers\Mip\Live\LiveController();
        $html = $mCon->subjectVideoDetailHtml($ch, $video);

        if (!empty($html)) {//静态化录像终端
            $patch = CommonTool::getSubjectVideoDetailPath($video['s_lid'], $ch['id']);
            Storage::disk("public")->put('mip/'.$patch, $html);
        }
    }

}