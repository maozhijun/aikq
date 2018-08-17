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
        $mname = $request->input('mname');
        $admin = $request->input('admin');
        $status = $request->input('status');
        $start = $request->input('start');
        $end = $request->input('end');

        $query = MatchLiveChannelLog::query();
        $query->join('accounts', 'accounts.id', '=', 'match_live_channel_logs.new_admin_id');
        if (!empty($mname)) {
            $query->where(function ($orQuery) use ($mname) {
                $orQuery->where('hname', 'like', '%'.$mname.'%');
                $orQuery->orWhere('aname', 'like', '%'.$mname.'%');
            });
        }
        if (!empty($admin)) {
            $query->where('accounts.name', 'like', '%'.$admin.'%');
        }
        if (is_numeric($status)) {
            $query->where('match_live_channel_logs.status', '=', $status);
        }
        if (!empty($start)) {
            $start = date('Y-m-d H:i', strtotime($start)) . ":00";
            $query->where('match_live_channel_logs.created_at', '>=', $start);
        }
        if (!empty($end)) {
            $end = date('Y-m-d H:i', strtotime($end)).":59";
            $query->where('match_live_channel_logs.created_at', '<=', $end);
        }
        $query->select('match_live_channel_logs.*');
        $query->addSelect(DB::raw('accounts.name as admin_name'));
        $query->orderByDesc('created_at');
        $page = $query->paginate(20);
        $page->appends($request->all());
        $result['page'] = $page;
        return view('admin.log.channel.list', $result);
    }

}