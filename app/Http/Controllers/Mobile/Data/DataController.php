<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/02/19
 * Time: 15:40
 */

namespace App\Http\Controllers\Mobile\Data;

use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\League;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\LgMatch\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller{
    public function index(Request $request){
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $subjects = Controller::SUBJECT_NAME_IDS;
        $subData = array();
        foreach ($subjects as $subject=>$data){
            $scores = array();
            //nba
            if ($subject == 'nba'){
                $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $kind = $season['kind'];
                    $season = $season['name'];
                }
                //球队积分
                $leagueData = self::curlData('http://match.liaogou168.com/static/league/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'.json',10);
                if (isset($leagueData['scores']) && isset($leagueData['scores']['east'])) {
                    $scores['east'] = $leagueData['scores']['east'];
                }
                if (isset($leagueData['scores']) && isset($leagueData['scores']['west'])) {
                    $scores['west'] = $leagueData['scores']['west'];
                }
                $scores['league'] = $leagueData['league'];
                $scores['subject'] = $subject;
                //球员
                $playerTech = self::curlData('http://match.liaogou168.com/static/technical/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
                $scores['playerTech'] = $playerTech;
                $scores['subject'] = $subject;
                $scores['tabs'] = array(
                    array('name'=>'得分','key'=>'ppg'),
                    array('name'=>'平均篮板','key'=>'rpg'),
                    array('name'=>'平均助攻','key'=>'apg'),

                );
                $subData[] = $scores;
            }
            //cba
            else if ($subject == 'cba'){
                $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $kind = $season['kind'];
                    $season = $season['name'];
                }
                //球队积分
                $leagueData = self::curlData('http://match.liaogou168.com/static/league/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'.json',10);
                if (isset($leagueData['scores']) && isset($leagueData['scores']['west'])) {
                    $scores['score'] = $leagueData['scores']['west'];
                }
                $scores['league'] = $leagueData['league'];
                $scores['subject'] = $subject;
                //球员
                $playerTech = self::curlData('http://match.liaogou168.com/static/technical/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
                $scores['playerTech'] = $playerTech;
                $scores['subject'] = $subject;
                $scores['tabs'] = array(
                    array('name'=>'得分','key'=>'ppg'),
                    array('name'=>'平均篮板','key'=>'rpg'),
                    array('name'=>'平均助攻','key'=>'apg'),

                );
                $subData[] = $scores;
            }
            else{
                $season = Season::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $season = $season['name'];
                }
                $kind = 0;
                //球队积分
                $leagueData = self::curlData('http://match.liaogou168.com/static/league/1/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'.json',10);
                if ($leagueData['league']['type'] == 2){
                    $tmp = array();
                    if (isset($leagueData['stages'])){
                        foreach ($leagueData['stages'] as $stage){
                            if ($stage['status'] == 1 && isset($stage['groupMatch'])){
                                $tmp = $stage['groupMatch'];
                            }
                        }
                    }
                    //杯赛
                    $scores['score'] = $tmp;
                }
                else{
                    $scores['score'] = isset($leagueData['score'])?$leagueData['score']:array();
                }
                $scores['league'] = $leagueData['league'];

                $scores['subject'] = $subject;
                $scores['tabs'] = array(
                );
                //球员
                $playerTech = self::curlData('http://match.liaogou168.com/static/technical/1/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
                $scores['playerTech'] = $playerTech;
                $subData[] = $scores;
            }
        }

        $this->html_var['leagues'] = $subData;
        $this->html_var['title'] = '足球篮球赛事数据_国内外足球篮球赛事排行榜-爱看球直播';
        $this->html_var['keywords'] = '';
        $this->html_var['description'] = '';
        return view('mobile.data.index',$this->html_var);
    }

    /**
     * 请求match接口
     * @param $url
     * @param $timeout
     * @return mixed
     */
    public static function curlData($url,$timeout = 5){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }

    /********** 静态化 ************/
    /**
     * data的首页
     * @param Request $request
     */
    public function staticIndex(Request $request){
        $html = $this->index($request);
        if (!is_null($html) && strlen($html) > 0){
            try {
                Storage::disk("public")->put("/www/data/index.html", $html);
            }
            catch (\Exception $exception){
                echo $exception;
            }
        }
        else{
            echo 'html为空';
        }
    }
}