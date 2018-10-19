<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/17
 * Time: 17:47
 */

namespace App\Http\Controllers\Admin\Label;


use App\Models\Label\Label;
use App\Models\Label\LabelGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class LabelController extends Controller
{

    public function labels(Request $request) {
        $label = $request->input('label');
//        $same = $request->input('group');

        $query = Label::query();
        if (!empty($label)) {
            $query->where('label', 'like', '%'.$label.'%');
        }
//        if (!empty($same)) {
//            Label::query()->where('label', 'like', '%'.$same.'%');
//            $query->whereRaw('id');
//        }
        $query->orderByDesc('id');
        $page = $query->paginate(20);
        $page->appends($request->all());

        $result['page'] = $page;
        return view('admin.label.list', $result);
    }


    /**
     * 删除近义词
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delLabelGroup(Request $request) {
        $label_id = $request->input('label_id');
        $same_id = $request->input('same_id');

        if (!is_numeric($label_id) || !is_numeric($same_id)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        try {
            $query = LabelGroup::query();
            $query->where(function ($andQuery) use ($label_id, $same_id) {
                $andQuery->where('lid_main', $label_id);
                $andQuery->where('lid_same', $same_id);
            });
            $query->orWhere(function ($andQuery) use ($label_id, $same_id) {
                $andQuery->where('lid_main', $same_id);
                $andQuery->where('lid_same', $label_id);
            });
            $query->delete();
        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'msg'=>'删除近义词失败']);
            Log::error($exception);
        }
        return response()->json(['code'=>200, 'msg'=>'删除近义词成功']);
    }

    /**
     * 保存近义词
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLabelGroup(Request $request) {
        $label_id = $request->input('label_id');
        $labelStr = $request->input('label');
        if (!is_numeric($label_id) || empty($labelStr)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }

        $label = Label::query()->find($label_id);
        if (!isset($label)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        if ($label->label == $labelStr) {
            return response()->json(['code'=>401, 'msg'=>'两个词语不能相同']);
        }
        try {
            $sameLabel = Label::query()->where('label', $labelStr)->first();
            if (isset($sameLabel)) {
                $sameLabelId = $sameLabel->id;
                $group = LabelGroup::findGroup($sameLabelId, $label_id);
                if (isset($group)) {
                    return response()->json(['code'=>401, 'msg'=>'已存在此近义词']);
                }
            } else {
                $sameLabel = new Label();
                $sameLabel->label = $labelStr;
                $sameLabel->save();
            }

            LabelGroup::saveLabelGroup($label_id, $sameLabel->id);
            $sames = $label->sameLabels();
            foreach ($sames as $otherSame) {
                LabelGroup::saveLabelGroup($sameLabel->id, $otherSame->id);
            }

        } catch (\Exception $exception) {
            return response()->json(['code'=>500, 'msg'=>'系统错误']);
            Log::error($exception);
        }
        return response()->json(['code'=>200, 'msg'=>'保存近义词成功']);
    }

}