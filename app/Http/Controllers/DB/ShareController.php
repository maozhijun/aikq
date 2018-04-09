<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/2
 * Time: 17:00
 */

namespace App\Http\Controllers\DB;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function index(Request $request) {
        $all = $request->all();
        $param = '?intf=aik';
        if (isset($all)) {
            foreach ($all as $key=>$value) {
                $param .= '&' . $key . '=' . $value;
            }
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/spread/api/matchList.html" . $param;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code >= 400 && $code <= 599) {
            return "您访问的链接出错或者不存在。";
        }

        return $server_output;
    }

    public function url(Request $request) {
        $all = $request->all();
        $param = '?intf=aik';
        if (isset($all)) {
            foreach ($all as $key=>$value) {
                $param .= '&' . $key . '=' . $value;
            }
        }
        $ch = curl_init();
        $url = env('LIAOGOU_URL')."/spread/api/matchListUrl.html" . $param;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $server_output = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code >= 400 && $code <= 599) {
            return "您访问的链接出错或者不存在。";
        }

        return $server_output;
    }
}