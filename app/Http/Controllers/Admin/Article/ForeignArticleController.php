<?php
/**
 * Created by PhpStorm.
 * User: bj
 * Date: 2018/9/3
 * Time: 22:42
 */

namespace App\Http\Controllers\Admin\Article;


use App\Http\Controllers\Admin\UploadTrait;
use App\Models\Article\Author;
use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleDetail;
use App\Models\Article\PcArticleType;
use App\Models\HCT\ForeignArticle;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ForeignArticleController extends Controller
{

    use UploadTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articles(Request $request)
    {
        $query = ForeignArticle::query();
        $query->where('status','<>',ForeignArticle::kStatusInvaild);
        //$query->orderBy('status');
        $query->orderBy('created_at', 'desc');
//        $query->join('foreign_article_contents','foreign_articles.id','=','foreign_article_contents.id');
//        $query->select('foreign_articles.*','foreign_article_contents.content_en','foreign_article_contents.content_ch');
        $from = $request->input('from');
        $sport = $request->input('sport');
        if (isset($from)){
            $query->where('from','=',$from);
        }
        if (isset($sport)){
            $query->where('sport','=',$sport);
        }
        $articles = $query->paginate(40);
        $result['articles'] = $articles;
        return view('admin.foreign_article.articles', $result);
    }

    /**
     * 文章编辑页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request)
    {
        $id = $request->input("fid");
        $query = ForeignArticle::query();
        $query->find($id);
        $query->join('foreign_article_contents','foreign_articles.id','=','foreign_article_contents.id');
        $query->select('foreign_articles.*','foreign_article_contents.content_en','foreign_article_contents.content_ch');
        $article = $query->get();
        if (count($article) > 0)
            $result['f_article'] = $article[0];
        else
            $result['f_article'] = null;
        if (isset($result['f_article']['aid'])) {
            $aid = $result['f_article']['aid'];
            $article = PcArticle::query()->find($aid);
            $result['article'] = $article;

            $array = TagRelation::tagCellArray(TagRelation::kTypeArticle, $aid);
            $result = array_merge($array, $result);
        }
        if (!isset($result["sports"])) {
            $result["sports"] = Tag::sports();
        }
        $types = PcArticleType::allTypes();
        $result['types'] = $types;
        $result['authors'] = [];//$authors;
        return view('admin.foreign_article.edit', $result);
    }

    /**
     * 修改文章
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return response()->json(array('code'=>-1,'msg'=>'参数错误'));
        }
        $article = ForeignArticle::query()->find($id);
        if (!isset($article)) {
            return response()->json(array('code'=>-1,'msg'=>'无效的文章'));
        }
        $article->status = $request->input('status');
        if ($article->save()) {
            return response()->json(array('code'=>0,'msg'=>'成功'));
        }
    }
}