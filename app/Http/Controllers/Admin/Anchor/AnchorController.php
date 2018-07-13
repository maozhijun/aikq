<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/13
 * Time: 下午3:12
 */

namespace App\Http\Controllers\Admin\Anchor;

use App\Models\Anchor\Anchor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AnchorController extends Controller
{
    /**
     * 主播列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function anchors(Request $request){
        $query = Anchor::query();
        $query->orderByDesc('created_at');
        $page = $query->paginate(15);

        $result['page'] = $page;
        return view('admin.anchor.anchor_list', $result);
    }

    /**
     * 更新主播信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request){
        if ($request->input('id',0) == 0){
            return back()->with('error', 'id不能为空');
        }
        $anchor = Anchor::find($request->input('id'));
        if (is_null($anchor)){
            return back()->with('error', '找不到该主播');
        }
        $anchor->name = $request->input('name');
        $anchor->phone = $request->input('phone');
        $anchor->hot = $request->input('hot');
        $cover = $request->input('icon');
        if (isset($cover)) {
            $anchor->icon = $cover;
        }
        if ($anchor->save()){
            return back()->with('success', '保存成功');
        }
        else{
            return back()->with('error', '保存失败');
        }
    }

    /**
     * 删除主播
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delAnchor(Request $request){
        if ($request->input('id',0) == 0){
            return back()->with('error', 'id不能为空');
        }
        $anchor = Anchor::find($request->input('id'));
        if (is_null($anchor)){
            return back()->with('error', '找不到该主播');
        }
        if ($anchor->delete()){
            return back()->with('success', '删除成功');
        }
        else{
            return back()->with('error', '删除失败');
        }
    }
}