<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/16
 * Time: 15:41
 */

namespace App\Http\Controllers\Admin\Match;


use App\Http\Controllers\Controller;
use App\Models\Match\RelationVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RelationVideoController extends Controller
{

    public function videos(Request $request) {
        $title = $request->input('title');
        $label = $request->input('label');

        $query = RelationVideo::query();
        if (!empty($title)) {
            $query->where('title', 'like', '%'.$title.'%');
        }
        $query->orderByDesc('created_at');
        $page = $query->paginate();
        $page->appends($request->all());

        $result['page'] = $page;
        return view('admin.match.relation.videos', $result);
    }


    /**
     * 保存相关链接
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveVideo(Request $request) {
        $id = $request->input('id');
        $title = $request->input('title');
        $cover = $request->input('cover');
        $labels = $request->input('labels');
        $link = $request->input('link');

        if (empty($title) || mb_strlen(trim($title)) == 0) {
            return response()->json(['code'=>401, 'msg'=>'标题不能为空']);
        }
        if (mb_strlen($title) > 64) {
            return response()->json(['code'=>401, 'msg'=>'标题不能超过64字符']);
        }

        if (empty($labels) || mb_strlen(trim($labels)) == 0) {
            return response()->json(['code'=>401, 'msg'=>'标签不能为空']);
        }
        if (mb_strlen($labels) > 255) {
            return response()->json(['code'=>401, 'msg'=>'标签不能超过255字符']);
        }

        if (empty($link) || mb_strlen(trim($link)) == 0) {
            return response()->json(['code'=>401, 'msg'=>'链接不能为空']);
        }
        if (mb_strlen($link) > 255) {
            return response()->json(['code'=>401, 'msg'=>'链接不能超过255字符']);
        }

        if (is_numeric($id)) {
            $video = RelationVideo::query()->find($id);
        }
        if (!isset($video)) {
            $video = new RelationVideo();
        }

        try {
            $video->title = $title;
            $video->cover = $cover;
            $video->labels = $labels;
            $video->link = $link;
            $video->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['code'=>500, 'msg'=>'保存失败']);
        }

        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

    /**
     * 删除相关视频
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delVideo(Request $request) {
        $id = $request->input('id');
        if (is_numeric($id)) {
            $video = RelationVideo::query()->find($id);
            if (isset($video)) {
                $video->delete();
            }
        }
        return response()->json(['code'=>200, 'msg'=>'删除成功']);
    }

}