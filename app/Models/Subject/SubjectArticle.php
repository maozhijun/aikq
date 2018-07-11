<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/3
 * Time: 12:37
 */

namespace App\Models\Subject;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * 专题资讯
 * Class SubjectArticle
 * @package App\Models\CMS\Subject
 */
class SubjectArticle extends Model
{
//    protected $connection = "match";
    const kStatusDraft = 2, kStatusPublish = 1;//1：发布，2：草稿。

    /**
     * 按时间顺序排序
     * @param $slid
     * @param int $size
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getArticles($slid, $size = 10) {
        $query = self::query();
        $query->where('subject_articles.s_lid', $slid);
        $query->orderBy('subject_articles.updated_at', 'desc');
        $query->select("subject_articles.*");
        return $query->take($size)->get();
    }

}