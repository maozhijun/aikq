<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/11
 * Time: 11:47
 */

namespace App\Console\Sync;


use App\Http\Controllers\Sync\BasketballController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class BasketballUpdateMatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_update_basketball_matches:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新basket_matches的比赛同步到爱看球的basket_matches表';

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
        $con = new BasketballController();
        $con->updateMatch(new Request());
    }

}