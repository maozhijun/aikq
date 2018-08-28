<?php
    $keywords = "爱看球," . $match['lname'] ."录像," . $match['hname'] . "," . $match['aname'];
    $description = "《" . $match['hname'] . ' VS ' . $match['aname'] . "》高清全场回放";
?>
@extends('mobile.layout.base')
@section('title')
    <title>爱看球|{{$match['lname']}}录像|【{{date('Y-m-d H:i', $match['time']) . ' ' . $match['hname'] . ' VS ' . $match['aname']}}】</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoPhone2.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner"><a class="home" href="/m/live/subject/videos/all/1.html"></a>爱看球</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Info" style="height: 140px;">
        <p class="other">{{$match['hname'] . ' ' . $match['hscore'] . ' - ' . $match['ascore'] . ' ' . $match['aname']}}</p>
    </div>
    <div class="default" id="Video" style="height: 436px;">
        <?php $channels = $match['channels'];?>
        <div class="line">
            @foreach($channels as $index=>$channel)
                <?php
                if ($channel['player'] == 11 || $channel['player'] == 19 || stristr($channel['link'], 'player.pptv.com')){
                    $preUrl = str_replace("https://", "http://",env('APP_URL'));
                } else{
                    $preUrl = str_replace("http://", "https://",env('APP_URL'));
                }
                $url = $preUrl . '/live/subject/player.html?cid=' . $channel['id'] . '&type=video';
                ?>
                <button id="{{$channel['id']}}" value="{{$preUrl.'/live/subject/player.html?cid=' . $channel['id'] . '&type=video'}}">{{$channel['title']}}</button>
            @endforeach
        </div>
        <iframe id="Frame" src=""></iframe>
        <div class="publicAd"><img src="{{env('CDN_URL')}}/img/pc/banner_app_868.jpg"></div>
    </div>
    <div id="Content">
        <img src="{{env('CDN_URL')}}/img/pc/image_qr_868.jpg">
        <p>扫二维码进入群</p>
    </div>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/videoPhone.js?time=201803030002"></script>
    <script>
        window.onload = function () {
            setPage();
        }
    </script>
    <script type="text/javascript">
        var ua = navigator.userAgent;
        if (ua.indexOf('Liaogou168') > 0 || ua.indexOf('AKQ') > 0){
            $('#Navigation').remove();
            $('body').css('padding','0')
        }
    </script>
@endsection