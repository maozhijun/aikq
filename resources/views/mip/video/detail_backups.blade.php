<?php
$keywords = "爱看球," . $match['lname'] ."录像," . $match['hname'] . "," . $match['aname'];
$description = "《" . $match['hname'] . ' VS ' . $match['aname'] . "》高清全场回放";
?>
@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/videoPhone.css">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>JRS低调看爱看球录像_{{$match['hname']}}VS{{$match['aname']}}</h1>
        <div class="banner"><a class="home" href="{{\App\Http\Controllers\Mip\UrlCommonTool::homeVideosUrl()}}"></a>比赛录像</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Info">
        @if($match['sport'] != 3)
            <div class="team host">
                <div class="img"><mip-img width="38" higth="38" src="{{isset($match['hicon'])?$match['hicon']:env('CDN_URL')."/img/mip/icon_teamDefault.png"}}"></mip-img></div>
                <p>{{$match['hname']}}</p>
            </div>
            <div class="score">
                <p>
                    <span class="host">{{$match['hscore']}}</span>
                    <span class="away">{{$match['ascore']}}</span>
                </p>
            </div>
            <div class="team away">
                <div class="img"><mip-img width="38" higth="38" src="{{isset($match['aicon'])?$match['aicon']:env('CDN_URL')."/img/mip/icon_teamDefault.png"}}"></mip-img></div>
                <p>{{$match['aname']}}</p>
            </div>
        @else
            <p class="other">{{$match['hname']}}</p>
        @endif
    </div>
    <div class="default" id="Video">
        <mip-iframe layout="fixed-height" width="100" height="210" allowfullscreen allowtransparency="true" src="{{env('WWW_URL')}}/live/spPlayer/player-{{$match['mid']}}-{{$match['sport']}}.html" id="MyIframe">

        </mip-iframe>
        <div class="publicAd"><mip-img width="100%" src="{{env('CDN_URL')}}/img/mobile/banner_app_868.jpg"></mip-img></div>
    </div>
    <div id="Content">
        <mip-img width="150" height="150" src="{{env('CDN_URL')}}/img/pc/kanqiu858.jpg"></mip-img>
        <p>扫二维码进入群</p>
    </div>
@endsection