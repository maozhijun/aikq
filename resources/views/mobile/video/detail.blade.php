@extends('mobile.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/record.css?201901181541">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>{{$video->getVideoTitle($svc['type']) . ' ' . $svc['title']}}</h1>
        <div class="banner"><a class="home" href="/"></a>{{$video['hname'] .' VS '.$video['aname']}}</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Video">
        <a href="{{$svc['content']}}" target="_blank"></a>
    </div>
    <div class="tabbox">
        @foreach($allChannels as $ch)
            @if($ch['id'] == $svc['id'])
                <button class="on" value="{{$svc['content']}}">{{$svc['title']}}</button>
            @else
                <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($video['s_lid'], $ch['id'], 'video')}}">{{$ch['title']}}</a>
            @endif
        @endforeach
    </div>
    @if(isset($moreVideos) && count($moreVideos) > 0)
    <div id="Content">
        <div id="Record">
            <p class="title">更多精彩视频</p>
            @foreach($moreVideos as $mv)
                <?php $vTitle = $mv->getVideoTitle(); ?>
                <div class="item">
                    <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($mv['s_lid'], $mv['id'], 'video')}}">
                        <p class="imgbox" style="background: url({{empty($mv['cover']) ? env('CDN_URL').'/img/mobile/image_default_n.jpg' : $mv['cover']}}) no-repeat center; background-size: cover;"></p>
                        <p class="con">{{$vTitle}}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
@section('js')
@endsection