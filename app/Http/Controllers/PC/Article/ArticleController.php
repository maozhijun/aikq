<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 16:56
 */

namespace App\Http\Controllers\PC\Article;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleType;
use App\Models\Subject\SubjectLeague;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{

    const PageSize = 15;
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

        $result['title'] = '体育新闻资讯-爱看球直播';
        $result['keywords'] = '体育,资讯';
        $result['description'] = '最新最全的体育资讯';
        $result['check'] = "news";
        $result['ma_url'] = self::getMobileHttpUrl("/news/");
        return view('pc.article.news', $result);
    }

    /**
     * 专题文章列表页
    */
    public function subjectNews(Request $request, $name_en, $page = 1) {
        if (!is_numeric($page) || $page < 1) {
            return abort(404);
        }
        $query = PcArticle::getPublishQuery($name_en);
        if (isset($query)) {
            $articles = $query->paginate(self::PageSize, ['*'], '', $page);
            return $this->subjectNewsHtml($name_en, $articles);
        } else {
            return abort(404);
        }
    }

    public function subjectNewsHtml($name_en, $articles) {
        $data = array_key_exists($name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$name_en] : null;
        if (isset($data)) {
            $data['name_en'] = $name_en;
            $result['zhuanti'] = $data;
        }

        $result['articles'] = $articles;
        $result['articles'] = $articles;

        $result['title'] = '体育新闻资讯-爱看球直播';
        $result['keywords'] = '体育,资讯';
        $result['description'] = '最新最全的体育资讯';
        $result['check'] = "news";
        $result['ma_url'] = self::getMobileHttpUrl("/$name_en/news/");
        return view('pc.article.v2.subject_news', $result);
    }

    /**
     * 文章终端
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
        $detail = PcArticle::query()->find($id);
        if (!isset($detail)) {
            return abort(404);
        }
        $type_obj = $detail->type_obj;
        if ($type_obj->name_en != $t_name) {
            return abort(404);
        }
        $isBaidu = str_contains($request->header('User-Agent'),'http://www.baidu.com/search/spider.html');
        return $this->detailHtml($detail,$isBaidu);
    }

    /**
     * 文章终端
     * @param Request $request
     * @param $name_en
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function detailByName(Request $request, $name_en, $id) {
        $detail = PcArticle::query()->find($id);
        if (!isset($detail)) {
            return abort(404);
        }
        $type_obj = $detail->type_obj;
        if ($type_obj->name_en != $name_en) {
            return abort(404);
        }
        $isBaidu = str_contains($request->header('User-Agent'),'http://www.baidu.com/search/spider.html');
        return $this->detailHtml($detail,$isBaidu);
    }

    /**
     * 文章终端html
     * @param PcArticle $detail
     * @param bool $isBaidu 是否百度
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function detailHtml(PcArticle $detail,$isBaidu = false) {
        if ($isBaidu){
            $detail->baidu_spider_count = $detail->baidu_spider_count + 1;
            $detail->save();
        }

        $type = $detail->type_obj;
        if (!isset($type)) {
            $typeName = '其他资讯';
        } else {
            $typeName = $type->name;
        }

        $result['article'] = $detail;
        $result['title'] = $detail->title . "_" . $typeName . "-爱看球直播";
        $result['keywords'] = str_replace('，', ',', $detail->labels);
        $result['description'] = $detail->digest;
        $data = array_key_exists($type->name_en, Controller::SUBJECT_NAME_IDS) ? Controller::SUBJECT_NAME_IDS[$type->name_en] : null;
        if (isset($data)) {
            $data['name_en'] = $type->name_en;
            $result['zhuanti'] = $data;
        }
        //相关文章
        $res = PcArticle::relationsArticle($detail->id, $detail->type, 10,$isBaidu);
        $result['res'] = $res;
        $result['ma_url'] = self::getMobileHttpUrl($detail->url);

        $result = array_merge($result, $this->html_var);

//        dump($detail->getContent());

//        return view('pc.article.article', $result);
        return view('pc.article.v2.article', $result);
    }

    /**
     * 文章终端页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function detailLives(Request $request) {
        $cache = Storage::get('/public/static/json/pc/lives.json');
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
        $type_name_en = isset($type_obj) ? $type_obj->name_en : 'other';

        $path = CommonTool::getArticleDetailPath($type_name_en, $article->id);

        $article->disks = $disks;
        $article->path = $path;
        $article->url = CommonTool::getArticleDetailUrl($type_name_en, $article->id);
        $article->save();

        $html = $this->detailHtml($article);
        Storage::disk("public")->put('/www'.$path, $html);

        $mobileCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
        $wapHtml = $mobileCon->detailHtml($article);
        Storage::disk("public")->put('/m'.$path, $wapHtml);

        $mipCon = new \App\Http\Controllers\Mip\Article\ArticleController();
        $mipHtml = $mipCon->detailHtml($article);
        Storage::disk("public")->put('/mip'.$path, $mipHtml);
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
    public function appNewsList(Request $request, $type_en) {
        $page = $request->input('page',1);
        if ($page<=0){
            $page =1;
        }

        $typeId = -1;
        $typeObj = PcArticleType::getTypeByTypeEn($type_en);
        if ($typeObj != null) {
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
            'pageNo'=>$page,
            'pageSize'=>$articles->lastPage(),
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

    public function logBaiduSpider(Request $request,$id){
        //不在这里ajax记录了,貌似不准
//        return response()->json(array('code'=>0));
        $article = PcArticle::find($id);
        if (isset($article)) {
            $article->baidu_spider_count = isset($article->baidu_spider_count) ? ($article->baidu_spider_count + 1) : 1;
            $article->save();
        }
        return response()->json(array('code'=>0));
    }
}