<?php
use App\Http\Controllers\Mip\UrlCommonTool;

$cdn = UrlCommonTool::MIP_PREFIX;
    $cur = empty($cur) ? 'live' : $cur;
    if ($cur == "live") {
        $liveIco = $cdn . "/img/commom_icon_live_s.png";
        $liveUrl = '';
        $liveClass = 'class="on"';
    } else {
        $liveIco = $cdn . "/img/commom_icon_live_n.png";
        $liveUrl = 'href='.UrlCommonTool::homeLivesUrl();
        $liveClass = '';
    }

    if ($cur == "anchor") {
        $anchorIco = $cdn . "/img/commom_icon_anchor_s.png";
        $anchorUrl = '';
        $anchorClass = 'class="on"';
    } else {
        $anchorIco = $cdn . "/img/commom_icon_anchor_n.png";
        $anchorUrl = 'href='.UrlCommonTool::homeAnchorUrl();
        $anchorClass = '';
    }

    if ($cur == "news") {
        $newsIco = $cdn . "/img/icon_news_s.png";
        $newsUrl = '';
        $newsClass = 'class="on"';
    } else {
        $newsIco = $cdn . "/img/icon_news_n.png";
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
            <mip-img height="24" width="24" src="{{$cdn}}/img/commom_icon_recommend_n.png"></mip-img>
            <p>推荐</p>
        </a>
    </dd>
</dl>