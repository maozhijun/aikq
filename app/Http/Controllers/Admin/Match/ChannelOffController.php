<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/20
 * Time: 10:57
 */

namespace App\Http\Controllers\Admin\Match;


use App\Models\Match\LiveChannelLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChannelOffController extends Controller
{

    /**
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logs(Request $request) {
        $mname = $request->input('mname');
        $start = $request->input('start');
        $end = $request->input('end');

        $query = LiveChannelLog::query();
        if (!empty($mname)) {
            $query->where(function ($orQuery) use ($mname) {
                $orQuery->where('hname', 'like', '%' .$mname. '%');
                $orQuery->orWhere('aname', 'like', '%' .$mname. '%');
            });
        }
        if (!empty($start)) {
            $start = date('Y-m-d H:i', strtotime($start)) . ":00";
            $query->where('created_at', '>=', $start);
        }
        if (!empty($end)) {
            $end = date('Y-m-d H:i', strtotime($end)).":59";
            $query->where('created_at', '<=', $end);
        }
        $query->orderByDesc('created_at');
        $page = $query->paginate(20);
        $page->appends($request->all());
        $result['page'] = $page;
        return view('admin.log.off.list', $result);
    }

}