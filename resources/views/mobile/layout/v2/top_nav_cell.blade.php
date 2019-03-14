<?php
    $cdn = env('CDN_URL');
    $cur = isset($cur) ? $cur : '';

    if ($cur == "live") {
        $liveClass = 'on';
    } else {
        $liveClass = '';
    }
    if ($cur == "news") {
        $newsClass = 'on';
    } else {
        $newsClass = '';
    }
    if ($cur == "video") {
        $videoClass = 'on';
    } else {
        $videoClass = '';
    }
    if ($cur == "record") {
        $recordClass = 'on';
    } else {
        $recordClass = '';
    }
    if ($cur == "data") {
        $dataClass = 'on';
    } else {
        $dataClass = '';
    }
?>
<div id="Navigation">
    <a href="/" class="home"><img src="{{$cdn}}/img/mobile/v2/logo_white.png"></a>
    <p class="inner_column_con">
        <a href="/" class="{{$liveClass}}">直播</a>
        <a href="/news/" class="{{$newsClass}}">资讯</a>
        <a href="/video/" class="{{$videoClass}}">视频</a>
        <a href="/record/" class="{{$recordClass}}">录像</a>
        <a href="/data/" class="{{$dataClass}}">数据</a>
    </p>
</div>