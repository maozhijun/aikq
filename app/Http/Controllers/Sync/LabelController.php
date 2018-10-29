<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/17
 * Time: 16:19
 */

namespace App\Http\Controllers\Sync;


use App\Http\Controllers\Controller;
use App\Models\Article\PcArticle;
use App\Models\Label\Label;
use App\Models\Label\LabelArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LabelController extends Controller
{

    public function syncArticleLabel(Request $request) {
        $key = 'LabelController_Last_Id';
        $lastArticleId = Redis::get($key);
        if (empty($lastArticleId))
        {
            $lastArticleId = 0;
        }
        $query = PcArticle::query()->where('status', PcArticle::kStatusPublish);
        $query->where('id', '>', $lastArticleId);
        $query->orderBy('id');
        $articles = $query->take(50)->get();
        foreach ($articles as $article) {
            $labels = $article->labels;
            if (!empty($labels)) {
                $labels = str_replace('，', ',', $labels);
                $list = explode(",", $labels);
                foreach ($list as $label) {
                    $labelEntity = $this->saveLabel($label);
                    $this->saveLabelArticle($labelEntity, $article->id, $article->publish_at);
                }
            }
            $lastArticleId = $article->id;
        }

        Redis::set($key, $lastArticleId);
        dump($lastArticleId);
    }


    public static function saveLabel($label) {
        $obj = Label::query()->where('label', $label)->first();
        if (!isset($obj)) {
            $obj = new Label();
            $obj->label = $label;
            $obj->save();
        }
        return $obj;
    }

    public static function saveLabelArticle($label, $article_id, $publish_at) {
        if (!isset($label)) {
            return;
        }
        $label_id = $label->id;
        $query = LabelArticle::query()->where('label_id', $label_id);
        $query->where('article_id', $article_id);
        $labelArticle = $query->first();
        if (!isset($labelArticle)) {
            $labelArticle = new LabelArticle();
            $labelArticle->label_id = $label_id;
            $labelArticle->article_id = $article_id;
            $labelArticle->publish_at = $publish_at;
            $labelArticle->save();
        }
    }

}