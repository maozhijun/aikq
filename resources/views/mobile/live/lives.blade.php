@extends('mobile.layout.base')
@section('title')
    <title>爱看球</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoList.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <p class="type">
                <button onclick="changeTab('football')" @if(!isset($type) || $type == 'football') class="on" @endif id="Football" name="type">足球</button>
                <button onclick="changeTab('basketball')" @if(isset($type) && $type == 'basketball') class="on" @endif id="Basketball" name="type">篮球</button>
            </p>
        </div>
    </div>
@endsection
@section('content')
    @foreach($matches as $time=>$match_array)
        <?php
        $week = date('w', strtotime($time));
        $week_array = array('周日','周一','周二','周三','周四','周五','周六');
        ?>
        <div class="default">
            <p class="day">{{$time}}&nbsp;&nbsp;{{$week_array[$week]}}</p>
            @foreach($match_array as $match)
                <a href="{{str_replace('https://', 'http://', asset('m/live/'.($match['sport'] == 1 ? 'football':'basketball').'/' . $match['mid'].'.html'))}}">
                    <p class="time">{{$match['league_name']}}&nbsp;&nbsp;{{date('H:i', strtotime($match['time']))}}</p>
                    <p class="team host">{{$match['hname']}}</p>
                    <p class="vs">@if($match['isMatching'])<img src="/img/pc/icon_live.png">@else VS @endif</p>
                    <p class="team away">{{$match['aname']}}</p>
                </a>
            @endforeach
        </div>
    @endforeach
    <p id="PC">更多赛事线路与特色功能，请登录电脑版！<br/>www.aikq.cc<button class="close" onclick="this.parentNode.style.display='none'"></button></p>
@endsection
@section('js')
    <script type="text/javascript">
        function changeTab(tab) {
            switch (tab){
                case 'all':
                    window.location.replace('/m');
                    break;
                case 'football':
                    window.location.replace('/m/football.html');
                    break;
                case 'basketball':
                    window.location.replace('/m/basketball.html');
                    break;
            }
        }
    </script>
@endsection