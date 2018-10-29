<?php

namespace App\Console\HtmlStaticCommand\Subject;

use App\Console\HtmlStaticCommand\BaseCommand;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Models\Subject\SubjectLeague;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DetailCommand extends BaseCommand
{

    protected function command_name()
    {
        return "subject_detail_cache";
    }

    protected function description()
    {
        return "专题终端html页面静态化";
    }

    protected function onCommonHandler(Request $request)
    {
        $subCon = new SubjectController();
        $subCon->staticSubjectLeagues(new Request());
        $leagues = SubjectLeague::getAllLeagues();
        $aiCon = new AikanQController();
        foreach ($leagues as $league) {
            $this->staticSubjectHtml($request, $aiCon, $league);
        }
    }

    public function staticSubjectHtml(Request $request, AikanQController $aiCon, SubjectLeague $sl) {
        $result = $aiCon->subjectDetailData(false, $sl);
        if (!isset($result) || !isset($result['subject'])) {
            return;
        }
        $html = SubjectController::subjectDetailHtml($result, $sl->id);
        if (!empty($html)) {
            $name_en = $sl->name_en;
            Storage::disk("public")->put("/www/$name_en/index.html", $html);
        }

        //手机
        $con = new \App\Http\Controllers\Mobile\Subject\SubjectController();
        echo $sl->name_en;
        $con->staticSubjectHtml($request,$sl->name_en);

        //mip
        $mipCon = new \App\Http\Controllers\Mip\Subject\SubjectController();
        echo $sl->name_en;
        $mipCon->staticSubjectHtml($request,$sl->name_en);
    }
}