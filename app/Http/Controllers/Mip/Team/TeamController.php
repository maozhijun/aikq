<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/10/25
 * Time: 10:57
 */

namespace App\Http\Controllers\Mip\Team;


use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\Mip\UrlCommonTool;
use App\Http\Controllers\PC\CommonTool;
use App\Models\Subject\SubjectLeague;
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

        $data = AikanQController::teamDetailData($sport, $lid, $tid, true);
        return self::detailHtml($data);
    }

    private static function detailHtml($data)
    {
        if ($data == null) return abort(404);
        return view('mip.team.detail', $data);
    }

    public static function detailStatic($data, $path)
    {
        $html = self::detailHtml($data);

        Storage::disk('public')->put("mip/$path", $html);
//        echo "mip: $path </br>";
    }
}