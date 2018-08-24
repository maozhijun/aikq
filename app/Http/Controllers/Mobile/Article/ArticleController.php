<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/15
 * Time: 17:44
 */

namespace App\Http\Controllers\Mobile\Article;


use App\Models\Article\PcArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ArticleController extends Controller
{

    const PageSize = 20;

    /**
     * 移动资讯首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articles(Request $request) {
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(self::PageSize, ['*'], '', 1);
        return $this->articlesHtml($articles);
    }


    public function detail(Request $request, $type, $date, $id) {
        $article = PcArticle::query()->find($id);
        if (!isset($article)) {
            return abort(404);
        }
        if ($article->status != PcArticle::kStatusPublish) {
            return abort(404);
        }

        return $this->detailHtml($article);
    }

    public function articlesHtml($articles) {
        $result['page'] = $articles;
        $result['title'] = '体育新闻资讯-爱看球直播';
        $result['keywords'] = '体育,资讯';
        $result['description'] = '最新最全的体育资讯';
        $result['h1'] = '体育新闻资讯';
        return view('mobile.articles.news', $result);
    }

    public function articlesCell($articles) {
        return view('mobile.articles.news_cell', ['page'=>$articles]);
    }

    /**
     * html
     * @param PcArticle $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detailHtml(PcArticle $article) {
        $type = $article->type_obj;
        if (!isset($type)) {
            $typeName = '其他资讯';
        } else {
            $typeName = $type->name;
        }
        $result['article'] = $article;
        $result['h1'] = $article->title;
        $result['title'] = $article->title . "_" . $typeName . "-爱看球直播";
        $result['keywords'] = str_replace('，', ',', $article->labels);
        $result['description'] = $article->digest;

        return view("mobile.articles.detail", $result);
    }

}