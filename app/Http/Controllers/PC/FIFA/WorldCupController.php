<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 11:40
 */

namespace App\Http\Controllers\PC\FIFA;

use App\Models\Carousel;
use App\Models\Match\Odd;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WorldCupController extends Controller{
    public function index(Request $request){
        $json = self::curlData('http://match.liaogou168.com/static/league/1/57.json',5);
        $rest = array();
        //赛程
        $rest['schedule'] = $json;
        //顶部推荐类
        $rest['top'] = array();
        //集锦
        $detail = $this->getSubjectDetail(1008);
        $tmp = array();
        if (isset($detail) && isset($detail['specimens'])) {
            foreach ($detail['specimens'] as $key => $item) {
                foreach ($item as $video) {
                    $tmp[] = $video;
                }
            }
        }
        $rest['top']['videos'] = $tmp;
        //资讯,先拿app接口
        $json = self::curlData('http://www.liaogou168.com/aik/subjects/detail/1008',5);
        $tmp = array();
        if($json && isset($json['articles'])){
            $tmp = $json['articles'];
        }
        $rest['top']['topics'] = $tmp;
        //重点的比赛
        $json = self::curlData('https://www.liaogou168.com/aik/worldcup/hotMatch',5);
        if (isset($json)){
            Storage::disk("public")->put("/static/json/pc/worldcup/2018/hotmatch.json", json_encode($json));
        }
        $rest['top']['focus_matches'] = $json;
        //焦点图
        $rest['top']['focus'] = $this->getIndexCarousel(1008);
        //排行榜
        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/rank.json',5);
        $rest['rank'] = $json;
//        dump($rest);
        //淘汰赛
//        $json = json_decode(Storage::disk('public')->get('/static/tmp.json'), true);
        $json = self::curlData('https://www.liaogou168.com/aik/worldcup/schedule',5);
        $rest['dieQuit'] = $json;
        return view('pc.fifa.index',$rest);
    }

    //球队终端
    public function teamDetail(Request $request,$tid){
        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/'.$tid.'/detail.json',10);
        $rest = $json;
        //小组积分
        $json = self::curlData('http://match.liaogou168.com/static/league/1/57.json',10);
        $groups = $json['stages'][0]['groupMatch'];
        $isHere = false;
        $rest['scores'] = array();
        foreach ($groups as $key=>$item){
            foreach ($item['scores'] as $score){
                if ($score['tid'] == $tid){
                    $isHere = true;
                }
            }
            if ($isHere){
                $rest['scores'] = $item['scores'];
                break;
            }
        }
        return view('pc.fifa.team_detail',$rest);
    }

    /**
     * 通过接口获取专题终端内容
     * @param $id
     * @return array|mixed|void
     */
    public function getSubjectDetail($id) {
        $url = env('LIAOGOU_URL')."aik/subjects/detail/" . $id;
        $server_output = $this->curlData($url);
        $subjects = isset($server_output) ? $server_output : [];
        return $subjects;
    }

    public function getIndexCarousel($id) {
        $url = env('LIAOGOU_URL')."aik/subjects/carousel/" . $id;
        $server_output = $this->curlData($url);
        $subjects = isset($server_output) ? $server_output : [];
        return $subjects;
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