<?php

namespace App\Console\HtmlStaticCommand\Article;

use App\Console\HtmlStaticCommand\BaseCommand;
use App\Http\Controllers\PC\Article\ArticleController;
use App\Models\Article\PcArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ArticlePageCommand extends BaseCommand
{
    const ARTICLE_PAGE_KEY = "ARTICLE_PAGE_KEY";

    protected function command_name()
    {
        return "article_page";
    }

    protected function description()
    {
        return "文章分页列表静态化";
    }

    protected function onCommonHandler(Request $request)
    {
        //静态化 资讯分页列表
        //获取分页信息
        $page = 1;
        $indexPage = 4;

        //静态化资讯第一页
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(ArticleController::PageSize, ['*'], '', $page);
        $con = new ArticleController();
        $html = $con->newsHtml($articles);
        if (!empty($html)) {
            Storage::disk("public")->put("/www/news/index.html", $html);
        }

        //mobile只静态化首页
        $wapCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
        $wapIndex = $wapCon->articlesHtml($articles);
        if (!empty($wapIndex)) {
            Storage::disk("public")->put(\App\Http\Controllers\Mobile\UrlCommonTool::MOBILE_STATIC_PATH."/news/index.html", $wapIndex);
        }

        //mip只静态化首页
        $mipCon = new \App\Http\Controllers\Mip\Article\ArticleController();
        $mipIndex = $mipCon->articlesHtml($articles);
        if (!empty($mipIndex)) {
            Storage::disk("public")->put(\App\Http\Controllers\Mip\UrlCommonTool::MIP_STATIC_PATH."/news/index.html", $wapIndex);
        }

        $lastPage = $articles->lastPage();
        if ($lastPage > 1) {
            //静态化其他资讯页
            $page = $this->getCachePage(self::ARTICLE_PAGE_KEY);
            $page = $page >= $lastPage ? 2 : $lastPage;
            $lastPage = $lastPage > $indexPage ? $indexPage : $lastPage;
            for (; $page <= $lastPage; $page++) {
                $this->staticNewsHtml($page);
            }
            $this->setCachePage(self::ARTICLE_PAGE_KEY, $page);
        }
    }

    /**
     * 静态化
     * @param $page
     */
    public function staticNewsHtml($page) {
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(ArticleController::PageSize, ['*'], '', $page);
        $con = new ArticleController();
        $html = $con->newsHtml($articles);
        if (!empty($html)) {
            Storage::disk("public")->put("/www/news/index" . $page . ".html", $html);
        }
        $wapCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
        $wapPageHtml = $wapCon->articlesCell($articles);
        if (!empty($wapPageHtml)) {
            Storage::disk("public")->put("/m/news/page" . $page . ".html", $wapPageHtml);
        }
    }

    /**
     * 获取有效的主播房间
     * @param $key
     * @return array|mixed
     */
    public function getCachePage($key) {
        $page = Redis::get($key);
        if (empty($page)) {
            $page = 2;
        }
        return $page;
    }

    public function setCachePage($key, $page) {
        Redis::set($key, $page);
    }
}