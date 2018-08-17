<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/17
 * Time: 15:14
 */

namespace App\Http\Controllers\Admin\Match;


use App\Models\Match\MatchLiveChannelLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ChannelLogController extends Controller
{

    public function logs(Request $request) {
        $lname = $request->input('lname');
        $hname = $request->input('hname');
        $aname = $request->input('aname');
        $start = $request->input('start');
        $end = $request->input('end');

        $query = MatchLiveChannelLog::query();
        $page = $query->paginate(20);
        $page->appends($request->all());
        $result['page'] = $page;
        return view('admin.log.channel.list', $result);
    }

}