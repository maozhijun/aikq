<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 12:09
 */

namespace App\Models\Article;


use App\Console\Article\ArticlesCacheCommand;
use App\Http\Controllers\PC\CommonTool;
use App\Models\Admin\Account;
use App\Models\Label\Label;
use App\Models\Label\LabelGroup;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\TagRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class PcArticle extends Model
{
    const kStatusPublish = 1;
    public function author_obj() {
        return $this->hasOne(Author::class, 'id', 'author_id');
    }

    public function detail() {
        return $this->hasOne(PcArticleDetail::class, 'id', 'id');
    }

    public function c_user() {
        return $this->hasOne(Account::class, 'id', 'c_uid');
    }

    public function type_obj() {
        return $this->hasOne(PcArticleType::class, 'id', 'type');
    }

    public function getContent() {
        return $this->detail->content;
    }

    public function statusCN()
    {
        switch ($this->status) {
            case 0: {
                return "未发布";
            }
            case 1: {
                return "已发布";
            }
        }
    }

    public function getUrl() {
        return $this->url;
    }

    public function getWebUrl() {
        return env('APP_URL').'/m'.$this->getUrl();
    }

    public function getMipUrl() {
        return env('APP_URL').'/mip'.$this->getUrl();
    }

    public static function getBaiduQuery(){
        $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
        $query->where('baidu_spider_count','=',0);
        $query->orderByDesc('publish_at');
        return $query;
    }

    public static function getPublishQuery($name_en = "") {
        $query = null;
        if (isset($name_en) && strlen($name_en) > 0) {
            $type = PcArticleType::getTypeByTypeEn($name_en);
            if(isset($type)){
                $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
                $query->where('type',$type->id);
                $query->orderByDesc('publish_at');
            }
        } else {
            $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
            $query->orderByDesc('publish_at');
        }
        return $query;
    }

    public function getCover() {
        $cover = $this->cover;
//        $local = env('APP_URL');
//        $cover = str_replace($local, env('CDN_URL'), $cover);
        return $cover;
    }

    public function getLocalCover() {
        $cover = $this->cover;
//        $local = env('CDN_URL');
//        $cover = str_replace($local, '//www.aikanqiu.com', $cover);
        return $cover;
    }

    public function appModel($isWithDetail = false) {
        $url =  env('APP_URL').'/'.$this->getUrl();
        $cover = $this->getCover();
        $modelItem = [
            'id'=>$this->id,
            'title'=>$this->title,
            'author_name'=>$this->author,
            'cover'=>$cover,
            'digest'=>$this->digest,
            'labels'=>$this->labels,
            'elite'=>$this->elite,
            'read_count'=>$this->read_count,
            'resource'=>$this->resource,
            'publish_at'=>strtotime($this->publish_at),
            'url'=>$url,
        ];
        if ($isWithDetail) {
            $modelItem['content'] = $this->getContent();
            $modelItem['type'] = $this->type_obj->appModel();
            $modelItem['author'] = $this->author_obj->appModel();
        }
        return $modelItem;
    }

    /**
     * 返回首页文章
     * @param bool $isBaidu
     * @return mixed
     */
    public static function indexArticles($isBaidu = false) {
        if($isBaidu){
            //$articles = DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.status = ".self::kStatusPublish.") as a ORDER BY r LIMIT 0,30;");
            $articles = DB::select("select * from pc_articles as p where p.status = ".self::kStatusPublish." and p.baidu_spider_count = 0 ORDER BY updated_at desc LIMIT 0,30;");
            $array = array();
            foreach ($articles as $article) {
                $array[] = ['title'=>$article->title, 'url'=>$article->url, 'publish_at'=>$article->publish_at];
            }
            if(count($array) < 30){
                $query = self::getPublishQuery();
                $articles = $query->take(30 - count($array))->get();
                foreach ($articles as $article) {
                    $array[] = ['title'=>$article->title, 'url'=>$article->getUrl(), 'publish_at'=>$article->publish_at];
                }
            }
            $articleCache = json_encode($array);
            return json_decode($articleCache, true);
        }
        $key = "IndexArticles_Cache_";
        $articleCache = Redis::get($key);
        if (empty($articleCache)) {
            $query = self::getPublishQuery();
            $articles = $query->take(30)->get();
            $array = [];
            //文章内容2小时刷新一次
            foreach ($articles as $article) {
                $array[] = ['title'=>$article->title, 'url'=>$article->getUrl(), 'publish_at'=>$article->publish_at];
            }
            //shuffle($array);
            //$result = [];
            //foreach ($array as $index=>$ar) {
            //    if ($index >= 12) break;
            //    $result[] = $ar;
            //}
            //usort($result, function ($a, $b) {
            //    $a_publish_at = strtotime($a['publish_at']);
            //    $b_publish_at = strtotime($b['publish_at']);
            //    return $b_publish_at - $a_publish_at;
            //});
            $articleCache = json_encode($array);
            Redis::setEx($key, 10 * 60, $articleCache);
        }
        return json_decode($articleCache, true);
    }

    public static function randArticles($size) {
        $articleCache = Redis::get(ArticlesCacheCommand::ARTICLE_CACHE_KEY);
        if (empty($articleCache)) {
            return [];
        } else {
            $array = json_decode($articleCache, true);
        }
        shuffle($array);
        $result = [];
        foreach ($array as $index=>$ar) {
            if ($index >= $size) break;
            $result[] = $ar;
        }
        usort($result, function ($a, $b) {
            $a_publish_at = strtotime($a['publish_at']);
            $b_publish_at = strtotime($b['publish_at']);
            return $b_publish_at - $a_publish_at;
        });
        return $result;
    }

    public static function articlesByType($name_en){
        $type = PcArticleType::getTypeByTypeEn($name_en);
        if(isset($type)){
            $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
            $query->where('type',$type->id);
            $query->orderByDesc('publish_at');
            return $query->take(20)->get();
        }
        return array();
    }

    /**
     * v2版本右侧资讯列表用
     * @param null $name_en
     * @param int $size
     * @return array
     */
    public static function getLastArticle($name_en = null,$size = 10){
        if (is_null($name_en)){
            $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
            $query->orderByDesc('publish_at');
            $query->take($size);
            $tmp = $query->get();
            $articles = array();
        }
        else{
            $type = PcArticleType::getTypeByTypeEn($name_en);
            if(isset($type)){
                $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
                $query->where('type',$type->id);
                $query->orderByDesc('publish_at');
                $articles = $query->take(10)->get();
            }
            else{
                $articles = array();
            }
            if (count($articles) < $size){
                $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
                $query->orderByDesc('publish_at');
                $query->take($size - count($articles));
                $tmp = $query->get();
            }
            else{
                $tmp = array();
            }
        }
        $article_array = [];
        foreach ($articles as $article) {
            $url = $article->url;
            if (is_null($url)){
                $url = $article->getUrl();
            }
            $tags = TagRelation::getTagWithSids(TagRelation::kTypeArticle,$article['id']);
            $t = array();
            foreach ($tags as $tag) {
                $t[] = $tag['name'];
            }
            $article_array[] = ['title'=>$article->title, 'link'=>$url,'update_at'=>$article->publish_at, 'cover'=>$article->cover, 'tags'=>$t];
        }
        foreach ($tmp as $article) {
            $url = $article->url;
            if (is_null($url)){
                $url = $article->getUrl();
            }
            $t = array();
            $tags = TagRelation::getTagWithSids(TagRelation::kTypeArticle,$article['id']);
            foreach ($tags as $tag) {
                $t[] = $tag['name'];
            }
            $article_array[] = ['title'=>$article->title, 'link'=>$url,'update_at'=>$article->publish_at, 'cover'=>$article->cover, 'tags'=>$t];
        }
        return $article_array;
    }

    public static function relationsArticle($curAid, $type, $count = 10,$isBaidu = false) {
        if($isBaidu){
            if (is_numeric($curAid)) {
                $result = DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.id <> $curAid and p.baidu_spider_count = 0 and p.status = " . self::kStatusPublish . " and p.type = $type) as a ORDER BY r LIMIT 0,$count;");
            }
            else {
                $result = DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.status = " . self::kStatusPublish . " and p.type = $type and p.baidu_spider_count = 0) as a ORDER BY r LIMIT 0,$count;");
            }
            if(count($result) < $count){
                $result = DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.id <> $curAid and p.baidu_spider_count = 0 and p.status = " . self::kStatusPublish . ") as a ORDER BY r LIMIT 0,$count;");
                if(count($result) < $count){
                    $result = DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.status = " . self::kStatusPublish . " and p.type = $type) as a ORDER BY r LIMIT 0,$count;");
                }
            }
            return $result;
        }
        else {
            if (is_numeric($curAid)) {
                return DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.id <> $curAid and p.status = " . self::kStatusPublish . " and p.type = $type) as a ORDER BY r LIMIT 0,$count;");
            }
            return DB::select("SELECT *,RAND() as r FROM (select * from pc_articles as p where p.status = " . self::kStatusPublish . " and p.type = $type) as a ORDER BY r LIMIT 0,$count;");
        }
//        $query = self::query();
//        "SELECT * FROM address WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM address))) ORDER BY id LIMIT 0,10";
//        $query->where('status', self::kStatusPublish);
//        $query->where('type', $type);
//        if (is_numeric($curAid)) {
//            $query->where('id', '<>', $curAid);
//        }
//        return $query->take($count)->get();
    }


    /**
     * 获取相关新闻方法
     * @param $labels
     * @param $count
     * @return array
     */
    public static function liveRelationArticle(array $labels, $count = 10) {
        $lidStr = "";
        foreach ($labels as $label) {
            $labelEntity = Label::query()->where('label', $label)->first();
            if (!isset($labelEntity)) continue;
            $id = $labelEntity->id;
            if (empty($lidStr)) {
                $lidStr .= $id;
            } else {
                $lidStr .= ",".$id;
            }
        }
        if(strlen($lidStr) <= 0) return [];

        $query = PcArticle::query();
        $query->join('label_articles', 'label_articles.article_id', '=', 'pc_articles.id');
//        $query->whereExists(function ($exQuery) use ($lidStr) {
//            $exQuery->selectRaw("1");
//            $exQuery->from("label_groups");
//            $exQuery->whereRaw("lid_main in (".$lidStr.")");
//            $exQuery->whereRaw("(lid_same = label_articles.label_id or label_articles.label_id in (".$lidStr.") ) ");
//        });
        $query->whereRaw('label_articles.label_id in ('.$lidStr.')');
        $query->where('pc_articles.status', PcArticle::kStatusPublish);
        $query->orderByDesc("pc_articles.publish_at");
        $query->take($count);
        $query->select("pc_articles.*");

        return $query->get();
    }


}