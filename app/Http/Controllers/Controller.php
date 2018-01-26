<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    protected $html_var = [];

    function __construct()
    {
        $this->html_var['title'] = '';
        $this->html_var['keywords'] = '';
        $this->html_var['description'] = '';
//        $links = Link::query()
//            ->where('show', 1)
//            ->orderBy('sort', 'desc')
//            ->get();
//        $this->html_var['links'] = $links;
    }

    static public function isMobile(Request $request)
    {
        $userAgent = $request->header('user_agent', '');
        if ($userAgent) {
            $userAgent = $request->header('user_agent', '');
            if (preg_match("/(iPad).*OS\s([\d_]+)/", $userAgent)) {
                return true;
            }
            else if (preg_match("/(iPhone\sOS)\s([\d_]+)/", $userAgent)){
                return true;
            }
            else if (preg_match("/(Android)\s+([\d.]+)/", $userAgent)){
                return true;
            }
        }
        return false;
    }

    public static function links() {
        $links = [];
        $key = 'base_link_cache';
        $server_output = Redis::get($key);

        if (empty($server_output)) {
            try {
                $ch = curl_init();
                $url = env('LIAOGOU_URL')."/json/link.json";
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close ($ch);
                if ($http_code >= 400) {
                    $server_output = "";
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
            if (empty($server_output)) {
                return $links;
            }
            Redis::setEx($key, 60 * 10, $server_output);
        }

        if (empty($server_output)) {
            return $links;
        }
        $json = json_decode($server_output);
        if (is_array($json)) {
            foreach ($json as $j) {
                $links[] = ['name'=>$j->name, 'url'=>$j->url];
            }
        }
        return $links;
    }

}
