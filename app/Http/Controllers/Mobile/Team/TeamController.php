<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/25
 * Time: 10:57
 */

namespace App\Http\Controllers\Mobile\Team;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\Mip\UrlCommonTool;
use App\Http\Controllers\PC\CommonTool;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function detail(Request $request, $name_en, $tid)
    {
        $lid = "";
        if ($name_en != 'other') {
            $subjectLeague = SubjectLeague::getSubjectLeagueByEn($name_en);
            if (isset($subjectLeague)) {
                $lid = $subjectLeague->lid;
            }
        }

        $sport = substr($tid, 0, 1);
        $tid = substr($tid, 1);

        $data = AikanQController::teamDetailData($sport, $lid, $tid, true, 20);
        $data['videos'] = TagRelation::getRelationsPageByTagId(TagRelation::kTypeVideo,$sport,3,$tid,1,20)->items();
        return self::detailHtml($data);
    }
    private static function detailHtml($data)
    {
        if ($data == null) return abort(404);
        return view('mobile.team.v2.detail', $data);
    }

    public function staticIndexHtml(Request $request, $sport, $name_en, $tid, $page){
        TeamController::detailStatic($name_en,$tid,$sport);
    }

    public static function detailStatic($name_en, $tid, $sport)
    {
//        $html = self::detailHtml($data);
//
//        Storage::disk('public')->put("www/$path", $html);
//        echo "pc: $path </br>";
        $path = CommonTool::getTeamDetailPathWithType($sport, $name_en, $tid,'index',1);

        $con = new TeamController();
        $tempTid = $tid;
        while (strlen($tempTid) < 4) {
            $tempTid = "0".$tempTid;
        }
        $tempTid = $sport.$tempTid;
        $html = $con->detail(new Request(), $name_en, $tempTid);
        if (isset($html) && strlen($html) > 0){
            Storage::disk('public')->put("m/$path", $html);
        }
    }
}