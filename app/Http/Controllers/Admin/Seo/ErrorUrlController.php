<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/9/6
 * Time: 下午1:40
 */

namespace App\Http\Controllers\Admin\Seo;

use App\Models\Admin\AdTrieStore;
use App\Models\Admin\CloudKeyword;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ErrorUrlController {
    public function errorUrls(Request $request){
        //已有的
        if (Storage::disk('public')->exists('www/sitemap/404.txt')) {

        }
        else{
            Storage::disk('public')->put('www/sitemap/404.txt', '');
        }
        $data = Storage::disk('public')->get('www/sitemap/404.txt');

        $array = explode("\n", $data);

        //拿最近3天
        $urls = array();
        $file = $request->input('file');
        if (isset($file)){
            $fh = fopen($file, 'r');
            if ($fh) {
                while (! feof($fh)) {
                    $row = fgets($fh);
//                dump($row);
                    $row_arr = explode("] [", $row);
                    if (isset($row_arr[3]) && $row_arr[3] == 404){
//                    echo $row_arr[2].'</br>';
                        $urls[] = $row_arr[2];
                    }
                }
            }
            fclose($fh);
            $urls = array_unique($urls);
            $result['urls'] = $urls;
            $result['array'] = $array;
        }
        else{
            $result['urls'] = $array;
            $result['array'] = $array;
        }
        return view('admin.seo.error_url',$result);
    }

    public function errorUrlAdd(Request $request){
        $url = $request->input('url');
        if (Storage::disk('public')->exists('www/sitemap/404.txt')) {

        }
        else{
            Storage::disk('public')->put('www/sitemap/404.txt', '');
        }
        $data = Storage::disk('public')->get('www/sitemap/404.txt');

        $array = explode("\n", $data);
        if (in_array($url,$array)) {
            foreach ($array as $k => $v) {
                if ($v == $url) {
                    unset($array[$k]);
                }
            }
        }
        else {
            $array[] = $url;
        }
        $data = '';
        foreach ($array as $item){
            if (strlen($data) > 0)
                $data = $data.PHP_EOL.$item;
            else
                $data = $item;
        }
        Storage::disk('public')->put('www/sitemap/404.txt', $data);
        return back()->with('success','success');
    }
}