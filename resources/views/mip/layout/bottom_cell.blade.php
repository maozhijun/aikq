<?php
use App\Http\Controllers\Mip\UrlCommonTool;

$cdn = "{{env('CDN_URL')}}";
    $cur = empty($cur) ? 'live' : $cur;
    if ($cur == "live") {
        $liveIco = $cdn . "/img/mip/commom_icon_live_s.png";
        $liveUrl = 'href=#';
        $liveClass = 'class="on"';
    } else {
        $liveIco = $cdn . "/img/mip/mip/commom_icon_live_n.png";
        $liveUrl = 'href='.UrlCommonTool::homeLivesUrl();
        $liveClass = '';
    }

    if ($cur == "anchor") {
        $anchorIco = $cdn . "/img/mip/commom_icon_anchor_s.png";
        $anchorUrl = 'href=#';
        $anchorClass = 'class="on"';
    } else {
        $anchorIco = $cdn . "/img/mip/commom_icon_anchor_n.png";
        $anchorUrl = 'href='.UrlCommonTool::homeAnchorUrl();
        $anchorClass = '';
    }

    if ($cur == "news") {
        $newsIco = $cdn . "/img/mip/icon_news_s.png";
        $newsUrl = 'href=#';
        $newsClass = 'class="on"';
    } else {
        $newsIco = $cdn . "/img/mip/icon_news_n.png";
        $newsUrl = 'href='.UrlCommonTool::homeNewsUrl();
        $newsClass = '';
    }
?>
<dl id="Bottom">
    <dd {{$liveClass}}>
        <a {{$liveUrl}}>
            <mip-img height="24" width="24" src="{{$liveIco}}"></mip-img>
            <p>直播</p>
        </a>
    </dd>
    <dd {{$anchorClass}}>
        <a {{$anchorUrl}}>
            <mip-img height="24" width="24" src="{{$anchorIco}}"></mip-img>
            <p>主播</p>
        </a>
    </dd>
    <dd {{$newsClass}}>
        <a {{$newsUrl}}>
            <mip-img height="24" width="24" src="{{$newsIco}}"></mip-img>
            <p>资讯</p>
        </a>
    </dd>
    <dd>
        <a href="https://shop.liaogou168.com">
            <mip-img height="24" width="24" src="{{$cdn}}/img/mip/commom_icon_recommend_n.png"></mip-img>
            <p>推荐</p>
        </a>
    </dd>
</dl>