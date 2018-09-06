<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Article\PcArticle;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SitemapService
{
    const YMD_FORMAT = "Y-m-d";
    const YMDHI_FORMAT = "Y-m-d H:i";

    const SITEMAP_STORAGE_PATH = "app/public/www/sitemap";

    /**
     * 首页（包括首页、主播首页、资讯首页、下载页）
     * @return bool
     */
    public function buildHome()
    {
        $sitemap = App::make("sitemap");

        //首页
        $sitemap->add(config('app.www_url'), date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add(config('app.m_url'), date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add(config('app.mip_url'), date(self::YMDHI_FORMAT, time()), '1.0', 'daily');

        //主播首页
        $anchorHost = "/anchor/";
        $sitemap->add(config('app.www_url').$anchorHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add(config('app.m_url').$anchorHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add(config('app.mip_url').$anchorHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');

        //资讯首页
        $newsHost = "/news/";
        $sitemap->add(config('app.www_url').$newsHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add(config('app.m_url').$newsHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');
        $sitemap->add(config('app.mip_url').$newsHost, date(self::YMDHI_FORMAT, time()), '1.0', 'daily');

        //下载页
        $downloadHost = '/download.html';
        $sitemap->add(config('app.www_url').$downloadHost, date(self::YMDHI_FORMAT, time()), '1.0', 'weekly');
        $sitemap->add(config('app.m_url').$downloadHost, date(self::YMDHI_FORMAT, time()), '1.0', 'weekly');
        $sitemap->add(config('app.mip_url').$downloadHost, date(self::YMDHI_FORMAT, time()), '1.0', 'weekly');

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
            $sitemap->add(config('app.www_url')."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
            $sitemap->add(config('app.m_url')."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
            $sitemap->add(config('app.mip_url')."/".$name.'/', date(self::YMDHI_FORMAT, time()), '0.9', 'daily');
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
                $sitemap->add(config('app.www_url').$_data['url'], date(self::YMDHI_FORMAT, $_data['lastmod']), '0.8', 'weekly');
                $sitemap->add(config('app.m_url').$_data['url'], date(self::YMDHI_FORMAT, $_data['lastmod']), '0.8', 'weekly');
                $sitemap->add(config('app.mip_url').$_data['url'], date(self::YMDHI_FORMAT, $_data['lastmod']), '0.8', 'weekly');
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
            $sitemap->addSitemap(config('app.www_url') . '/sitemap/home.xml', date(self::YMDHI_FORMAT, time()));
        }
        if ($this->buildSubject()) {
            $sitemap->addSitemap(config('app.www_url') . '/sitemap/subject.xml', date(self::YMDHI_FORMAT, time()));
        }
        if ($lastModTimes = $this->buildArticles()) {
            foreach ($lastModTimes as $name => $time) {
                $sitemap->addSitemap(config('app.www_url') . '/sitemap/news-' . $name . '.xml', date(self::YMDHI_FORMAT, $time));
            }
        }

        $sitemap->store('sitemapindex', 'sitemap');
    }
}