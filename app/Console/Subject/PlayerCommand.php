<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/10
 * Time: 10:28
 */

namespace App\Console\Subject;


use App\Http\Controllers\PC\Live\SubjectController;
use Illuminate\Console\Command;

class PlayerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subject_player_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '专题集锦/录像播放页面静态化';

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
        $url = asset('/static/subject/player');
        SubjectController::execUrl($url);
    }

}