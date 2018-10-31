<?php
$keywords = "爱看球," . $match['lname'] ."直播," . $match['hname'] . "," . $match['aname'];
$description = "《" . $match['hname'] . ' VS ' . $match['aname'] . "》高清全场回放";
?>
@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/videoPhone.css">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>JRS低调看爱看球直播_{{$match['hname']}}VS{{$match['aname']}}</h1>
        <div class="banner"><a class="home" href="{{\App\Http\Controllers\Mip\UrlCommonTool::homeLivesUrl()}}"></a>比赛直播</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Info">
        @if($match['sport'] != 3)
            <div class="team host">
                <div class="img"><mip-img width="38" higth="38" src="{{isset($host_icon)?$host_icon:env('CDN_URL')."/img/mip/icon_teamDefault.png"}}"></mip-img></div>
                <p>{{$match['hname']}}</p>
            </div>
            <div class="score">
                <!--<p>
                    <span class="host">{{$match['hscore']}}</span>
                    <span class="away">{{$match['ascore']}}</span>
                </p>-->
                <b>VS</b>
            </div>
            <div class="team away">
                <div class="img"><mip-img width="38" higth="38" src="{{isset($away_icon)?$away_icon:env('CDN_URL')."/img/mip/icon_teamDefault.png"}}"></mip-img></div>
                <p>{{$match['aname']}}</p>
            </div>
        @else
            <p class="other">{{$match['hname']}}</p>
        @endif
    </div>
    <div class="default" id="Video">
        @if($match['status'] >= 0 || (isset($match['isMatching']) && $match['isMatching']))
            <mip-iframe layout="fixed-height" width="100" height="210" allowfullscreen allowtransparency="true" src="{{env('WWW_URL')}}/live/spPlayer/player-{{$match['mid']}}-{{$match['sport']}}.html" id="MyIframe">

            </mip-iframe>
            <div class="publicAd"><mip-img width="100%" src="{{env('CDN_URL')}}/img/pc/banner_app_868.jpg"></mip-img></div>
        @else
            <div class="publicAd"><a href="/download/"><img src="{{env('CDN_URL')}}/img/mobile/image_ad_wap.jpg"></a></div>
            <div class="outPlay">
                {{--<img src="{{\App\Http\Controllers\Mip\UrlCommonTool::MIP_PREFIX}}/img/customer2/icon_video_live.png">--}}
                {{--<p class="reciprocal">距离比赛还有<span>05：30</span></p>--}}
                <img src="{{env('CDN_URL')}}/img/customer2/icon_video_over.png">
                <p class="end">比赛已结束</p>
            </div>
        @endif
    </div>
@endsection