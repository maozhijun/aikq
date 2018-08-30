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
use App\Models\Subject\SubjectLeague;
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

    public static function getPublishQuery() {
        $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
        $query->orderByDesc('publish_at');
        return $query;
    }

    public function getCover() {
        $cover = $this->cover;
        $local = env('APP_URL');
        $cover = str_replace($local, env('CDN_URL'), $cover);
        return $cover;
    }

    public function appModel($isWithDetail = false) {
        $modelItem = [
            'id'=>$this->id,
            'title'=>$this->title,
            'author_name'=>$this->author,
            'cover'=>$this->getCover(),
            'digest'=>$this->digest,
            'labels'=>$this->labels,
            'elite'=>$this->elite,
            'read_count'=>$this->read_count,
            'resource'=>$this->resource,
            'publish_at'=>strtotime($this->publish_at),
            'url'=>$this->getWebUrl(),
        ];
        if ($isWithDetail) {
            $modelItem['content'] = $this->getContent();
            $modelItem['type'] = $this->type_obj->appModel();
            $modelItem['author'] = $this->author_obj->appModel();
        }
        return $modelItem;
    }

    public static function indexArticles() {
        $key = "indexArticles_Cache";
        $articleCache = Redis::get($key);
        if (empty($articleCache)) {
            $query = self::getPublishQuery();
            $articles = $query->take(30)->get();
            $array = [];
            //文章内容2小时刷新一次
            foreach ($articles as $article) {
                $array[] = ['title'=>$article->title, 'url'=>$article->getUrl()];
            }
            shuffle($array);
            $result = [];
            foreach ($array as $index=>$ar) {
                if ($index >= 12) break;
                $result[] = $ar;
            }
            $articleCache = json_encode($result);
            Redis::setEx($key, 1 * 60 * 60, $articleCache);
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

    public static function relationsArticle($curAid, $type, $count = 10) {
        $query = self::query();
        $query->where('status', self::kStatusPublish);
        $query->where('type', $type);
        if (is_numeric($curAid)) {
            $query->where('id', '<>', $curAid);
        }
        return $query->take($count)->get();
    }
}