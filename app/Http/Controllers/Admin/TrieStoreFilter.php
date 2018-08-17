<?php

namespace App\Http\Controllers\Admin;

/**
 * Created by PhpStorm.
 * User: maozhijun
 * Date: 17/4/21
 * Time: 18:11
 */
trait TrieStoreFilter
{
    private function findFilterKeys($content)
    {
        $result = [];
        $blackwordPath = storage_path('blackword.tree');
        if (file_exists($blackwordPath)) {
            $resTrie = \trie_filter_load($blackwordPath);
            // 执行过滤
            $arrRet = \trie_filter_search_all($resTrie, $content);
            foreach ($arrRet as $k => $v) {
                $word = substr($content, $v[0], $v[1]);
                if (!in_array($word, $result)) {
                    $result[] = $word;
                }
            }
        }
        return $result;
    }
}