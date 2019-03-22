<?php $cdnUrl = env("CDN_URL", ""); $index = 0; ?>
@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/mobile/v2/match_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/mobile/v2/home_wap_2.css">
@endsection
@section('banner')
    <div id="Navigation">
        <h1>爱看球</h1>
        <div class="header_con">
            <img src="{{$cdnUrl}}/img/mobile/v2/logo_white.png" class="logo_icon">
            <a href="/download/" class="app">APP下载</a>
        </div>
        <div class="tab_con">
            <p class="tab_item on" forItem="all">全部</p>
            <p class="tab_item" forItem="football">足球</p>
            <p class="tab_item" forItem="basketball">篮球</p>
            {{--<a class="tab_item" href="match_end.html">完赛</a>--}}
        </div>
        <div class="date_con">
            @foreach($matches as $time=>$match_array)
                <?php
                    $mt = strtotime($time);
                    $week = date('w', $mt);
                    $week_array = array('周日','周一','周二','周三','周四','周五','周六');
                    $isShowDate = false;
                    $date = date('m-d', $mt);
                    $now = strtotime(date("Y-m-d"));
                ?>
                <div class="date_item {{$mt == $now ? "on" : ""}}" forItem="{{date('m_d', $mt)}}">
                    <p class="week">{{$now == $mt ? "今天" : $week_array[$week]}}</p>
                    <p class="date">{{$date}}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('content')
    @foreach($matches as $time=>$match_array)
        <?php $date = date('m_d', strtotime($time)); ?>
        <div class="match_list_con {{$date}}" style="display: {{$index++ == 0 ? "block" : "none"}}">
            @foreach($match_array as $key=>$match)
            <?php
                $sport = $match["sport"];
                $lid = $match["lid"];
                $mid = $match["mid"];

                $channels = $match['channels'];
                $firstChannel = isset($channels[0]) ? $channels[0] : [];
                $impt = isset($firstChannel['impt']) ? $firstChannel['impt'] : 1;

                $url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $mid);
                $sportClass = $sport == 1 ? "football" : ($sport == 2 ? "basketball" : "");
                $liveClass = $match['isMatching'] ? "live" : "";
                $goodClass = $impt == 2 ? "good" : "";
            ?>
            <a href="{{$url}}" class="{{$sportClass}} {{$liveClass}} {{$goodClass}}">
                <div class="team_con">
                    <p>{{$match["hname"]}}</p>
                    <p>{{$match["aname"]}}</p>
                </div>
                <div class="info_con">
                    <p>{{$match["league_name"]}}</p>
                    <p>{{date('H:i', strtotime($match['time']))}}</p>
                </div>
                <div class="status_con"></div>
            </a>
            @endforeach
        </div>
    @endforeach
@endsection
@section('bottom')
    @component("mobile.layout.v2.bottom_cell", ["cur"=>'live']) @endcomponent
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdnUrl}}/js/mobile/v2/home_wap_2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage()
        }
    </script>
@endsection