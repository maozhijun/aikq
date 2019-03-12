<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\Subject;


use App\Console\HtmlStaticCommand\BaseCommand;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Models\Subject\SubjectLeague;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DetailCommand extends BaseCommand
{

    protected  function command_name()
    {
        return "subject_detail_cache";
    }

    protected function description()
    {
        return "专题终端html页面静态化";
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $request = new Request();
        $subCon = new SubjectController();
        $subCon->staticSubjectLeagues($request);

        $type = $this->argument('type');
        switch ($type) {
            case "pc":
                $this->onPcHandler($request);
                break;
            case "mobile":
                $this->onMobileHandler($request);
                break;
            case "mip":
                $this->onMipHandler($request);
                break;
            case "all":
                $this->onPcHandler($request);
                $this->onMobileHandler($request);
                $this->onMipHandler($request);
                break;
        }
        $this->onCommonHandler($request);
    }

    protected function onPcHandler(Request $request)
    {
        $leagues = SubjectLeague::getAllLeagues();
        $subjectCon = new SubjectController();

        foreach ($leagues as $league) {
            $name_en = $league["name_en"];

            $html = $subjectCon->detailV2($request, $name_en);//SubjectController::subjectDetailHtml($result, $sl);
            if (!empty($html)) {
                Storage::disk("public")->put("/www/$name_en/index.html", $html);
            }
        }

    }

    protected function onMobileHandler(Request $request)
    {
        $leagues = SubjectLeague::getAllLeagues();
        $aiCon = new AikanQController();
        foreach ($leagues as $league) {
            $result = $aiCon->subjectDetailData(false, $league);
            if (!isset($result) || !isset($result['subject'])) {
                return;
            }
            $name_en = $league["name_en"];

            //手机
            $con = new \App\Http\Controllers\Mobile\Subject\SubjectController();
            $con->staticSubjectHtml($request, $name_en);
        }
    }

    protected function staticSubjectHtml(AikanQController $aiCon, SubjectLeague $sl) {
        $result = $aiCon->subjectDetailData(false, $sl);
        if (!isset($result) || !isset($result['subject'])) {
            return;
        }
        $subjectCon = new SubjectController();
        $request = new Request();
        $name_en = $sl["name_en"];

        $html = $subjectCon->detailV2($request, $name_en);//SubjectController::subjectDetailHtml($result, $sl);
        if (!empty($html)) {
            Storage::disk("public")->put("/www/$name_en/index.html", $html);
        }

        //手机
        $con = new \App\Http\Controllers\Mobile\Subject\SubjectController();
        echo $sl->name_en;
        $con->staticSubjectHtml(new Request(),$sl->name_en);
    }

}