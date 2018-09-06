<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 14:17
 */

namespace App\Console\Subject;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\Live\SubjectController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class LeaguesJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subject_leagues_json:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题列表json静态化';

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
        $con = new SubjectController();
        $con->staticSubjectLeagues(new Request());

        $con = new \App\Http\Controllers\Mobile\Subject\SubjectController();
        foreach (Controller::SUBJECT_NAME_IDS as $str=>$data){
            echo $str;
            $con->staticSubjectHtml(new Request(),$str);
        }
    }

}