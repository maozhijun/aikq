@extends('mip.layout.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/roomPhone.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            @if(isset($h1))
                <h1>{{$h1}}</h1>
            @endif
        </div>
    </div>
@endsection
@section("content")
    <div id="Video">
        <p>主播正在客户端直播~~</p>
        <a href="/download/index.html">点击下载app观看</a>
    </div>
    <div id="Anchor">
        <div class="info">
            <mip-img layout="fixed" width="50" height="50" src="{{$anchor->icon}}"></mip-img>
            <p>{{$anchor->name}}</p>
        </div>
    </div>
@endsection

