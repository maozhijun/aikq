<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mip\UrlCommonTool;
use App\Models\Article\PcArticle;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

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
        $downloadHost = '/download.html';
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
        $sitemap = App::make("sitemap");
        foreach (Controller::SUBJECT_NAME_IDS as $name=>$item) {
            $sitemap->add($this->getHostByOffset(self::WWW_OFFSET)."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
            $sitemap->add($this->getHostByOffset(self::M_OFFSET)."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
            $sitemap->add($this->getHostByOffset(self::MIP_OFFSET)."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
        }
        $info = $sitemap->store('xml', 'subject', storage_path(self::SITEMAP_STORAGE_PATH));
        Log::info($info);
        return true;
    }

    /**
     * 资讯终端
     * @return array
     */
    public function buildArticles()
    {
        $sitemap = App::make("sitemap");

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
            $info = $sitemap->store('xml','news-' . $name, storage_path(self::SITEMAP_STORAGE_PATH));
            $lastModTimes[$name] = $lastModTime;
            Log::info($info);
            $sitemap->model->resetItems();
        }
        return $lastModTimes;
    }

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
            foreach ($lastModTimes as $name => $time) {
                $sitemap->addSitemap($this->getHostByOffset(self::WWW_OFFSET) . '/sitemap/news-' . $name . '.xml', date(self::YMDHI_FORMAT, $time));
            }
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
}