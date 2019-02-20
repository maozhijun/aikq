<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/02/19
 * Time: 15:40
 */

namespace App\Http\Controllers\PC\Data;

use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller{
    public function index(Request $request){
        $team = self::curlData('http://match.liaogou168.com/static/technical/2/1/team/18-19_1.json',5);
        $player = self::curlData('http://match.liaogou168.com/static/technical/2/1/player/18-19_1.json',5);
        return view('pc.data.index',$this->html_var);
    }

    public function detail(Request $request,$subject,$season = null,$kind = null){
        $season = '18-19';
        $kind = '1';
        $data = array_key_exists($subject, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$subject] : null;
        if (isset($data)) {
            $data['name_en'] = $subject;
            $this->html_var['zhuanti'] = $data;
        }
        if ($subject == 'nba'){
            if ($season == null){
                $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $season = $season['name'];
                    $kind = $season['kind'];
                }
            }
            $o_score = BasketScore::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                ->orderby('rank','asc')
                ->where('season',$season)
                ->get();
            $west = array();
            $east = array();
            $tids = array();
            foreach ($o_score as $item){
                $tids[] = $item['tid'];
                if ($item['zone'] == 0){
                    $west[] = $item;
                }
                else{
                    $east[] = $item;
                }
            }
            $o_teams = BasketTeam::whereIn('id',$tids)->get();
            $teams = array();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $scores = array('west'=>$west,'east'=>$east);
        }
        else{

        }

        $teamTech = self::curlData('http://match.liaogou168.com/static/technical/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/team/'.$season.'_'.$kind.'.json',5);
        $playerTech = self::curlData('http://match.liaogou168.com/static/technical/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
        $this->html_var['scores'] = $scores;
//        dump($teamTech);
//        dump($playerTech);
        $this->html_var['playerTech'] = $playerTech;
        $this->html_var['teamTech'] = $teamTech;
        $this->html_var['teams'] = $teams;
        $this->html_var['tabs'] = array(
            array('name'=>'得分','key'=>'ppg'),
            array('name'=>'投篮命中率','key'=>'fp_rate'),
            array('name'=>'三分命中率','key'=>'three_p_rate'),
            array('name'=>'罚球命中率','key'=>'ft_rate'),
            array('name'=>'平均篮板','key'=>'rpg'),
            array('name'=>'平均助攻','key'=>'apg'),
            array('name'=>'平均盖帽','key'=>'bpg'),
            array('name'=>'平均抢断','key'=>'spg'),
            array('name'=>'平均犯规','key'=>'fpg'),
            array('name'=>'平均失误','key'=>'topg'),

        );
//        dump($this->html_var['teams']);
        return view('pc.data.basketball',$this->html_var);
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
     * 球队列表,只要一次就好,基本上不会变了
     * @param Request $request
     */
    public function staticIndex(Request $request){
        $html = $this->index($request);
        if (!is_null($html) && strlen($html) > 0){
            try {
                Storage::disk("public")->put("/static/worldcup/2018/index.html", $html);
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
     * 球队终端
     * @param Request $request
     * @param $tid
     */
    public function staticTeamDetail(Request $request,$tid){
        $html = $this->teamDetail($request,$tid);
        if (!is_null($html) && strlen($html) > 0){
            Storage::disk("public")->put("/static/worldcup/2018/team/".$tid.".html", $html);
        }
    }
}