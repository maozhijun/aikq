<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/25
 * Time: 10:57
 */

namespace App\Http\Controllers\PC\Team;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Models\Subject\SubjectLeague;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function detail(Request $request, $name_en, $tid) {
        if ($name_en != 'other') {
            $subjectLeague = SubjectLeague::getSubjectLeagueByEn($name_en);
            if (!isset($subjectLeague)) return abort(404);
        }

        $sport = substr($tid, 0, 1);
        $tid = substr($tid, 1);
        $data = AikanQController::teamDetailData($sport, $name_en, $tid);

        if ($data == null) return abort(404);
        return view('pc.team.detail', $data);
    }
}