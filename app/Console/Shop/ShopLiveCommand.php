<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/26
 * Time: 1:11
 */

namespace App\Console\Shop;


use App\Http\Controllers\IntF\ShopController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopLiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop_living_json:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "正在直播的json列表";

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
        $shopCon = new ShopController();
        $json = $shopCon->shopLives(new Request());
        if (isset($json)) {
            Storage::disk('public')->put('/www/json/shop/lives.json', json_encode($json));
        }
    }

}