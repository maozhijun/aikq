<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/6
 * Time: 16:52
 */

namespace App\Console\Article;


use App\Http\Controllers\PC\Article\ArticleController;
use App\Models\Article\PcArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ArticlePageCommands extends Command
{

    const ARTICLE_PAGE_KEY = "ARTICLE_PAGE_KEY";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article_page:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文章分页列表静态化';

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
        //静态化 资讯分页列表
        //获取分页信息
        $page = 1;
        $indexPage = 4;

        //静态化资讯第一页
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(ArticleController::PageSize, ['*'], '', $page);
        $con = new ArticleController();
        $html = $con->newsHtml($articles);
        if (!empty($html)) {
            Storage::disk("public")->put("news/index.html", $html);
        }
        $wapCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
        $wapIndex = $wapCon->articlesHtml($articles);
        if (!empty($wapIndex)) {
            Storage::disk("public")->put("static/m/news/index.html", $wapIndex);
        }

        $lastPage = $articles->lastPage();
        if ($lastPage > 1) {
            //静态化其他资讯页
            $page = $this->getCachePage(self::ARTICLE_PAGE_KEY);
            $page = $page >= $lastPage ? 2 : $lastPage;
            $lastPage = $lastPage > $indexPage ? $indexPage : $lastPage;
            for (; $page <= $lastPage; $page++) {
                $this->staticNewsHtml($page);
            }
            $this->setCachePage(self::ARTICLE_PAGE_KEY, $page);
        }
    }

    /**
     * 静态化
     * @param $page
     */
    public function staticNewsHtml($page) {
        $query = PcArticle::getPublishQuery();
        $articles = $query->paginate(ArticleController::PageSize, ['*'], '', $page);
        $con = new ArticleController();
        $html = $con->newsHtml($articles);
        if (!empty($html)) {
            Storage::disk("public")->put("news/index" . $page . ".html", $html);
        }
        $wapCon = new \App\Http\Controllers\Mobile\Article\ArticleController();
        $wapPageHtml = $wapCon->articlesCell($articles);
        if (!empty($wapPageHtml)) {
            Storage::disk("public")->put("static/m/news/page" . $page . ".html", $wapPageHtml);
        }
    }

    /**
     * 获取有效的主播房间
     * @param $key
     * @return array|mixed
     */
    public function getCachePage($key) {
        $page = Redis::get($key);
        if (empty($page)) {
            $page = 2;
        }
        return $page;
    }

    public function setCachePage($key, $page) {
        Redis::set($key, $page);
    }

}