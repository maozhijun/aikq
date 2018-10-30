<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/3
 * Time: 12:40
 */

namespace App\Http\Controllers\Admin\Subject;


use App\Http\Controllers\Admin\UploadTrait;
use App\Models\Subject\SubjectArticle;
use App\Models\Subject\SubjectLeague;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class SubjectArticleController extends Controller
{
    use UploadTrait;

    /**
     * 资讯专题
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function articles(Request $request) {
        $query = SubjectArticle::query();
        $query->orderByDesc('created_at');
        $page = $query->paginate(15);

        $result['s_leagues'] = SubjectLeague::getAllLeagues();
        $result['page'] = $page;
        return view('admin.subject.article.list', $result);
    }

    /**
     * 保存文章
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function saveArticle(Request $request) {
        $id = $request->input('id');
        $title = $request->input('title');
        $link = $request->input('link');
        $status = $request->input('status');
        $s_lid = $request->input('s_lid');

        if (empty($title)) {
            return back()->with('error', '标题不能为空');
        }
        if (mb_strlen($title) > 30) {
            return back()->with('error', '标题不能大于30字');
        }
        if (!is_numeric($s_lid)) {
            return back()->with('error', '请选择专题联赛');
        }
        if (empty($link)) {
            return back()->with('error', '链接不能为空');
        }
        if (!in_array($status, [SubjectArticle::kStatusPublish, SubjectArticle::kStatusDraft])) {
            return back()->with('error', '状态错误');
        }
        $sl = SubjectLeague::query()->find($s_lid);
        if (!isset($sl)) {
            return back()->with('error', '专题联赛不存在');
        }

        if (is_numeric($id)) {
            $article = SubjectArticle::query()->find($id);
        }
        if (!isset($article)) {
            $article = new SubjectArticle();
        }

        try {
            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $upload = $this->saveUploadedFile($file, 'cover');
                $cover = $upload->getEvnUrl();
                $article->cover = $cover;
            }
            $article->title = $title;
            $article->status = $status;
            $article->link = $link;
            $article->s_lid = $s_lid;
            $article->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return back()->with('error', '保存失败');
        }

        return back()->with('success', '保存成功');
    }

    /**
     * 删除文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArticle(Request $request) {
        $id = $request->input('id');
        if (is_numeric($id)) {
            $article = SubjectArticle::query()->find($id);
            if (isset($article)) {
                $article->delete();
            }
        }
        return response()->json(['code'=>200, 'msg'=>'删除成功']);
    }


}