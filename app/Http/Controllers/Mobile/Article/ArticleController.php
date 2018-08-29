<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/15
 * Time: 17:44
 */

namespace App\Http\Controllers\Mobile\Article;


use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleType;
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

    /**
     * m站分页
     * @param Request $request
     * @param $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articlesPage(Request $request, $page) {
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(self::PageSize, ['*'], '', $page);
        return $this->articlesCell($articles);
    }

    /**
     * 移动终端
     * @param Request $request
     * @param $param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function detail(Request $request, $param) {
        preg_match("/([a-zA-Z]+)(\d+)/", $param, $matches);
        if (count($matches) != 3) {
            return abort(404);
        }
        $t_name = $matches[1];
        $id = $matches[2];
        $type = PcArticleType::getTypeByTypeEn($t_name);
        $article = PcArticle::query()->find($id);
        if (!isset($article) || !isset($type) || ($type->id != $article->type) ) {
            return abort(404);
        }
        if ($article->status != PcArticle::kStatusPublish) {
            return abort(404);
        }
        return $this->detailHtml($article);
    }

    /**
     * 移动终端 匹配另外的url规则
     * @param Request $request
     * @param $name_en
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function detailByName(Request $request, $name_en, $id) {
        $type = PcArticleType::getTypeByTypeEn($name_en);
        $article = PcArticle::query()->find($id);
        if (!isset($article) || !isset($type) || ($type->id != $article->type) ) {
            return abort(404);
        }
        if ($article->status != PcArticle::kStatusPublish) {
            return abort(404);
        }
        return $this->detailHtml($article);
    }

    /**
     * 列表html
     * @param $articles
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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