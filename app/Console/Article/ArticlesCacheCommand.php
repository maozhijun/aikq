<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/23
 * Time: 17:48
 */

namespace App\Console\Article;


use App\Models\Article\PcArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ArticlesCacheCommand extends Command
{

    const ARTICLE_CACHE_KEY = "ArticlesCacheCommand_ARTICLE_CACHE_KEY";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文章缓存';

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
        $query = PcArticle::getPublishQuery();
        $articles = $query->take(60)->get();
        $array = [];
        foreach ($articles as $article) {
            $array[] = ['title'=>$article->title, 'url'=>$article->getUrl()];
        }
        Redis::set(self::ARTICLE_CACHE_KEY, json_encode($array));
    }

}