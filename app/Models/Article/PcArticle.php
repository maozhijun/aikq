<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 12:09
 */

namespace App\Models\Article;


use App\Models\Admin\Account;
use Illuminate\Database\Eloquent\Model;

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
        $type = PcArticleType::query()->find($this->type);
        if(isset($type)){
            $name_en = $type->name_en;
        } else{
            $name_en = 'detail';
        }
        $path = '';
        $mid = $this->id;
        if ($mid > 0) {
            $first = date('Ymd', strtotime($this->publish_at));
            $path = '/news/'.$name_en.'/' . $first . '/'  . $mid . '.html';
        }
        return $path;
    }


    public static function getPublishQuery() {
        $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
        $query->orderByDesc('publish_at');
        return $query;
    }

}