<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/3/11 0011
 * Time: 18:03
 */

namespace App\Http\Controllers\Admin;

use App\Models\Admin\AdTrieStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TrieStoreController extends Controller
{

    public function filters(Request $request)
    {
        $query = AdTrieStore::query();
        if ($request->has('key')) {
            $query->where('key', 'like', "%$request->key%");
        }
        $filters = $query->paginate();
        if ($request->has('key')) {
            $filters->appends('key', $request->key);
        }
        return view('admin.filter.index', ['filters' => $filters]);
    }

    public function add(Request $request)
    {
        if ($request->has('key')) {
            $ts = AdTrieStore::query()->find($request->key);
            if (!isset($ts)) {
                $ts = new AdTrieStore();
                $ts->key = $request->key;
                $ts->level = AdTrieStore::kTrieLevelFilter;
                if ($ts->save()) {
                    $this->restore();
                    return $this->filters($request);
                }
            }
        }
        return back();
    }

    protected function restore()
    {
        $tss = AdTrieStore::query()->get();
        $resTrie = \trie_filter_new();
        foreach ($tss as $ts) {
            \trie_filter_store($resTrie, $ts->key);
        }
        // 生成trie-tree文件
        $blackwordPath = storage_path('blackword.tree');
        \trie_filter_save($resTrie, $blackwordPath);
    }

    public function del(Request $request)
    {
        if ($request->has('key')) {
            $ts = AdTrieStore::query()->find($request->key);
            if (isset($ts)) {
                if ($ts->delete()) {
                    $this->restore();
                }
            }
        }
        return back();
    }

    public function batchFill(Request $request)
    {

        $dictPath = storage_path('dict.txt');
        // 读取敏感词字典库
        $handle = fopen($dictPath, 'r');
        while (!feof($handle)) {
            $item = trim(fgets($handle));
            if (empty($item) || strlen($item) > 20) {
                continue;
            }
            $ts = AdTrieStore::query()->find($item);
            if (!isset($ts)) {
                $ts = new AdTrieStore();
                $ts->key = $item;
                $ts->level = AdTrieStore::kTrieLevelFilter;
                $ts->save();
            }
        }
    }


}