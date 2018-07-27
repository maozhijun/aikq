<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/27
 * Time: 10:50
 */

namespace App\Console\Anchor;


use App\Http\Controllers\PC\Anchor\AnchorController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnchorIndexCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anchor_index_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '主播列表静态化';

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
    public function handle() {
        $con = new AnchorController();
        $html = $con->index(new Request());
        if (!empty($html)) {
            Storage::disk('public')->put('static/anchor/index.html', $html);
        }
    }

}