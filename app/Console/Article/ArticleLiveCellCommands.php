<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/6
 * Time: 18:34
 */

namespace App\Console\Article;


use App\Http\Controllers\PC\Article\ArticleController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleLiveCellCommands extends Command
{


    const ARTICLE_PAGE_KEY = "ARTICLE_PAGE_KEY";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article_lives:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文章直播单元静态化';

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
        $con = new ArticleController();
        $html = $con->detailLives(new Request());
        if (!empty($html)) {
            Storage::disk("public")->put("news/lives.html", $html);
            Storage::disk("public")->put("www/news/lives.html", $html);
        }
    }

}