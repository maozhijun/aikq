<?php

namespace App\Console\HtmlStaticCommand\Article;

use App\Console\HtmlStaticCommand\BaseCommand;
use App\Models\Article\PcArticle;
use Illuminate\Http\Request;

class ArticleDetailCommand extends BaseCommand
{
    const ARTICLE_PAGE_KEY = "ARTICLE_PAGE_KEY";

    protected function command_name()
    {
        return "article_all_detail";
    }

    protected function description()
    {
        return "文章分页列表静态化";
    }

    protected function onCommonHandler(Request $request)
    {
        $articles = PcArticle::query()->where('status', PcArticle::kStatusPublish)->get();
        foreach ($articles as $article){
            $ch = curl_init();
            $url = env('WWW_URL').'/static/article/'.$article->id;
            echo "$url <br>";
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);//8秒超时
            curl_exec ($ch);
            curl_close ($ch);
        }
        return;
    }
}