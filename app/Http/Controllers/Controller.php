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

}
