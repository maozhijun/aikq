<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/3
 * Time: 11:30
 */

namespace App\Http\Controllers\Admin\Subject;


use App\Models\Subject\SubjectLeague;
use App\Models\Match\BasketLeague;
use App\Models\Match\League;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * 专题列表
 * Class AkqSubjectController
 * @package App\Http\Controllers\CMS\akq
 */
class SubjectController extends Controller
{

    /**
     * 专题列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subjectLeagues(Request $request) {
        $name = $request->input('name');
        $sport = $request->input('sport');
        $status = $request->input('status');

        $query = SubjectLeague::query();
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if (in_array($sport, [SubjectLeague::kSportFootball, SubjectLeague::kSportBasketball])) {
            $query->where('sport', $sport);
        }
        if (in_array($status, [SubjectLeague::kStatusShow, SubjectLeague::kStatusHide])) {
            $query->where('status', $status);
        }

        $query->selectRaw('*, ifNull(subject_leagues.od, 999) as n_od');
        $query->orderBy('status')->orderBy('n_od');
        $page = $query->paginate(15);
        $page->appends($request->all());
        $result['page'] = $page;

        return view('admin.subject.league.list', $result);
    }

    /**
     * 编辑页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request) {
        $id = $request->input('id');
        $result= [];
        if (is_numeric($id)) {
            $sl = SubjectLeague::query()->find($id);
            $result['sl'] = $sl;
        }
        return view('admin.subject.league.edit', $result);
    }

    /**
     * 查询赛事
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findLeague(Request $request) {
        $sport = $request->input('sport');
        $name = $request->input('name');
        if (empty($name) || !in_array($sport, [SubjectLeague::kSportFootball, SubjectLeague::kSportBasketball])) {
            return response()->json(['code'=>200, 'leagues'=>[]]);
        }
        if ($sport == SubjectLeague::kSportFootball) {
            $query = League::query();
        } else {
            $query = BasketLeague::query();
        }
        $query->where('name', 'like', '%' . $name . '%');
        $leagues = $query->get();
        $json = [];
        foreach ($leagues as $league) {
            $obj['id'] = $league->id;
            $obj['name'] = $league->name;
            $obj['type'] = $league->type;
            $json[] = $obj;
        }
        return response()->json(['code'=>200, 'leagues'=>$json]);
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLeague(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $sport = $request->input('sport');
        $lid = $request->input('lid');
//        $type = $request->input('type');
        $content = $request->input('content');
        $od = $request->input('od');
        $icon = $request->input('icon');

        //判断参数 开始
        if (!is_numeric($lid)) {
            return response()->json(['code'=>401, 'msg'=>'赛事填写错误']);
        }
        if (!in_array($sport, [SubjectLeague::kSportFootball, SubjectLeague::kSportBasketball])) {
            return response()->json(['code'=>401, 'msg'=>'类型填写错误']);
        }
        if (empty($content)) {
            return response()->json(['code'=>401, 'msg'=>'简介不能为空']);
        }
        if (!empty($od) && !is_numeric($od) && $od < 0) {
            return response()->json(['code'=>401, 'msg'=>'排序必须为正整数']);
        }
        if ($sport == SubjectLeague::kSportFootball) {
            $league = League::query()->find($lid);
        } else {
            $league = BasketLeague::query()->find($lid);
        }
        if (!isset($league)) {
            return response()->json(['code'=>401, 'msg'=>'赛事不存在']);
        }
        //判断参数 结束

        try {
            if (is_numeric($id)) {
                $sl = SubjectLeague::query()->find($id);
            }
            if (!isset($sl)) {
                $sl = new SubjectLeague();
            }
            //判断是否存在该赛事 开始
            $query = SubjectLeague::query()->where('sport', $sport)->where('lid', $lid);
            if (isset($sl->id)) {
                $query->where('id', '<>', $id);
            }
            $same = $query->first();
            if (isset($same)) {
                return response()->json(['code'=>401, 'msg'=>'赛事已存在。']);
            }
            //判断是否存在该赛事 结束
            $sl->name = $name;
            $sl->sport = $sport;
            $sl->lid = $lid;
            $sl->type = $league->type;
            $sl->od = $od;
            $sl->content = $content;
            $sl->icon = $icon;
            $sl->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['code'=>500, 'msg'=>'保存失败']);
        }

        return response()->json(['code'=>200, 'msg'=>'保存成功']);
    }

    /**
     * 改变专题状态
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeSL(Request $request) {
        $id = $request->input('id');
        $status = $request->input('status');
        if (!in_array($status, [SubjectLeague::kStatusShow, SubjectLeague::kStatusHide])) {
            return response()->json(['code'=>401, 'msg'=>'状态错误']);
        }
        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        $sl = SubjectLeague::query()->find($id);
        if (!isset($sl)) {
            return response()->json(['code'=>403, 'msg'=>'专题不存在']);
        }
        $msg = $status == 1 ? "显示" : "隐藏";
        try {
            $sl->status = $status;
            $sl->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['code'=>500, 'msg'=>$msg . '失败']);
        }
        return response()->json(['code'=>200, 'msg'=>$msg . '成功']);
    }

}