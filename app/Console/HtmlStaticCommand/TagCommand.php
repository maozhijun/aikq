<?php

namespace App\Console\HtmlStaticCommand;

use App\Http\Controllers\PC\Article\ArticleController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\Live\LiveController;
use App\Http\Controllers\PC\Record\RecordController;
use App\Http\Controllers\PC\StaticController;
use App\Http\Controllers\PC\Team\TeamController;
use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleType;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TagCommand extends BaseCommand
{

    protected function command_name()
    {
        return "tag_static";
    }

    protected function description()
    {
        return "tag静态化 每次拿3个分页最小的进行 分赛事和球队终端";
    }

    public function handle()
    {
        $type = $this->argument('type');
        if ($type == 'league'){
            //拿分页里面还有的最少的那个出来开始静态化他的分页
            $data = StaticController::lastStaticLeague();
            $page = 0;
            $name_en = null;
            $stype = null;
            foreach ($data as $tag_o=>$page_o){
                $page = $page_o;
                $tmp = explode("_",$tag_o);
                if (count($tmp) < 2)
                    break;
                else{
                    $name_en = $tmp[0];
                    $stype = $tmp[1];
                }
            }
            echo $name_en . ' ' .$stype . ' ' . $page . ' ';
            //静态化
            if ($page <= 0 || is_null($name_en) || is_null($stype) || strlen($name_en) == 0 || strlen($stype) == 0){
                //没有就不静态化了
            }
            else{
                $lastPage = 0;
                $sl = SubjectLeague::where('name_en',$name_en)->first();
                if (is_null($sl))
                    return;
                if ($stype == 'record'){
                    //拿这页的数据
                    $datas = RecordController::getRecordBySid($sl->id,$page);
                    //一共有多少页
                    $lastPage = $datas['page'];
                    //一次2页
                    for ($i = $page ; $i < min($lastPage + 1,$page + 2) ; $i++){
                        $this->loadUrl('/static/record_subject/'.$name_en.'/'.$i);
                    }
                }
                else if ($stype == 'news'){
                    //拿这页的数据
                    $typeId = -1;
                    $typeObj = PcArticleType::getTypeByTypeEn($name_en);
                    if ($typeObj != null) {
                        $typeId = $typeObj->id;
                    }

                    $query = PcArticle::getPublishQuery();
                    if ($typeId > 0) {
                        $query->where('type', $typeId);
                    }
                    $datas = $query->paginate(ArticleController::APP_PAGE_SIZE, ['*'], '', $page);
                    $lastPage = $datas->lastPage();
                    //一次2页
                    for ($i = $page ; $i < min($lastPage + 1,$page + 2) ; $i++){
                        $this->loadUrl('/static/news_subject/'.$name_en.'/'.$i);
                    }
                }
                //清空
                if ($page + 2 > $lastPage){
                    StaticController::delStaticLeague($name_en,$stype);
                }
                else{
                    dump('here');
                    StaticController::pushStaticLeague($name_en,$stype,($page + 2));
                }
            }
        }
        if ($type == 'team'){
            $data = StaticController::lastStaticTeam("record");
            $page = 0;
            $name_en = null;
            $stype = null;
            $sport = null;
            $tid = null;
            foreach ($data as $tag_o=>$page_o){
                $page = $page_o;
                $tmp = explode("_",$tag_o);
                if (count($tmp) < 4)
                    break;
                else{
                    $name_en = $tmp[0];
                    $sport = $tmp[1];
                    $tid = $tmp[2];
                    $stype = $tmp[3];
                }
            }
            echo $name_en . ' ' .$stype . ' ' .$sport .' '. $tid . ' '.$page;
            //静态化
            if ($page <= 0 ||
                is_null($name_en) || is_null($sport) || is_null($tid) || is_null($stype) ||
                strlen($name_en) == 0 || strlen($sport) == 0 || strlen($tid) == 0 || strlen($stype) == 0){
                //没有就不静态化了
            }
            else{
                $lastPage = 0;
                if ($stype == 'record'){
                    //拿这页的数据
                    $datas = TagRelation::getRelationsPageByTagId(TagRelation::kTypePlayBack,$sport,3,$tid,$page,1);
                    //一共有多少页
//                    dump($datas);
                    $lastPage = $datas->lastPage();
                    for ($i = $page ; $i < min($lastPage + 1,$page + 2) ; $i++){
                        $this->loadUrl('/static/team_record/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                    }
                }
                elseif ($stype == 'news'){
                    //拿这页的数据
                    $datas = TagRelation::getRelationsPageByTagId(TagRelation::kTypeArticle,$sport,3,$tid,$page,1);
                    //一共有多少页
//                    dump($datas);
                    $lastPage = $datas->lastPage();
                    for ($i = $page ; $i < min($lastPage + 1,$page + 2) ; $i++){
                        $this->loadUrl('/static/team_news/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                    }
                }
                elseif ($stype == 'video'){
                    //拿这页的数据
                    $datas = TagRelation::getRelationsPageByTagId(TagRelation::kTypeVideo,$sport,3,$tid,$page,1);
                    //一共有多少页
//                    dump($datas);
                    $lastPage = $datas->lastPage();
                    for ($i = $page ; $i < min($lastPage + 1,$page + 2) ; $i++){
                        $this->loadUrl('/static/team_video/'.$sport.'/'.$name_en.'/'.$tid.'/'.$i);
                    }
                }
                if ($page + 2 >= $lastPage){
                    StaticController::delStaticTeam($name_en,$sport,$tid,$stype);
                }
                else{
                    StaticController::pushStaticTeam($name_en,$sport,$tid,$stype,($page + 2));
                }
            }
        }
    }

    private function loadUrl($url){
        $ch = curl_init();
        $url = env('CMS_URL').$url;
        echo "$url <br>";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }
}