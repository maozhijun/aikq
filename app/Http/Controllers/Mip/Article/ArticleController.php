<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/15
 * Time: 17:44
 */

namespace App\Http\Controllers\Mip\Article;


use App\Http\Controllers\Mip\UrlCommonTool;
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
        $result['canonical'] = UrlCommonTool::homeNewsUrl(env('M_URL'));
//        return view('mip.articles.news', $result);
        return view('mip.articles.news', $result);
    }

    public function articlesCell(Request $request, $type) {
        $page = $request->input('page', 1);

        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(self::PageSize, ['*'], '', $page);

        $items = array();
        foreach ($articles as $article) {
            $item = array();
            $item['url'] = $article->getMipUrl();
            $item['cover'] = $article->getCover();
            $item['title'] = $article->title;
            $item['date'] = date('Y.m.d', strtotime($article->publish_at));
            $item['time'] = date('H:i', strtotime($article->publish_at));
            $items[] = $item;
        }

        $result = response()->json(['status'=>0, 'data'=>['items'=>$items]])->getContent();
        $callback = $request->input('callback', "callback");
        return $callback."(".$result.")";
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
        $content = $article->getContent();
        $content = preg_replace("/ border=\"(.*?)\"/", "", $content);
        $content = preg_replace("/ vspace=\"(.*?)\"/", "", $content);
        $content = preg_replace("/ height=\"(.*?)\"/", " layout=\"container\"", $content);
        $content = preg_replace("/ width=\"(.*?)\"/", "", $content);
        $content = preg_replace("/ class=\"(.*?)\"/", "", $content);
        $content = preg_replace("/ style=\"(.*?)\"/", "", $content);
        $content = str_replace("<img", "<mip-img", $content);

        $article->content = $content;

        $result['article'] = $article;
        $result['h1'] = $article->title;
        $result['title'] = $article->title . "_" . $typeName . "-爱看球直播";
        $result['keywords'] = str_replace('，', ',', $article->labels);
        $result['description'] = $article->digest;

        $result['canonical'] = UrlCommonTool::newsDetailUrl($article, env('M_URL'));

        return view("mip.articles.detail", $result);
    }

}