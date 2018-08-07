<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/7
 * Time: 10:00
 */

namespace App\Http\Controllers\Admin\Article;


use App\Models\Article\PcArticleType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ArticleTypeController extends Controller
{

    public function types(Request $request) {
        $name = $request->input('name');
        $name_en = $request->input('name_en');
        $status = $request->input('status');
        $query = PcArticleType::query();
        if (!empty($name)) {
            $query->where("name", "like", "%" . $name . "%");
        }
        if (!empty($name_en)) {
            $query->where("name_en", "like", "%" . $name_en . "%");
        }
        if (in_array($status, [PcArticleType::kStatusShow, PcArticleType::kStatusHide])) {
            $query->where('status', $status);
        }
        $query->orderByRaw("ifNull(od, 999)");
        $page = $query->paginate(20);
        $result['page'] = $page;
        return view("admin.article.types", $result);
    }

    /**
     * 保存类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveType(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $name_en = $request->input('name_en');
        $status = $request->input('status');
        $od = $request->input('od');

        if (empty($name)) {
            return back()->with(['code'=>401, 'error'=>'分类名称不能为空']);
        }
        if (empty($name_en)) {
            return response()->json(['code'=>401, 'error'=>'英文名称不能为空']);
        }
        if (!in_array($status, [PcArticleType::kStatusShow, PcArticleType::kStatusHide])) {
            return back()->with(['code'=>401, 'error'=>'分类状态错误']);
        }
        if (isset($od) && !is_numeric($od)) {
            return back()->with(['code'=>401, 'error'=>'排序只能位数字']);
        }
        if (is_numeric($id)) {
            $type = PcArticleType::query()->find($id);
        }
        if (!isset($type)) {
            $type = new PcArticleType();
        }
        try {
            $type->name = $name;
            $type->name_en = $name_en;
            $type->status = $status;
            $type->od = $od;
            $type->save();
        } catch (\Exception $exception) {
            return back()->with(['code'=>500, 'error'=>'保存失败']);
        }
        return back()->with(['code'=>500, 'success'=>'保存成功']);
    }

}