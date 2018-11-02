<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IntF\AikanQController;
use App\Http\Controllers\Mip\UrlCommonTool;
use App\Http\Controllers\PC\CommonTool;
use App\Models\Article\PcArticle;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\Match\MatchLive;
use App\Models\Subject\SubjectLeague;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SitemapService
{
    const YMD_FORMAT = "Y-m-d";
    const YMDHI_FORMAT = "Y-m-d H:i";

    const SITEMAP_STORAGE_PATH = "app/public/www/sitemap";

    const WWW_OFFSET = 0;
    const M_OFFSET = 1;
    const MIP_OFFSET = 2;

    /**
     * 首页（包括首页、主播首页、资讯首页、下载页）
     * @return bool
     */
    public function buildHome()
    {
        $sitemap = App::make("sitemap");

        //首页
        $sitemap->add($this->getHostByOffset(self::WWW_OFFSET), date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add($this->getHostByOffset(self::M_OFFSET), date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add($this->getHostByOffset(self::MIP_OFFSET), date(self::YMDHI_FORMAT, time()), '1.0', 'daily');

        //主播首页
        $anchorHost = "/anchor/";
        $sitemap->add($this->getHostByOffset(self::WWW_OFFSET).$anchorHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add($this->getHostByOffset(self::M_OFFSET).$anchorHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add($this->getHostByOffset(self::MIP_OFFSET).$anchorHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');

        //资讯首页
        $newsHost = "/news/";
        $sitemap->add($this->getHostByOffset(self::WWW_OFFSET).$newsHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add($this->getHostByOffset(self::M_OFFSET).$newsHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add($this->getHostByOffset(self::MIP_OFFSET).$newsHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');

        //下载页
        $downloadHost = '/download/';
        $sitemap->add($this->getHostByOffset(self::WWW_OFFSET).$downloadHost, date(self::YMDHI_FORMAT, time()), '1.0', 'weekly');
        $sitemap->add($this->getHostByOffset(self::M_OFFSET).$downloadHost, date(self::YMDHI_FORMAT, time()), '1.0', 'weekly');
        $sitemap->add($this->getHostByOffset(self::MIP_OFFSET).$downloadHost, date(self::YMDHI_FORMAT, time()), '1.0', 'weekly');

        $info = $sitemap->store('xml', 'home', storage_path(self::SITEMAP_STORAGE_PATH));
        Log::info($info);
        return true;
    }

    /**
     * 专题页
     * @return bool
     */
    public function buildSubject()
    {
        $sitemapIndex = App::make("sitemap");
        $sitemap = App::make("sitemap");

        //把专题详情页的sitemap加入index里
        $sitemapIndex->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . '/sitemap/subject/index.xml', date(self::YMDHI_FORMAT, time()));

        $subLeagues = SubjectLeague::getAllLeagues();
        foreach ($subLeagues as $subLeague) {
            $name = $subLeague['name_en'];

            $sitemap->add($this->getHostByOffset(self::WWW_OFFSET)."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
            $sitemap->add($this->getHostByOffset(self::M_OFFSET)."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
            $sitemap->add($this->getHostByOffset(self::MIP_OFFSET)."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');

            //添加专题所有球队的终端页
            $sitemapIndex = $this->buildSubjectTeams($sitemapIndex, $name, $subLeague['sport'], $subLeague['lid']);
        }
        $this->fileOnCreate(storage_path(self::SITEMAP_STORAGE_PATH."/subject/"));
        $sitemap->store('xml', 'index', storage_path(self::SITEMAP_STORAGE_PATH."/subject"));

        //添加专题最近比赛的终端页
        $sitemapIndex = $this->buildSubjectLiveDetails($sitemapIndex);

        $info = $sitemapIndex->store('sitemapindex', 'subject', storage_path(self::SITEMAP_STORAGE_PATH));
        Log::info($info);
        return true;
    }

    /**
     * 首页比赛直播
     */
    public function buildSubjectLiveDetails($sitemapIndex) {
        $lives = json_decode(Storage::get("/public/static/json/pc/lives.json"), true);
        $array = [];
        foreach ($lives['matches'] as $data => $matches) {
            foreach ($matches as $key => $match) {
                list($sport, $mid) = explode("_", $key, 2);

                $liveDetailUrl = CommonTool::getLiveDetailUrl($sport, $match['lid'], $mid);
                list($empty, $name_en, $other) = explode("/", $liveDetailUrl, 3);

                if (!isset($array[$name_en])) {
                    $array[$name_en] = App::make("sitemap");
                }
                $array[$name_en]->add($this->getHostByOffset(self::WWW_OFFSET).$liveDetailUrl, date(self::YMDHI_FORMAT, time()), '0.8', 'daily');
                $array[$name_en]->add($this->getHostByOffset(self::M_OFFSET).$liveDetailUrl, date(self::YMDHI_FORMAT, time()), '0.8', 'daily');
                $array[$name_en]->add($this->getHostByOffset(self::MIP_OFFSET).$liveDetailUrl, date(self::YMDHI_FORMAT, time()), '0.8', 'daily');
            }
        }

        foreach ($array as $name_en=>$sitemap) {
            $this->fileOnCreate(storage_path(self::SITEMAP_STORAGE_PATH."/subject/$name_en/"));
            $sitemap->store('xml', "live", storage_path(self::SITEMAP_STORAGE_PATH."/subject/$name_en"));

            $sitemapIndex->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . "/sitemap/$name_en/live.xml", date(self::YMDHI_FORMAT, time()));
        }
        return $sitemapIndex;
    }

    /**
     * 专题球队终端
     */
    public function buildSubjectTeams($sitemapIndex, $name_en, $sport, $lid) {
        if ($sport == MatchLive::kSportBasketball) {
            $season = BasketSeason::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $query = BasketScore::query();
        } else {
            $season = Season::query()->where("lid", $lid)->orderBy("year", "desc")->first();
            $query = Score::query();
        }
        if (isset($season)) {
            AikanQController::leagueRankStatic($sport, $lid);
            $scores = $query->select('tid')->where('lid', $lid)->where('season', $season->name)->get()->unique('tid');

            $sitemap = App::make("sitemap");
            foreach ($scores as $score) {
                $teamUrl = CommonTool::getTeamDetailUrl($sport, $lid, $score['tid']);

                $sitemap->add($this->getHostByOffset(self::WWW_OFFSET).$teamUrl, date(self::YMDHI_FORMAT, time()), '0.8', 'daily');
                $sitemap->add($this->getHostByOffset(self::M_OFFSET).$teamUrl, date(self::YMDHI_FORMAT, time()), '0.8', 'daily');
                $sitemap->add($this->getHostByOffset(self::MIP_OFFSET).$teamUrl, date(self::YMDHI_FORMAT, time()), '0.8', 'daily');
            }
            $this->fileOnCreate(storage_path(self::SITEMAP_STORAGE_PATH."/subject/$name_en/"));
            $info = $sitemap->store('xml', "team", storage_path(self::SITEMAP_STORAGE_PATH."/subject/$name_en"));
            Log::info($info);

            $sitemapIndex->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . "/sitemap/$name_en/team.xml", date(self::YMDHI_FORMAT, time()));
        }
        return $sitemapIndex;
    }


    /**
     * 资讯终端
     */
    public function buildArticles()
    {
        $sitemap = App::make("sitemap");
        $sitemapIndex = App::make("sitemap");

        $sitemapName = '';
        $articlesData = [];

        PcArticle::query()->select(['id', 'url', 'created_at', 'updated_at'])
            ->where('status', 1)->orderBy('created_at', 'desc')
            ->chunk(100, function ($articles) use (&$articlesData, &$sitemapName) {
                foreach ($articles as $article) {
                    $sitemapName = date('Y-m', strtotime($article->created_at));
                    $articlesData[$sitemapName][] = [
                        'url' => $article->getUrl(),
                        'lastmod' => strtotime($article->updated_at)
                    ];
                }
            });

        $lastModTimes = [];
        $index = 0;
        foreach ($articlesData as $name => $data) {
            $lastModTime = 0;
            foreach ($data as $_data) {
                if ($_data['lastmod'] > $lastModTime) {
                    $lastModTime = $_data['lastmod'];
                }
                $index++;
                $sitemap->add($this->getHostByOffset(self::WWW_OFFSET).$_data['url'], date(self::YMDHI_FORMAT, $_data['lastmod']), '0.8', 'weekly');
                $sitemap->add($this->getHostByOffset(self::M_OFFSET).$_data['url'], date(self::YMDHI_FORMAT, $_data['lastmod']), '0.8', 'weekly');
                $sitemap->add($this->getHostByOffset(self::MIP_OFFSET).$_data['url'], date(self::YMDHI_FORMAT, $_data['lastmod']), '0.8', 'weekly');
            }
            $this->fileOnCreate(storage_path(self::SITEMAP_STORAGE_PATH."/news/"));
            $info = $sitemap->store('xml', $name, storage_path(self::SITEMAP_STORAGE_PATH."/news"));
            $lastModTimes[$name] = $lastModTime;
            Log::info($info);
            $sitemap->model->resetItems();

            $sitemapIndex->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . '/sitemap/news/' . $name . '.xml', date(self::YMDHI_FORMAT, $lastModTime));
        }

        $sitemapIndex->store('sitemapindex', 'news', self::SITEMAP_STORAGE_PATH);
        return true;
    }


    /**
     * ================================================================
     * ================================================================
     */

    public function buildIndex()
    {
        $sitemap = App::make ("sitemap");

        if ($this->buildHome()) {
            $sitemap->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . '/sitemap/home.xml', date(self::YMDHI_FORMAT, time()));
        }
        if ($this->buildSubject()) {
            $sitemap->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . '/sitemap/subject.xml', date(self::YMDHI_FORMAT, time()));
        }
        if ($lastModTimes = $this->buildArticles()) {
            $sitemap->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . '/sitemap/news.xml', date(self::YMDHI_FORMAT, time()));
        }

        $sitemap->store('sitemapindex', 'sitemap');
    }

    protected function getHostByOffset($offset) {
        $host = "";
        switch ($offset) {
            case self::MIP_OFFSET:
                $host = UrlCommonTool::convertHost(env('MIP_URL'));
                break;
            case self::M_OFFSET:
                $host = UrlCommonTool::convertHost(env('M_URL'));
                break;
            case self::WWW_OFFSET:
                $host = UrlCommonTool::convertHost(env('WWW_URL'));
                break;
        }
        return $host;
    }

    protected function fileOnCreate($dir) {
        if (!file_exists($dir)){
            mkdir ($dir,0777,true);
        }
    }
}