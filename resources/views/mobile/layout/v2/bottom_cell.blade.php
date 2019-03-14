<?php
    $cdn = env('CDN_URL');
    $cur = empty($cur) ? 'live' : $cur;
    if ($cur == "live") {
        $liveIco = $cdn . "/img/mobile/v2/icon_live_s.png.png";
        $liveUrl = '';
        $liveClass = 'on';
    } else {
        $liveIco = $cdn . "/img/mobile/v2/icon_live_n.png";
        $liveUrl = 'href=/';
        $liveClass = '';
    }

    if ($cur == "news") {
        $newsIco = $cdn . "/img/mobile/v2/icon_news_s.png";
        $newsUrl = '';
        $newsClass = 'on';
    } else {
        $newsIco = $cdn . "/img/mobile/v2/icon_news_n.png";
        $newsUrl = 'href=/news/';
        $newsClass = '';
    }

    if ($cur == "video") {
        $videoIco = $cdn . "/img/mobile/v2/icon_ship_s.png";
        $videoUrl = '';
        $videoClass = 'on';
    } else {
        $videoIco = $cdn . "/img/mobile/v2/icon_ship_n.png";
        $videoUrl = 'href=/anchor/';
        $videoClass = '';
    }

    if ($cur == "record") {
        $recordIco = $cdn . "/img/mobile/v2/icon_video_s.png";
        $recordUrl = '';
        $recordClass = 'on';
    } else {
        $recordIco = $cdn . "/img/mobile/v2/icon_video_n.png";
        $recordUrl = 'href=/anchor/';
        $recordClass = '';
    }

    if ($cur == "data") {
        $dataIco = $cdn . "/img/mobile/v2/icon_date_s.png";
        $dataUrl = '';
        $dataClass = 'on';
    } else {
        $dataIco = $cdn . "/img/mobile/v2/icon_date_n.png";
        $dataUrl = 'href=/anchor/';
        $dataClass = '';
    }
?>
<div id="Bottom">
    <a class="{{$liveClass}}" {{$liveUrl}}><img src="{{$liveIco}}">直播</a>
    <a class="{{$newsClass}}" {{$newsUrl}}><img src="{{$newsIco}}">资讯</a>
    <a class="{{$videoClass}}" {{$videoUrl}}><img src="{{$videoIco}}">视频</a>
    <a class="{{$recordClass}}" {{$recordUrl}}><img src="{{$recordIco}}">录像</a>
    <a class="{{$dataClass}}" {{$dataUrl}}><img src="{{$dataIco}}">数据</a>
</div>