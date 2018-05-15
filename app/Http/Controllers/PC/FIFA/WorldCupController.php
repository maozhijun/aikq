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
        $json = self::curlData('http://match.liaogou168.com/static/league/1/57.json',10);
        $rest = array();
        //赛程
        $rest['schedule'] = $json;
        //顶部推荐类
        $rest['top'] = array();
        //集锦
        $detail = $this->getSubjectDetail(1007);
        $tmp = array();
        foreach ($detail['specimens'] as $key=>$item){
            foreach ($item as $video){
                $tmp[] = $video;
            }
        }
        $rest['top']['videos'] = $tmp;
        //资讯,先拿app接口
        $json = self::curlData('https://shop.liaogou168.com/api/v140/app/topic/list?type=9',10);
        $tmp = array();
        if($json && $json['code'] == 0){
            $tmp = $json['data'];
        }
        $rest['top']['topics'] = $tmp;
        //重点的比赛
        $rest['top']['focus_matches'] = array();
        //焦点图
        $rest['top']['focus'] = $this->getIndexCarousel(1008);
        //排行榜
        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/rank.json',10);
        $rest['rank'] = $json;
        dump($rest);
        return view('pc.fifa.index',$rest);
    }

    public function rank(Request $request){
//        $json = self::curlData('http://match.liaogou168.com/static/league/1/FIFA/2018/rank.json',10);
        $json = json_decode(Storage::disk('public')->get('/static/rank.json'), true);
        $rest = array();
        $rest['rank'] = $json;
        $json = json_decode(Storage::disk('public')->get('/static/tmp.json'), true);
        $rest['score'] = $json;
        return view('mobile.fifa.rank',$rest);
    }

    //球队列表
    public function teamIndex(Request $request){
        $json = json_decode(Storage::disk('public')->get('/static/tmp.json'), true);
        $rest['group'] = $json['stages'][0]['groupMatch'];
        return view('mobile.fifa.team_index',$rest);
    }

    //球队终端
    public function teamDetail(Request $request){
        $json = json_decode(Storage::disk('public')->get('/static/detail.json'), true);
        $rest = $json;
        return view('mobile.fifa.team_detail',$rest);
    }

    public function topicList(Request $request){
        $json = self::curlData('https://shop.liaogou168.com/api/v140/app/topic/list?type=9',20);
        $rest['topics'] = $json['data'];
        return view('mobile.fifa.topic_index',$rest);
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
}