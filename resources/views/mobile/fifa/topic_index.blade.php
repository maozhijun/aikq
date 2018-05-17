@extends('mobile.fifa.base')
@section('content')
    <div id="Topbar">
        <a href="https://www.aikq.cc/m/">直播</a>
        <a href="https://www.aikq.cc/m/live/subject/videos/all/1.html">录像</a>
        <a href="https://shop.liaogou168.com/article/recommends">推荐</a>
        <a class="on">世界杯</a>
    </div>
    @foreach($topics as $topic)
        <a class="li" href="{{'https://shop.liaogou168.com/news/detail/'.$topic['id'].'.html'}}">
            <div class="img" style="background: url({{$topic['coverImg']}}) no-repeat center; background-size: cover;"></div>
            <p class="title">{{$topic['title']}}</p>
            <p class="time">{{date("Y/m/d", $topic['publishAt'])}}&nbsp;&nbsp;{{date("H:i", $topic['publishAt'])}}</p>
        </a>
    @endforeach
    @component('mobile.fifa.base_bottom',['index'=>3])
    @endcomponent
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/team.js?time=201803030002"></script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/news.css">
@endsection
