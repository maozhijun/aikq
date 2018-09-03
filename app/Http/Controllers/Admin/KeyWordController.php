<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/9/3
 * Time: 上午11:34
 */

namespace App\Http\Controllers\Admin;

use App\Models\Admin\AdTrieStore;
use App\Models\Admin\CloudKeyword;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KeyWordController extends Controller
{

    public function lists(Request $request)
    {
        $query = CloudKeyword::query();
        if ($request->has('keyword')) {
            $query->where('keyword', 'like', "%$request->key%");
        }
        $query->where('status','<>',-1);
        $query->orderby('created_at','desc');
        $filters = $query->paginate();
        if ($request->has('keyword')) {
            $filters->appends('keyword', $request->key);
        }
        if ($request->has('url')) {
            $filters->appends('url', $request->url);
        }
        return view('admin.cloudKey.list', ['filters' => $filters]);
    }

    public function add(Request $request)
    {
        if ($request->has('key') && $request->has('url')) {
            $ts = CloudKeyword::query()->find($request->key);
            if (!isset($ts)) {
                $ts = new CloudKeyword();
                $ts->keyword = $request->key;
                $ts->url = $request->url;
                if ($ts->save()) {
                    return back()->with('success', '保存成功');;
                }
            }
        }
        return back()->with('error', '保存失败');
    }

    public function update(Request $request)
    {
        if ($request->has('id')) {
            $ts = CloudKeyword::query()->find($request->id);
            if (isset($ts)) {
                $ts->status = $request->input('status');
                if ($ts->save()) {
                    return back()->with('success', '成功');;
                }
            }
        }
        return back()->with('error', '失败');
    }
}