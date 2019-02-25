<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Record;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\MatchTool;
use App\Models\LgMatch\BasketMatch;
use App\Models\LgMatch\Match;
use App\Models\Match\HotVideo;
use App\Models\Match\HotVideoType;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    //=====================================页面内容 开始=====================================//

    /**
     * 录像列表
     * @param Request $request
     * @param $name_en
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $name_en, $mid) {
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

    public function getSubjectRecord($name_en){
        $sid = null;
        if ($name_en != 'other'){
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
}