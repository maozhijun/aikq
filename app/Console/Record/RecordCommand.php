<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\Record;


use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\PC\Data\DataController;
use App\Http\Controllers\PC\Live\SubjectController;
use App\Http\Controllers\PC\Record\RecordController;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordCommand extends Command
{
    protected $type = "";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '录像页面静态化';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        $this->signature = 'record_cache:run {type}';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        $this->type = $type;
        $aiCon = new RecordController();
        if ($this->type == 'index' || $this->type == 'all'){
            $aiCon->staticIndex(new Request());
        }
        if ($this->type == 'league' || $this->type == 'all'){
            $leagues = SubjectLeague::getAllLeagues();
            foreach ($leagues as $league) {
                if ($league->name_en == 'worldcup')
                    continue;
                $this->staticSubjectHtml($aiCon, $league);
            }
        }
        if ($this->type == 'detail'){
            $datas = SubjectVideo::whereNull('url')
                ->where('s_lid','<>',1008)
                ->orderby('time','desc')
                ->take(5)->get();
            foreach ($datas as $data){
                $ch = curl_init();
                $url = env('CMS_URL').'/static/record/'.$data->id;
                echo "$url <br>";
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);//8秒超时
                curl_exec ($ch);
                curl_close ($ch);
            }
        }
    }

    public function staticSubjectHtml(RecordController $aiCon, SubjectLeague $sl) {
        for ($i = 1 ; $i < 4; $i++){
            $html = $aiCon->subjectDetailHtml(new Request(), $sl,$i);
            if (!empty($html)) {
                $name_en = $sl->name_en;
                if ($i == 1){
                    Storage::disk("public")->put("/www/$name_en/record/index.html", $html);
                }
                else{
                    Storage::disk("public")->put("/www/$name_en/record/index$i.html", $html);
                }
            }
        }

        //手机
//        $con = new \App\Http\Controllers\Mobile\Subject\SubjectController();
//        echo $sl->name_en;
//        $con->staticSubjectHtml(new Request(),$sl->name_en);
    }

}