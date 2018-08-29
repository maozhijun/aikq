<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\Subject;


use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Models\Subject\SubjectLeague;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DetailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subject_detail_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题终端html页面静态化';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leagues = SubjectLeague::getAllLeagues();
        $aiCon = new AikanQController();
        foreach ($leagues as $league) {
            $this->staticSubjectHtml($aiCon, $league);
        }
    }


    public function staticSubjectHtml(AikanQController $aiCon, SubjectLeague $sl) {
        $result = $aiCon->subjectDetailData(false, $sl);
        if (!isset($result) || !isset($result['subject'])) {
            return;
        }
        $html = SubjectController::subjectDetailHtml($result, $sl->id);
        if (!empty($html)) {
            $name_en = $sl->name_en;
            Storage::disk("public")->put("/www/$name_en/index.html", $html);
            Storage::disk("public")->put("/live/subject/" . $sl->id . ".html", $html);//兼容旧地址
        }
    }

}