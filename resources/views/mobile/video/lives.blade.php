<?php
    $keywords = "爱看球,爱看球录像,中超录像,英超录像,全场回放,高清录像";
    $description = "爱看球录像，为球迷收录各大联赛的精彩录像、高清录像。";
?>
@extends('mobile.layout.base')
@section("body_attr") onscroll="scrollBottom(loadVideos);" @endsection
@section('title')
    <title>爱看球-爱看球录像|NBA录像|英超录像|意甲录像|西甲录像|全场回放|高清录像</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoList.css?rd=2018050001">
    <style>
        #Navigation {
            background: #4492fd;
        }
    </style>
@endsection
@section('banner')
    <div id="Navigation">
        <h1>JRS低调看爱看球直播</h1>
        <div class="banner">
            <!-- <p class="type"><button class="on" id="Football" name="type">足球</button><button id="Basketball" name="type">篮球</button><button id="Other" name="type">其他</button></p> -->
            <img src="{{env('CDN_URL')}}/img/mobile/image_slogan_nav.png">
        </div>
    </div>
@endsection

@section('content')
    <a href="http://mp.dlfyb.com/downloadPhone.html"><img style="width: 100%" src="/img/mobile/image_ad_wap.jpg"></a>
    <?php $week_array = array('周日','周一','周二','周三','周四','周五','周六'); ?>
    @foreach($matches as $time=>$match_array)
        <?php $week = date('w', strtotime($time)); ?>
        <div class="default">
            <p class="day" day="{{$time}}">{{$time}}&nbsp;&nbsp;{{$week_array[$week]}}</p>
            @foreach($match_array as $match)
                <a href="{{\App\Http\Controllers\PC\MatchTool::subjectLink($match['id'], 'video')}}">
                    <p class="time">{{$match['lname']}}&nbsp;&nbsp;{{date('H:i', $match['time'])}}</p>
                    <p class="other">{{$match['hname'] . ' ' . $match['hscore'] . ' - ' . $match['ascore'] . ' ' . $match['aname']}}</p>
                </a>
            @endforeach
        </div>
    @endforeach
    <div class="nolist separated">暂时无直播比赛</div>
    <p id="PC"><a href="http://mp.dlfyb.com/downloadPhone.html">下载爱看球APP，流畅度快3倍<br/>www.aikq.cc</a><button class="close" onclick="this.parentNode.style.display='none'"></button></p>
@endsection

@section('bottom')
    <dl id="Bottom">
        <dd>
            <a href="/">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_live_n.png">
                <p>直播</p>
            </a>
        </dd>
        <dd class="">
            <a href="/anchor">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_anchor_n.png">
                <p>主播</p>
            </a>
        </dd>
        <dd class="on">
            <a href="">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_vedio_s.png">
                <p>录像</p>
            </a>
        </dd>
        <dd>
            <a href="https://shop.liaogou168.com">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_recommend_n.png">
                <p>推荐</p>
            </a>
        </dd>
    </dl>
@endsection

@section('js')
    <script type="text/javascript">
        window.curPage = '{{$page['curPage']}}';
        window.loadPage = false;
        function changeTab(tab) {
            switch (tab){
                case 'all':
                    window.location.replace('/');
                    break;
                case 'football':
                    window.location.replace('/football.html');
                    break;
                case 'basketball':
                    window.location.replace('/basketball.html');
                    break;
                case 'other':
                    window.location.replace('/other.html');
                    break;
                case 'live':
                    window.location.replace('/');
                    break;
                case 'video':
                    window.location.replace('/live/subject/videos/all/1.html');
                    break;
            }
        }
    </script>
    <script type="text/javascript" src="/js/public/mobile/subjectVideo.js"></script>
@endsection