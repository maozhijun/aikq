<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Record;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\MatchTool;
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Match;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\LgMatch\Team;
use App\Models\Match\HotVideo;
use App\Models\Match\HotVideoType;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    //=====================================页面内容 开始=====================================//

    /**
     * 首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        //最后一条录像
        $v = SubjectVideo::orderby('time','desc')->first();
//        $start = date('Y-m-d',date_create('-7 day')->getTimestamp());
//        $end = date('Y-m-d',date_create()->getTimestamp());
//        $start = date_create('2018-07-20');
//        $end = date_create('2018-08-20');
        $start = date_create($v->time)->modify("-6 day")->format("Y-m-d");
        $end = date_create($start)->modify("+7 day")->format("Y-m-d");
        $this->html_var['datas'] = $this->getRecordByDate($start,$end);
//        dump($this->html_var['datas']);
        return view('pc.record.index',$this->html_var);
    }

    /**
     * 专题录像列表 nba cba终端那些
     * @param Request $request
     * @param $name_en
     * @param int $pageNo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subject(Request $request, $name_en, $pageNo = 1){
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        if (isset($data)) {
            $data['name_en'] = $name_en;
            $this->html_var['zhuanti'] = $data;
        }
        //录像
        $records = $this->getRecordBySid($data['id'],$pageNo);
        $this->html_var['records'] = $records['data'];
        $this->html_var['page'] = $records['page'];
        $this->html_var['pageNo'] = $pageNo;
        //球队
        if ($name_en == 'nba'){
            $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$name_en]['lid'])
                ->orderby('name','desc')->first();
            if (isset($season)){
                $season = $season['name'];
            }
            $o_score = BasketScore::where('lid',Controller::SUBJECT_NAME_IDS[$name_en]['lid'])
                ->orderby('rank','asc')
                ->where('season',$season)
                ->get();
            $west = array();
            $east = array();
            $tids = array();
            foreach ($o_score as $item){
                $tids[] = $item['tid'];
                if ($item['zone'] == 0){
                    $west[] = $item['tid'];
                }
                else{
                    $east[] = $item['tid'];
                }
            }
            $o_teams = BasketTeam::whereIn('id',$tids)->get();
            $teams = array();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $this->html_var['teamsData'] = $teams;
            $this->html_var['teams'] = array('west'=>$west,'east'=>$east);
        }
        else if ($name_en == 'cba'){
            $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$name_en]['lid'])
                ->orderby('name','desc')->first();
            if (isset($season)){
                $season = $season['name'];
            }
            $o_score = BasketScore::where('lid',Controller::SUBJECT_NAME_IDS[$name_en]['lid'])
                ->orderby('rank','asc')
                ->where('season',$season)
                ->get();
            $tids = array();
            foreach ($o_score as $item){
                $tids[] = $item['tid'];
            }
            $o_teams = BasketTeam::whereIn('id',$tids)->get();
            $teams = array();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $this->html_var['teamsData'] = $teams;
            $this->html_var['teams'] = $tids;
        }
        else{
            $season = Season::where('lid',Controller::SUBJECT_NAME_IDS[$name_en]['lid'])
                ->orderby('name','desc')->first();
            if (isset($season)){
                $season = $season['name'];
            }
            $o_score = Score::where('lid',Controller::SUBJECT_NAME_IDS[$name_en]['lid'])
                ->where('kind',null)
                ->where('season',$season)
                ->orderby('score','desc')
                ->get();
            $tids = array();
            foreach ($o_score as $item){
                $tids[] = $item['tid'];
            }
            $o_teams = Team::whereIn('id',$tids)->get();
            $teams = array();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $this->html_var['teamsData'] = $teams;
            $this->html_var['teams'] = $tids;
        }
//        dump($this->html_var['teamsData'][206]);
        return view('pc.record.subject',$this->html_var);
    }

    /**
     * 录像列表
     * @param Request $request
     * @param $name_en
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $name_en, $mid) {
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        if (isset($data)) {
            $data['name_en'] = $name_en;
            $this->html_var['zhuanti'] = $data;
        }
        if ($name_en == 'nba' || $name_en == 'cba'){
            $sv = SubjectVideo::where('sport',2)
                ->where('mid',$mid)
                ->first();
            $match = BasketMatch::where('id',$mid)->first();
        }
        else{
            $sv = SubjectVideo::where('sport',1)
                ->where('mid',$mid)
                ->first();
            $match = Match::where('id',$mid)->first();
        }
        if (is_null($sv) || is_null($match)){
            return view('pc.record.detail',$this->html_var);
        }
        $match['time'] = date_create($match['time'])->getTimestamp();
        $records = SubjectVideoChannels::where('sv_id',$sv->id)
            ->orderby('od','asc')
            ->get();
        $this->html_var['sv'] = $sv;
        $this->html_var['records'] = $records;
        $this->html_var['match'] = $match;
        $this->html_var['hotRecords'] = $this->getSubjectRecord($name_en);
//        dump($this->html_var);
        return view('pc.record.detail',$this->html_var);
    }

    /**
     * 获取专题相关录像
     * @param $name_en
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getSubjectRecord($name_en){
        $sid = null;
        if ($name_en != 'record' && $name_en != 'other'){
            $sid = Controller::SUBJECT_NAME_IDS[$name_en]['id'];
        }
        $query = SubjectVideo::query();
        if (!is_null($sid)){
            $query->where('s_lid',$sid);
        }
        $records = $query->orderby('created_at','desc')
            ->take(10)
            ->get();
        return $records;
    }

    /**
     * 返回接口
     * @param Request $request
     * @return string
     */
    public function getRecordByDateJson(Request $request){
        $start = $request->input('date');
        $end = date_create($start)->modify("+1 day")->format("Y-m-d");
        $json = $this->getRecordByDate($start,$end);
        $result = array();
        foreach ($json as $data){
            $result['data'] = $data;
        }
        $result['code'] = 0;
        $result['msg'] = 'success';
        return json_encode($result);
    }

    /**
     * 根据日期获取录像列表
     * @param $start
     * @param $end
     * @return array
     */
    public function getRecordByDate($start, $end){
        $query = SubjectVideo::query();
        $query->where('time','>=', $start);
        $query->where('time','<', $end);
        $query->orderby('time','desc');
        $list = $query->get();
        $result = array();
        foreach ($list as $item){
            $timeStr = date('Y-m-d',date_create($item['time'])->getTimestamp());
            if (!array_key_exists($timeStr,$result)){
                $result[$timeStr]['records'] = array();
                $result[$timeStr]['date'] = $timeStr;
            }
            $result[$timeStr]['records'][] = $item;
        }
        return $result;
    }

    /**
     * 翻页获取录像记录
     * @param $sid
     * @param $pageNo
     * @param int $pageSize
     * @return array
     */
    public function getRecordBySid($sid,$pageNo,$pageSize = 20){
        $query = SubjectVideo::query();
        if (!is_null($sid)){
            $query->where('s_lid',$sid);
        }
        $query->orderby('time','desc');
        $result = $query->paginate($pageSize, ['*'], '', $pageNo);
        return array(
            'page'=>$result->lastPage(),
            'data'=>$result
        );
    }

    /****** 静态化 ******/
    /**
     * 静态化录像首页
     * @param Request $request
     */
    public function staticIndex(Request $request){
        $html = $this->index($request);
        if (!is_null($html) && strlen($html) > 0){
            try {
                Storage::disk("public")->put("/www/record/index.html", $html);
            }
            catch (\Exception $exception){
                echo $exception;
            }
        }
        else{
            echo 'html为空';
        }
    }

    /**
     * 静态化专题录像首页
     * @param Request $request
     * @param $league
     * @param int $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
     */
    public function subjectDetailHtml(Request $request,$league,$page = 1){
        $html = $this->subject($request,$league->name_en,$page);
        if (!is_null($html) && strlen($html) > 0){
            return $html;
        }
        return null;
    }


    public function recordDetailHtml(Request $request,$id){
        $record = SubjectVideo::find($id);
        if (is_null($record)){
            return null;
        }
        $s = SubjectLeague::find($record->s_lid);
        if (is_null($s)){
            $name_en = 'other';
        }
        else{
            $name_en = $s->name_en;
        }
        $html = $this->detail($request,$name_en,$record->mid);
        if (!is_null($html) && strlen($html) > 0){
            $path = CommonTool::getRecordDetailPath($name_en, $record->mid);
            $record->path = $path;
            $record->url = CommonTool::getRecordDetailUrl($name_en, $record->mid);
            $record->save();

            Storage::disk("public")->put('/www'.$path, $html);
            echo 'success';
//            $mobileCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
//            $wapHtml = $mobileCon->detailHtml($article);
//            Storage::disk("public")->put('/m'.$path, $wapHtml);
//
//            $mipCon = new \App\Http\Controllers\Mip\Article\ArticleController();
//            $mipHtml = $mipCon->detailHtml($article);
//            Storage::disk("public")->put('/mip'.$path, $mipHtml);
        }
    }
}