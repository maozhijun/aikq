@extends('mobile.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/record.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner"><a class="home" href="/"></a>{{$video['hname'] .' VS '.$video['aname']}}</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Video">
        <iframe src="{{$svc['content']}}" id="MyIframe"></iframe>
    </div>
    <div class="tabbox">
        <button class="on" value="{{$svc['content']}}">{{$svc['title']}}</button>
        @foreach($allChannels as $ch)
        @continue($ch['id'] == $svc['id'])
        <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $ch['id'], 'video')}}">{{$ch['title']}}</a>
        @endforeach
    </div>
    @if(isset($moreVideos) && count($moreVideos) > 0)
    <div id="Content">
        <div id="Record">
            <p class="title">更多精彩视频</p>
            @foreach($moreVideos as $mv)
                <div class="item">
                    <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($mv['s_lid'], $mv['id'], 'video')}}">
                        <p class="imgbox" style="background: url({{empty($mv['cover']) ? '/img/pc/video_bg.jpg' : $mv['cover']}}) no-repeat center; background-size: cover;"></p>
                        <p class="con">{{$mv['title']}}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
@section('js')
@endsection