<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\Mobile\Record;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\MatchTool;
use App\Models\Article\PcArticle;
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
use Illuminate\Support\Facades\Response;
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
        $this->html_var['check'] = 'record';
        $this->html_var['title'] = '国内外篮球、足球赛事录像大全-爱看球直播';
        $this->html_var['keywords'] = '';
        $this->html_var['description'] = '';
        return view('mobile.record.index',$this->html_var);
    }

    public function getMatchWithDate(Request $request){
        $date = $request->input('date');
        $start = date_create($date)->format("Y-m-d");
        $end = date_create($start)->modify("+1 day")->format("Y-m-d");
        $data = $this->getRecordByDate($start,$end);
        if (count($data) == 0){
            $data = array($start=>array(
                'records'=>array(),
                'date'=>$start
            ));
        }
        $callback = $request->input('callback');
        if (isset($callback)) {
            return response($callback . '(' . json_encode($data) . ')');
        }
        else{
            return response()->json($data);
        }
    }

    public function detail2(Request $request, $mid) {
        return $this->detail($request,'other',$mid);
    }

    /**
     * 录像终端
     * @param Request $request
     * @param $name_en
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $name_en, $mid) {
        $this->html_var['check'] = 'record';
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $data = SubjectLeague::getSubjectLeagueByEn($name_en);
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
            if (count(explode('_',$mid)) == 2){
                $sport = explode('_',$mid)[1];
                $mid = explode('_',$mid)[0];
                if ($sport == 2){
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
            }
            else{
                $sv = SubjectVideo::where('sport',1)
                    ->where('mid',$mid)
                    ->first();
                $match = Match::where('id',$mid)->first();
            }
        }
        if (is_null($sv) || is_null($match)){
            return view('mobile.record.detail',$this->html_var);
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
        //资讯
        //专题资讯 开始
//        $article_array = PcArticle::getLastArticle($name_en);
//        dump(CommonTool::getComboData($name_en));
        $this->html_var['comboData'] = CommonTool::getComboData($name_en);
        $this->html_var['check'] = 'record';
        //专题资讯 结束
        $this->html_var["title"] = '['.$sv["hname"].'VS'.$sv["aname"].']'.$sv["lname"].$sv["hname"].'VS'.$sv["aname"].'比赛录像_爱看球直播';
        $keywords = $sv->tagsCn();
        $this->html_var["keywords"] = str_replace("，", ",", $keywords);
//        dump($this->html_var);
        return view('mobile.record.detail',$this->html_var);
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
        $callback = $request->input('callback');
        if (isset($callback)) {
            return response($callback . '(' . json_encode($result) . ')');
        }
        else{
            return response()->json($result);
        }
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
            $item['hurl'] = CommonTool::getTeamDetailUrl2($item['sport'],$item['s_lid'],$item['hid']);
            $item['aurl'] = CommonTool::getTeamDetailUrl2($item['sport'],$item['s_lid'],$item['hid']);
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
    public static function getRecordBySid($sid,$pageNo,$pageSize = 20){
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
                Storage::disk("public")->put("/m/record/index.html", $html);
            }
            catch (\Exception $exception){
                echo $exception;
            }
        }
        else{
            echo 'html为空';
        }
    }

    public function recordDetailHtml(Request $request,$id){
        $record = SubjectVideo::find($id);
        if (is_null($record)){
            return null;
        }
        $s = SubjectLeague::find($record->s_lid);
        if (is_null($s)){
            $name_en = 'other';
            $html = $this->detail($request,$name_en,$record->mid.'_'.$record->sport);
        }
        else{
            $name_en = $s->name_en;
            $html = $this->detail($request,$name_en,$record->mid);
        }
        if (!is_null($html) && strlen($html) > 0){
            $path = CommonTool::getRecordDetailPath($name_en, $record->mid,$record->sport);
            $record->path = $path;
            $record->url = CommonTool::getRecordDetailUrl($name_en, $record->mid, $record->sport);
            $record->save();

            Storage::disk("public")->put('/m'.$path, $html);
//            $mobileCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
//            $wapHtml = $mobileCon->detailHtml($article);
//            Storage::disk("public")->put('/m'.$path, $wapHtml);
//
//            $mipCon = new \App\Http\Controllers\Mip\Article\ArticleController();
//            $mipHtml = $mipCon->detailHtml($article);
//            Storage::disk("public")->put('/mip'.$path, $mipHtml);
        }
        else{
            echo 'fail ' . $id;
        }
    }
}