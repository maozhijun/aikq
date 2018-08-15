<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 16:56
 */

namespace App\Http\Controllers\PC\Article;


use App\Http\Controllers\Controller;
use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{

    const PageSize = 10;
    const APP_PAGE_SIZE = 20;

    /**
     *
     * @param Request $request
     * @param $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function news(Request $request, $page = 1) {
        if (!is_numeric($page) || $page < 1) {
            return abort(404);
        }
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(self::PageSize, ['*'], '', $page);

        return $this->newsHtml($articles);
    }

    /**
     * @param $articles
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newsHtml($articles) {
        $result['articles'] = $articles;

        $result['title'] = '体育资讯_爱看球';
        $result['keywords'] = '体育,资讯';
        $result['description'] = '最新最全的体育资讯';
        $result['check'] = "news";
        return view('pc.article.news', $result);
    }

    /**
     * 文章终端
     * @param Request $request
     * @param $t_name
     * @param $date
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function detail(Request $request, $t_name, $date, $id) {
        $detail = PcArticle::query()->find($id);
        if (!isset($detail)) {
            return abort(404);
        }
        $type_obj = $detail->type_obj;
        if ($type_obj->name_en != $t_name) {
            return abort(404);
        }
        return $this->detailHtml($detail);
    }

    /**
     * 文章终端html
     * @param PcArticle $detail
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function detailHtml(PcArticle $detail) {
        $result['article'] = $detail;
        $result['title'] = $detail->title . "_爱看球";
        $result['keywords'] = str_replace('，', ',', $detail->labels);
        $result['description'] = $detail->digest;

        return view('pc.article.article', $result);
    }

    /**
     * 文章终端页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function detailLives(Request $request) {
        $cache = Storage::get('/public/static/json/lives.json');
        $json = json_decode($cache, true);
        if (is_null($json)){
            return "";
        }
        $matches = $json['matches'];
        $array = [];
        foreach ($matches as $time=>$mArray) {
            foreach ($mArray as $match) {
                if (count($array) > 15) break;
                $array[] = $match;
            }
        }
        $result['matches'] = $array;
        return view('pc.article.article_live_cell', $result);
    }

    /**
     * 保存文章终端html文件
     * @param PcArticle $article
     */
    public function generateHtml(PcArticle $article) {
        $type_obj = $article->type_obj;
        $disks = 'news';
        $type_name_en = isset($type_obj) ? $type_obj->name_en : 'detail';
        $publish_date = date('Ymd', strtotime($article->publish_at));
        $path = '/' . $disks . '/' . $type_name_en . '/' . $publish_date . '/' . $article->id . '.html';
        $article->disks = $disks;
        $article->path = $path;
        $article->save();
        $html = $this->detailHtml($article);
        Storage::disk("public")->put($path, $html);
    }


    //================================================================================================================//

    /**
     * 静态化文章终端
     * @param Request $request
     * @param $id
     */
    public function staticDetailHtml(Request $request, $id) {
        $detail = PcArticle::query()->find($id);
        if (!isset($detail)) {
            dump("静态化失败：无此文章");
            return;
        }
        $this->generateHtml($detail);
    }

    //=================================================================//
    //==========================app相关=================================//

    /**
     * 文章列表
     */
    public function appNewsList(Request $request, $type_en, $page = 1) {
        if (!is_numeric($page) || $page < 1) {
            return response()->json(array(
                'code'=>404,
                'msg'=>"页码错误"
            ));
        }

        $typeId = -1;
        $typeObj = PcArticleType::getTypeByTypeEn($type_en);
        if ($type_en != null) {
            $typeId = $typeObj->id;
        }

        $query = PcArticle::getPublishQuery();
        if ($typeId > 0) {
            $query->where('type', $typeId);
        }
        $articles = $query->paginate(self::APP_PAGE_SIZE, ['*'], '', $page);

        $results = array();
        foreach ($articles as $article) {
            $results[] = $article->appModel();
        }
        return response()->json(array(
            'code'=>0,
            'data'=>$results
        ));
    }

    /**
     * 类型
     */
    public function appNewsTypes(Request $request) {
        $results = array();

        $allType = new PcArticleType();
        $allType->id = -1;
        $allType->name = "全部";
        $results[] = $allType;

        $types = PcArticleType::allTypes();
        foreach ($types as $type) {
            $results[] = $type->appModel();
        }

        return response()->json(array(
            'code'=>0,
            'data'=>$results
        ));
    }

    /**
     * 资讯终端
     */
    public function appNewsDetail(Request $request, $t_name, $id) {
        $detail = PcArticle::query()->find($id);
        if (!isset($detail)) {
            return response()->json(array(
                'code'=>404,
                'msg'=>"未能找到该文章"
            ));
        }
        $type_obj = $detail->type_obj;
        if ($type_obj->name_en != $t_name) {
            return response()->json(array(
                'code'=>404,
                'msg'=>"文章类型匹配失败"
            ));
        }
        return response()->json(array(
            'code'=>0,
            'data'=>$detail->appModel(true)
        ));
    }
}