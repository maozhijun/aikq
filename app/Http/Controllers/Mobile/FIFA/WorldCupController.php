<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:40
 */

namespace App\Http\Controllers\Mobile\FIFA;

use App\Http\Controllers\PC\CommonTool;
use App\Models\Match\Odd;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WorldCupController extends Controller{
    public function index(Request $request){
        $json = self::curlData('http://match.liaogou168.com/static/league/1/57.json',10);
        $rest = $json;
//        dump($rest);
        return view('mobile.fifa.index',$rest);
    }

    /**
     * 积分射手榜
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rank(Request $request){
//        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/rank.json',10);
        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/rank.json',10);
        $rest = array();
        $rest['rank'] = $json;
        $json = self::curlData('http://match.liaogou168.com/static/league/1/57.json',10);
        $rest['score'] = $json;
        return view('mobile.fifa.rank',$rest);
    }

    //球队列表
    public function teamIndex(Request $request){
        $json = self::curlData('http://match.liaogou168.com/static/league/1/57.json',10);
        $rest['group'] = $json['stages'][0]['groupMatch'];
        return view('mobile.fifa.team_index',$rest);
    }

    //球队终端
    public function teamDetail(Request $request,$tid){
        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/'.$tid.'/detail.json',10);
        $rest = $json;
        return view('mobile.fifa.team_detail',$rest);
    }

    /**
     * 资讯列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function topicList(Request $request){
        $json = self::curlData('https://shop.liaogou168.com/api/v140/app/topic/list?type=12',20);
        $rest['topics'] = $json['data'];
        return view('mobile.fifa.topic_index',$rest);
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

    /****************** 静态化 *********************/
    /**
     * 球队列表,只要一次就好,基本上不会变了
     * @param Request $request
     */
    public function staticTeamIndex(Request $request){
        $html = $this->teamIndex($request);
        if (!is_null($html) && strlen($html) > 0){
            try {
                Storage::disk("public")->put("/static/m/worldcup/2018/team_index.html", $html);
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
            Storage::disk("public")->put("/static/m/worldcup/2018/team/".$tid.".html", $html);
        }
    }

    /**
     * 积分射手榜
     * @param Request $request
     */
    public function staticRank(Request $request){
        $html = $this->rank($request);
        if (!is_null($html) && strlen($html) > 0){
            Storage::disk("public")->put("/static/m/worldcup/2018/rank.html", $html);
        }
    }

    /**
     * 首页
     * @param Request $request
     */
    public function staticIndex(Request $request){
        $html = $this->index($request);
        if (!is_null($html) && strlen($html) > 0){
            Storage::disk("public")->put("/static/m/worldcup/2018/index.html", $html);
        }
    }

    /**
     * 资讯
     * @param Request $request
     */
    public function staticTopicList(Request $request){
        $html = $this->topicList($request);
        if (!is_null($html) && strlen($html) > 0){
            Storage::disk("public")->put("/static/m/worldcup/2018/topic/index.html", $html);
        }
    }
}