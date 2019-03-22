<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 11:42
 */

namespace App\Http\Controllers\Admin\Article;


use App\Http\Controllers\Admin\UploadTrait;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\StaticController;
use App\Http\Controllers\Sync\LabelController;
use App\Jobs\TagStatic;
use App\Models\Article\Author;
use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleDetail;
use App\Models\Article\PcArticleType;
use App\Models\HCT\ForeignArticle;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use EasyWeChat\Support\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{

    use UploadTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articles(Request $request)
    {
        $type = $request->input("type");
        $title = $request->input("title");
        $author = $request->input("author");

        $query = PcArticle::query();
        if (!empty($author)) {
            $query->where('author', 'like', '%' . $author . '%');
        }
        if (is_numeric($type)) {
            $query->where('type', $type);
        }

        if (!empty($title)) {
            $query->where('title', 'like', '%' . $title . '%');
        }
        $query->orderBy('status')->orderBy('publish_at', 'desc');
        $articles = $query->paginate(40);

        $articles->appends($request->all());

        $types = PcArticleType::allTypes();
        $t_names = [];
        foreach ($types as $type) {
            $t_names[$type->id] = $type->name;
        }

        $result['articles'] = $articles;
        $result['t_names'] = $t_names;
        return view('admin.article.list', $result);
    }

    /**
     * 文章编辑页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $id = $request->input("id");
        if (isset($id)) {
            $article = PcArticle::query()->find($id);
            $result['article'] = $article;

            $array = TagRelation::tagCellArray(TagRelation::kTypeArticle, $id);
            $result = array_merge($result, $array);
        } else {
            $result["sports"] = Tag::sports();
        }

        $types = PcArticleType::allTypes();
        $result['types'] = $types;
        $result['authors'] = [];//$authors;
        return view('admin.article.edit', $result);
    }

    /**
     * 保存文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $title = $request->input('title', '');
        $title = trim($title);
        $resource = $request->input("resource");//来源
        $digest = $request->input('digest', '');//摘要
        $digest = trim($digest);
        $author = $request->input('author', '');//作者
        $labels = $request->input('labels', '');//标签
        $labels = str_replace('，', ',', $labels);
        $labels = preg_replace('/[\s|,]+/', ',', $labels);
        $type = $request->input("type");//类型
        $cover = $request->input('cover', '');//封面
        $content = $request->input('content', '');//内容
        $images = $request->input('images');//图片
        $action = $request->input('action', '');
        $tags = $request->input("tags");//2019-02-20 标签
        $sport = $request->input("sport");//足球、篮球标签

        $user = $request->_account;//当前登录用户。

        $title_len = mb_strlen($title);
        $digest_len = mb_strlen($digest);
        if (!is_numeric($type)) {
            return response()->json(['code' => 403, 'error' => '请选择分类']);
        }

        if ($title_len == 0 || $digest_len == 0) {
            return response()->json(['code' => 403, 'error' => '标题、摘要不能为空。']);
        }

        if ($type != 12) {
            if ($title_len < 11 || $title_len > 31) {
                return response()->json(['code' => 403, 'error' => '标题必须不少于10字符，不能多于30字符']);
            }
            if ($digest_len < 30 || $digest_len > 100) {
                return response()->json(['code' => 403, 'error' => '摘要必须不少于30字符，不能多于100字符']);
            }
        }
        if (!is_numeric($sport) || !Tag::isFirstTag($sport)) {
            return response()->json(['code' => 403, 'error' => '请选择竞技']);
        }
        if (mb_strlen($content) < 10 || mb_strlen($content) > 100000) {
            return response()->json(['code' => 403, 'error' => '内容必须大于100字符，小于100000']);
        }
        if (empty($author)) {
            return response()->json(['code' => 403, 'error' => '必须填写作者']);
        }

        if ($request->has('id')) {//更新
            $article = PcArticle::query()->find($request->input('id', ''));
            if (!isset($article)) {
                return response()->json(['code' => 403, 'error' => '无效的文章']);
            }
            $article->u_uid = $user->id;
        } else {//新建
            $article = new PcArticle();
            $article->read_count = 0;
            $article->status = 0;
            $article->c_uid = $user->id;
        }

        //创建作者
        $author_model = Author::query()->where("name", $author)->first();
        if (!isset($author_model)) {
            $author_model = new Author();
            $author_model->name = $author;
            $author_model->save();
        }
        $author_id = $author_model->id;

        $article->original = $request->input('original',0) ? 1 : 0;
        $article->title = $title;
        $article->digest = $digest;
        $article->resource = $resource;
        if (!empty($cover)) {
            if (starts_with($cover, '/')) {
                $cover = env('CDN_URL') . $cover;
            } else if ( !str_contains($cover, env('CDN_URL')) ) {
                $upload = $this->saveUrlFile($cover, 'cover');
                $cover = $upload->getEvnUrl();
            }
        }
        $article->cover = $cover;
        $article->type = $type;
        $article->author = $author;
        $article->labels = $labels;
        $article->author_id = $author_id;
        if ( ($article->status == 0 || is_null($article->status)) && $action == 'publish') {
            $article->status = 1;
            $article->publish_at = date('Y-m-d H:i:s');
        }
        $controller = $this;
        $hasId = $request->has('id');

        //文章内容处理 开始
        $content = preg_replace("/[&nbsp;|\s]*(<img.*?src=[\"|\'].*?[\"|\'][^>]*>)/is", '$1', $content);//消除图片空格 //$content
        if (isset($images)) {
            $images = explode('@@@', $images);
            foreach ($images as $image) {
                if (!str_contains($image, env('CDN_URL'))) {
                    $upload = $this->saveUrlFile($image, 'uploads');
                    $content = str_replace($image, $upload->getEvnUrl(), $content);
                }
            }
        }

        $exception = DB::transaction(function () use ($article, $controller, $hasId, $content, $labels, $tags, $sport) {
            if ($article->status == 1) {
                $article->url = $article->getUrl();
            }
            $article->save();
            if (!$hasId) {
                $article->author_obj->article_count += 1;
                $article->author_obj->save();
            }
            $detail = PcArticleDetail::query()->find($article->id);
            if (!isset($detail)) {
                $detail = new PcArticleDetail();
                $detail->id = $article->id;
            }
            $detail->content = $content;
            $detail->save();

            //保存文章标签关系
//            $list = explode(",", $labels);
//            foreach ($list as $label) {
//                $labelEntity = LabelController::saveLabel($label);
//                LabelController::saveLabelArticle($labelEntity, $article->id, $article->publish_at);
//            }
            $tagArray = json_decode($tags, true);
            $tagArray = is_null($tagArray) ? [] : $tagArray;
            TagRelation::saveArticleTagRelation($sport, $article->id, $tagArray);
        });

        if (isset($exception)) {
            Log::error($exception);
            return response()->json(['code' => 403, 'error' => '数据库异常']);
        }

        //文章内容处理 结束
        $fid = $request->input('fid');
        if (is_numeric($fid)){
            $foreign = ForeignArticle::query()->find($fid);
            if (isset($foreign)){
                $foreign->aid = $article->id;
                $foreign->status = $article->status == PcArticle::kStatusPublish ? ForeignArticle::kStatusValid : $foreign->status;
                $foreign->save();
            }
        }

        if($article->status == PcArticle::kStatusPublish) {
            $con = new \App\Http\Controllers\PC\Article\ArticleController();
            $con->generateHtml($article);//生成静态文件
        }

        $type_obj = $article->type_obj;
        $type_name_en = isset($type_obj) ? $type_obj->name_en : 'other';
        $tmp = CommonTool::getArticleDetailUrl($type_name_en, $article->id);

        dispatch(new TagStatic(TagRelation::kTypeArticle, $article->id));

        return response()->json(['code' => 0, 'id' => $article->id, 'action' => $action, 'url' => $tmp]);
    }

    /**
     * 删除文章
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return back()->with('error', '参数错误');
        }
        $article = PcArticle::query()->find($id);
        if (!isset($article)) {
            return back()->with('error', '无效的文章');
        }
        DB::transaction(function () use ($article, $id) {
            $path = $article->path;

            $pai = PcArticleDetail::query()->find($id);
            if(isset($pai)) {
                $pai->delete();//删除文章内容。
            }
            $article->delete();
            if (!empty($path)) {
                //Storage::delete('public/' . $path);
            }
            //删除文件对应的标签关系
            TagRelation::deleteTagRelations(TagRelation::kTypeArticle, $id);
        });
        return back()->with('success', '删除成功');
    }


    public function publish(Request $request)
    {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return back()->with('error', '参数错误');
        }
        $article = PcArticle::query()->find($id);
        if (!isset($article)) {
            return back()->with('error', '无效的文章');
        }
        if ($article->status == 0) {
            $article->status = 1;
            $article->publish_at = date('Y-m-d H:i:s');

            $exception = DB::transaction(function () use ($article) {
                if (empty($article->url)) {
                    $article->url = $article->getUrl();
                }
                $article->save();
            });
            if (!isset($exception)) {
                if (isset($article->url)) {
                    $con = new \App\Http\Controllers\PC\Article\ArticleController();
                    $con->generateHtml($article);//生成静态文件
                }
                dispatch(new TagStatic(TagRelation::kTypeArticle, $article->id));
                return back()->with('success', '发布成功');
            } else {
                return back()->with('success', '数据错错，发布失败');
            }
        } else {
            return back()->with('error', '无效的文章状态');
        }
    }

    /**
     * 隐藏文章
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hide(Request $request)
    {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return back()->with('error', '参数错误');
        }
        $article = PcArticle::query()->find($id);
        if (!isset($article)) {
            return back()->with('error', '无效的文章');
        }
        if ($article->status == 1) {
            $article->status = 2;
            $article->save();
            dispatch(new TagStatic(TagRelation::kTypeArticle, $article->id));
            return back()->with('success', '隐藏成功');
        } else {
            return back()->with('error', '无效的文章状态');
        }
    }

    /**
     * 显示文章
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Request $request)
    {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return back()->with('error', '参数错误');
        }
        $article = PcArticle::query()->find($id);
        if (!isset($article)) {
            return back()->with('error', '无效的文章');
        }
        if ($article->status == 2) {
            $article->status = 1;
            $article->save();
            dispatch(new TagStatic(TagRelation::kTypeArticle, $article->id));
            return back()->with('success', '显示成功');
        } else {
            return back()->with('error', '无效的文章状态');
        }
    }

}