<?php
    $keywords = "爱看球," . $match['lname'] ."录像," . $match['hname'] . "," . $match['aname'];
    $description = "《" . $match['hname'] . ' VS ' . $match['aname'] . "》高清全场回放";
?>
@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{\App\Http\Controllers\Mip\UrlCommonTool::MIP_PREFIX}}/css/record.css">
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
            <mip-iframe allowfullscreen height="210" allowtransparency="true" src="{{$svc['content']}}" class="MyIframe"></mip-iframe>
            @foreach($allChannels as $ch) @continue($ch->id == $svc->id)
            <mip-iframe allowfullscreen height="210" allowtransparency="true" src="{{$ch['content']}}" class="MyIframe"></mip-iframe>
            @endforeach
        </mip-vd-tabs>
    </div>
    @if(isset($moreVideos) && count($moreVideos) > 0)
    <div id="Content">
        <div id="Record">
            <p class="title">更多精彩视频</p>
            @foreach($moreVideos as $video)
                <div class="item">
                    <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $video['id'], 'video')}}">
                        <mip-img height="100" layout="responsive" src="{{empty($video['cover']) ? '/img/pc/video_bg.jpg' : $video['cover']}}"></mip-img>
                        <p class="con">{{$video['title']}}</p>
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