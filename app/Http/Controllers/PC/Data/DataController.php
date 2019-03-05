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
        $this->html_var['check'] = 'data';
        $subjects = Controller::SUBJECT_NAME_IDS;
        $subData = array();
        foreach ($subjects as $subject=>$data){
            //nba
            if ($subject == 'nba'){
                $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $kind = $season['kind'];
                    $season = $season['name'];
                }
                //球队积分
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
                $scores['league'] = $data;

                //球员
                $playerTech = self::curlData('http://match.liaogou168.com/static/technical/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
                $scores['playerTech'] = $playerTech;
//                dump($playerTech);
                $scores['teams'] = $teams;
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
                $o_score = BasketScore::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
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
                $scores = array('score'=>$o_score);
                $scores['league'] = $data;

                //球员
                $playerTech = self::curlData('http://match.liaogou168.com/static/technical/2/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
                $scores['playerTech'] = $playerTech;
//                dump($playerTech);
                $scores['teams'] = $teams;
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
                $o_score = Score::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('rank','asc')
                    ->where('kind',null)
                    ->where('season',$season)
                    ->get();

                $tids = array();
                foreach ($o_score as $item){
                    $tids[] = $item['tid'];

                }
                $scores = array('score'=>$o_score);
                $o_teams = Team::whereIn('id',$tids)->get();
                $teams = array();
                foreach ($o_teams as $item){
                    $teams[$item['id']] = $item;
                }
                $league = League::where('id',Controller::SUBJECT_NAME_IDS[$subject]['lid'])->first();
                $scores['league'] = $league;
                $scores['subject'] = $subject;
                $scores['teams'] = $teams;
                $scores['tabs'] = array(
                );
                //球员
                $playerTech = self::curlData('http://match.liaogou168.com/static/technical/1/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
                $scores['playerTech'] = $playerTech;
                $subData[] = $scores;
            }
        }

        $this->html_var['leagues'] = $subData;
        return view('pc.data.index',$this->html_var);
    }

    public function detail(Request $request, $subject, $season = null, $kind = null){
        if ($subject == 'nba' || $subject == 'cba'){
            return $this->basketDetail($request,$subject,$season,$kind);
        }
        else{
            return $this->footballDetail($request,$subject,$season,$kind);
        }
    }

    public function footballDetail(Request $request, $subject, $season = null, $kind = 0){
        $this->html_var['check'] = 'data';
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
        $kind = 0;
        $data = array_key_exists($subject, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$subject] : null;
        if (isset($data)) {
            $data['name_en'] = $subject;
            $this->html_var['zhuanti'] = $data;
        }
        else{
            return null;
        }
        if ($season == null){
            $season = Season::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                ->orderby('name','desc')->first();
            if (isset($season)){
                $season = $season['name'];
            }
        }
        $league = League::where('id',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
            ->orderby('name','desc')->first();
        $this->html_var['league'] = $league;
        $o_score = Score::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
            ->where('kind',null)
            ->orderby('rank','asc')
            ->where('season',$season)
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

        //杯赛
        if ($league['type'] == 2){
            $tmp = array();
            foreach ($o_score as $item){
                if (!array_key_exists($item['group'],$tmp)){
                    $tmp[$item['group']] = array();
                }
                $tmp[$item['group']][] = $item;
            }
            ksort($tmp);
            $o_score = $tmp;
        }
        $scores = array('score'=>$o_score);

        if (!isset($scores)){
            return null;
        }

        $teamTech = self::curlData('http://match.liaogou168.com/static/technical/1/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/team/'.$season.'_'.$kind.'.json',5);
        $playerTech = self::curlData('http://match.liaogou168.com/static/technical/1/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/player/'.$season.'_'.$kind.'.json',5);
        $this->html_var['scores'] = $scores;
//        dump('http://match.liaogou168.com/static/technical/1/'.Controller::SUBJECT_NAME_IDS[$subject]['lid'].'/team/'.$season.'_'.$kind.'.json');
//        dump($playerTech);
        $this->html_var['playerTech'] = $playerTech;
//        dump($playerTech);
        $this->html_var['teamTech'] = $teamTech;
        $this->html_var['teams'] = $teams;
        $this->html_var['teamTabs'] = array(
            array('name'=>'进球','key'=>'goal'),
            array('name'=>'失球','key'=>'fumble'),
            array('name'=>'黄牌','key'=>'yellow'),
            array('name'=>'红牌','key'=>'red'),
        );
        $this->html_var['playerTabs'] = array(
            array('name'=>'进球','key'=>'goal'),
            array('name'=>'助攻','key'=>'assist'),
            array('name'=>'黄牌','key'=>'yellow'),
            array('name'=>'红牌','key'=>'red'),
        );
//        dump($this->html_var['teamTech']);
        return view('pc.data.football',$this->html_var);
    }

    public function basketDetail(Request $request,$subject,$season = null,$kind = null){
        $this->html_var['check'] = 'data';
        $this->html_var['subjects'] = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();
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
                    $kind = $season['kind'];
                    $season = $season['name'];
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
        else if ($subject == 'cba'){
            if ($season == null){
                $season = BasketSeason::where('lid',Controller::SUBJECT_NAME_IDS[$subject]['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $kind = $season['kind'];
                    $season = $season['name'];
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
            }
            $o_teams = BasketTeam::whereIn('id',$tids)->get();
            $teams = array();
            foreach ($o_teams as $item){
                $teams[$item['id']] = $item;
            }
            $scores = array('score'=>$o_score);
        }
        else{
        }

        if (!isset($scores)){
            return null;
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

    /**
     * 专题的data终端
     * @param Request $request
     * @param $league
     * @return html
     */
    public function dataDetailHtml(Request $request,$league){
        $html = $this->detail($request,$league->name_en);
        if (!is_null($html) && strlen($html) > 0){
            return $html;
        }
        return null;
    }
}