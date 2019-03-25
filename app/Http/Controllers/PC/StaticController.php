<?php

namespace App\Http\Controllers\PC;

use App\Http\Controllers\PC\Article\ArticleController;
use App\Http\Controllers\PC\Record\RecordController;
use App\Http\Controllers\PC\Team\TeamController;
use App\Models\Article\PcArticle;
use App\Models\Match\HotVideo;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class StaticController extends Controller
{
    public function updateTag(Request $request, $tagId){

    }

    //静态化逻辑
    public static function staticDetail($type,$id){
        switch ($type){
            case TagRelation::kTypeArticle:{
                //终端静态化 本来有了
                //找到对应的tags
                $trs = TagRelation::where('type',$type)
                    ->join('tags','tags.id','=','tag_relations.tag_id')
                    ->orderby('tags.level','asc')
                    ->where('tag_relations.source_id',$id)
                    ->where('tag_relations.type',TagRelation::kTypeArticle)
                    ->select('tags.*')->get();
                $name_en = null;
                $lid = null;
                foreach ($trs as $tr){
                    //没错了,就是这么蛋疼,tag和以前的subject_leagues不通,但是又有更新关系,这里做个对应
                    switch ($tr->level){
                        case Tag::kLevelTwo:{
                            //更新赛事combodata
                            $sl = SubjectLeague::where('lid','=',$tr->tid)->first();
                            if (isset($sl)) {
                                $name_en = $sl->name_en;
                                $lid = $sl->lid;
                                HomeController::updateFileComboData($name_en);
                                //赛事终端(录像、资讯、视频)加入更新列表,并且触发page为4,首页直接刷新
                                //录像 只有一页
//                                $rcon = new RecordController();
//                                $rcon->staticIndex(new Request());

                                //资讯 只有一页
                                $con = new ArticleController();
                                $mobileCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
                                $html = $con->newsHome(new Request());
                                if (!empty($html)) {
                                    Storage::disk("public")->put("/www/news/index.html", $html);
                                }
                                //视频

                                //资讯专题更新2页,重置page
                                for ($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/record_subject/'.$name_en.'/'.$i);
                                    $con->subjectDetailHtml(new Request(),$name_en,$i);
                                    $mobileCon->subjectDetailHtml(new Request(),$name_en,$i);
                                }
                                StaticController::pushStaticLeague($name_en,"news",3);
                            }
                        }
                            break;
                        case Tag::kLevelThree:{
                            //录像对应球队综合页与录像页(更新2页
                            if (isset($name_en) && isset($lid)) {
                                $tid = $tr->tid;
                                $sport = $tr->sport;
                                $path = CommonTool::getTeamDetailPathWithType($sport, $name_en, $tid,'index',1);

                                //pc站综合页
                                $con = new TeamController();
                                $tempTid = $tid;
                                while (strlen($tempTid) < 4) {
                                    $tempTid = "0".$tempTid;
                                }
                                $tempTid = $sport.$tempTid;
                                $html = $con->detail(new Request(), $name_en, $tempTid);
                                if (isset($html) && strlen($html) > 0){
                                    Storage::disk('public')->put("www/$path", $html);
                                }

                                //录像
                                $tcon = new TeamController();
                                for($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/team_record/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                                    $tcon->staticNewsHtml(new Request(),$sport,$name_en,$tid,$i);
                                }
                                //球队加入更新队列
                                StaticController::pushStaticTeam($name_en,$sport,$tid,"record",3);

                                //资讯
                                $tcon = new TeamController();
                                for($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/team_record/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                                    $tcon->staticNewsHtml(new Request(),$sport,$name_en,$tid,$i);
                                }
                                StaticController::pushStaticTeam($name_en,$sport,$tid,"news",3);

                                //m站球队终端 (只有一个页面
                                \App\Http\Controllers\Mobile\Team\TeamController::detailStatic($name_en,$sport,$tempTid);
                            }
                        }
                            break;
                        case Tag::kLevelFour:
                        case Tag::kLevelOne:
                            break;
                    }
                }
            }
                break;
            case TagRelation::kTypeVideo:{
                //终端静态化 本来有了
                //找到对应的tags
                $trs = TagRelation::where('type',$type)
                    ->join('tags','tags.id','=','tag_relations.tag_id')
                    ->orderby('tags.level','asc')
                    ->where('tag_relations.source_id',$id)
                    ->where('tag_relations.type',TagRelation::kTypeVideo)
                    ->select('tags.*')->get();
                $name_en = null;
                $lid = null;
                foreach ($trs as $tr){
                    //没错了,就是这么蛋疼,tag和以前的subject_leagues不通,但是又有更新关系,这里做个对应
                    switch ($tr->level){
                        case Tag::kLevelTwo:{
                            //更新赛事combodata
                            $sl = SubjectLeague::where('lid','=',$tr->tid)->first();
                            if (isset($sl)) {
                                $name_en = $sl->name_en;
                                $lid = $sl->lid;
                                HomeController::updateFileComboData($name_en);
                                //赛事终端(录像、资讯、视频)加入更新列表,并且触发page为4,首页直接刷新
                                //录像 只有一页
//                                $rcon = new RecordController();
//                                $rcon->staticIndex(new Request());

                                //资讯 只有一页
//                                $con = new ArticleController();
//                                $html = $con->newsHome(new Request());
//                                if (!empty($html)) {
//                                    Storage::disk("public")->put("/www/news/index.html", $html);
//                                }
                                //视频

                                //资讯专题更新2页,重置page
                                for ($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/record_subject/'.$name_en.'/'.$i);
//                                    $con->subjectDetailHtml(new Request(),$name_en,$i);
                                }
//                                StaticController::pushStaticLeague($name_en,"news",3);
                            }
                        }
                            break;
                        case Tag::kLevelThree:{
                            //视频对应球队综合页与视频页(更新2页
                            if (isset($name_en) && isset($lid)) {
                                $tid = $tr->tid;
                                $sport = $tr->sport;
                                $path = CommonTool::getTeamDetailPathWithType($sport, $name_en, $tid,'index',1);

                                //pc站综合页
                                $con = new TeamController();
                                $tempTid = $tid;
                                while (strlen($tempTid) < 4) {
                                    $tempTid = "0".$tempTid;
                                }
                                $tempTid = $sport.$tempTid;
                                $html = $con->detail(new Request(), $name_en, $tempTid);
                                if (isset($html) && strlen($html) > 0){
                                    Storage::disk('public')->put("www/$path", $html);
                                }

                                //视频
                                $tcon = new TeamController();
                                for($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/team_record/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                                    $tcon->staticVideoHtml(new Request(),$sport,$name_en,$tid,$i);
                                }
                                //球队加入更新队列
                                StaticController::pushStaticTeam($name_en,$sport,$tid,"video",3);

                                //m站球队终端 (只有一个页面
                                \App\Http\Controllers\Mobile\Team\TeamController::detailStatic($name_en, $tempTid, $sport);
                            }
                        }
                            break;
                        case Tag::kLevelFour:
                        case Tag::kLevelOne:
                            break;
                    }
                }
            }
                break;
            case TagRelation::kTypePlayBack:{
                //终端静态化
                $ch = curl_init();
                $url = env('CMS_URL').'/static/record/'.$id;
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
                curl_exec ($ch);
                curl_close ($ch);

                $ch = curl_init();
                $url = env('CMS_URL').'/m/static/record/'.$id;
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
                curl_exec ($ch);
                curl_close ($ch);

                //找到对应的tags
                $trs = TagRelation::where('type',$type)
                    ->join('tags','tags.id','=','tag_relations.tag_id')
                    ->orderby('tags.level','asc')
                    ->where('tag_relations.source_id',$id)
                    ->where('tag_relations.type',TagRelation::kTypePlayBack)
                    ->select('tags.*')->get();
                $name_en = null;
                $lid = null;

                //录像 只有一页
                $rcon = new RecordController();
                $rcon->staticIndex(new Request());
                $mcon = new \App\Http\Controllers\Mobile\Record\RecordController();
                $mcon->staticIndex(new Request());
                foreach ($trs as $tr){
                    //没错了,就是这么蛋疼,tag和以前的subject_leagues不通,但是又有更新关系,这里做个对应
                    switch ($tr->level){
                        case Tag::kLevelTwo:{
                            //更新赛事combodata
                            $sl = SubjectLeague::where('lid','=',$tr->tid)->first();
                            if (isset($sl)) {
                                $name_en = $sl->name_en;
                                $lid = $sl->lid;
                                HomeController::updateFileComboData($name_en);
                                //赛事终端(录像、资讯、视频)加入更新列表,并且触发page为4,首页直接刷新

                                //资讯 只有一页
//                                $con = new ArticleController();
//                                $html = $con->newsHome(new Request());
//                                if (!empty($html)) {
//                                    Storage::disk("public")->put("/www/news/index.html", $html);
//                                }
                                //视频

                                //录像专题更新2页,重置page
                                $con = new RecordController();
                                for ($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/record_subject/'.$name_en.'/'.$i);
                                    $con->subjectDetailHtml(new Request(),$name_en,$i);
                                }
                                StaticController::pushStaticLeague($name_en,"record",3);
                            }
                            }
                            break;
                        case Tag::kLevelThree:{
                            //录像对应球队综合页与录像页(更新2页
                            if (isset($name_en) && isset($lid)) {
                                $tid = $tr->tid;
                                $sport = $tr->sport;
                                $path = CommonTool::getTeamDetailPathWithType($sport, $name_en, $tid,'index',1);

                                //pc站综合页
                                $con = new TeamController();
                                $tempTid = $tid;
                                while (strlen($tempTid) < 4) {
                                    $tempTid = "0".$tempTid;
                                }
                                $tempTid = $sport.$tempTid;
                                $html = $con->detail(new Request(), $name_en, $tempTid);
                                if (isset($html) && strlen($html) > 0){
                                    Storage::disk('public')->put("www/$path", $html);
                                }

                                //录像
                                $tcon = new TeamController();
                                for($i = 1 ; $i < 3 ; $i++){
//                                    StaticController::loadUrl('/static/team_record/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                                    $tcon->staticRecordHtml(new Request(),$sport,$name_en,$tid,$i);
                                }
                                //球队加入更新队列
                                StaticController::pushStaticTeam($name_en,$sport,$tid,"record",3);

                                //资讯
//                                $tcon = new TeamController();
//                                for($i = 1 ; $i < 3 ; $i++){
////                                    StaticController::loadUrl('/static/team_record/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
//                                    $tcon->staticNewsHtml(new Request(),$sport,$name_en,$tid,$i);
//                                }
//                                StaticController::pushStaticTeam($name_en,$sport,$tid,"news",3);
                            }
                        }
                            break;
                        case Tag::kLevelFour:
                        case Tag::kLevelOne:
                            break;
                    }
                }
            }
                break;
        }
    }

    private static function loadUrl($url){
        $ch = curl_init();
        $url = env('CMS_URL').$url;
        echo "$url <br>";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
        $response = curl_exec ($ch);
        dump($response);
        curl_close ($ch);
    }

    /*********** 队列维护 ************/
    //获取最新要更新的league的name_en
    public static function lastStaticLeague(){
        $name_en = Redis::zRange("static_leagues_page_list",0,0,'WITHSCORES');
        return $name_en;
    }

    //获取最新要更新的league的name_en
    public static function pushStaticLeague($name_en,$type,$page){
        //nba_record
        Redis::zAdd("static_leagues_page_list",$page,$name_en.'_'.$type);
    }

    //删除更新的league(已经把分页都走了一次之后触发)
    public static function delStaticLeague($name_en,$type){
        Redis::zRem("static_leagues_page_list",$name_en.'_'.$type);
    }

    //获取最新要更新的team的name_en
    public static function lastStaticTeam($type){
        $name_en = Redis::zRange("static_team_page_list",0,0,'WITHSCORES');
        return $name_en;
    }

    //获取最新要更新的team的name_en
    public static function pushStaticTeam($name_en,$sport,$tid,$type,$page){
        //nba_2_135_record
        Redis::zAdd("static_team_page_list",$page,$name_en."_".$sport.'_'.$tid.'_'.$type);
    }

    //删除更新的team(已经把分页都走了一次之后触发)
    public static function delStaticTeam($name_en,$sport,$tid,$type){
        Redis::zRem("static_team_page_list",$name_en."_".$sport.'_'.$tid.'_'.$type);
    }
}
