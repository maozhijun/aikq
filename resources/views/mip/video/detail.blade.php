<?php
    $keywords = "爱看球," . $match['lname'] ."录像," . $match['hname'] . "," . $match['aname'];
    $description = "《" . $match['hname'] . ' VS ' . $match['aname'] . "》高清全场回放";
?>
@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/record.css">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>JRS低调看爱看球录像_{{$match['hname']}}VS{{$match['aname']}}</h1>
        <div class="banner"><a class="home" href="/"></a>比赛录像</div>{{--\App\Http\Controllers\Mip\UrlCommonTool::homeVideosUrl()--}}
    </div>
@endsection
@section('content')
    <div class="default" id="Video">
        <mip-vd-tabs class="tab">
            <section>
                <li>{{$svc['title']}}</li>
                @foreach($allChannels as $ch) @continue($ch->id == $svc->id)
                <li>{{$ch['title']}}</li>
                @endforeach
            </section>
            <a href="{{$svc['content']}}" target="_blank"></a>
            @foreach($allChannels as $ch) @continue($ch->id == $svc->id)
            <a href="{{$ch['content']}}" target="_blank"></a>
            @endforeach
        </mip-vd-tabs>
    </div>
    @if(isset($moreVideos) && count($moreVideos) > 0)
    <div id="Content">
        <div id="Record">
            <p class="title">更多精彩视频</p>
            @foreach($moreVideos as $video)
                <?php $vTitle = $video->getVideoTitle(); ?>
                <div class="item">
                    <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $video['id'], 'video')}}">
                        <mip-img height="100" layout="responsive" src="{{empty($video['cover']) ? env('CDN_URL').'/img/mobile/image_default_n.jpg' : $video['cover']}}"></mip-img>
                        <p class="con">{{$vTitle}}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
@section("js")
    <script src="https://c.mipcdn.com/static/v1/mip-vd-tabs/mip-vd-tabs.js"></script>
@endsection