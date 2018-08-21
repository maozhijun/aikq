@extends('mobile.layout.base')
@section('title')
    <title>爱看球-JRS直播|NBA直播|英超直播|西甲直播|低调看|直播吧</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoList.css?rd=201804">
    <style>
        #Navigation {
            background: #4492fd;
        }
    </style>
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <!-- <p class="type"><button class="on" id="Football" name="type">足球</button><button id="Basketball" name="type">篮球</button><button id="Other" name="type">其他</button></p> -->
            <img src="{{env('CDN_URL')}}/img/mobile/image_slogan_nav.png">
        </div>
    </div>
@endsection
@section('content')
    <a href="http://mp.dlfyb.com"><img style="width: 100%" src="/img/mobile/image_ad_wap2.jpg"></a>
    @foreach($matches as $time=>$match_array)
        <?php
        $week = date('w', strtotime($time));
        $week_array = array('周日','周一','周二','周三','周四','周五','周六');
        ?>
        <div class="default">
            <p class="day">{{$time}}&nbsp;&nbsp;{{$week_array[$week]}}</p>
            @foreach($match_array as $match)
                <?php
                $channels = $match['channels'];
                $firstChannel = isset($channels[0]) ? $channels[0] : [];
                $impt = isset($firstChannel['impt']) ? $firstChannel['impt'] : 1;
                $impt_style = '';
                if ($impt == 2) {
                    $impt_style = 'style="color:#bc1c25;"';
                }
                ?>
                @if($match['sport'] == 3)
                    <a href="/m/live/other/{{$match['mid']}}.html">
                        <p class="time" {!! $impt_style !!}>{{$match['league_name']}}&nbsp;&nbsp;{{date('H:i', strtotime($match['time']))}}</p>
                        @if(isset($match['type']) && $match['type'] == 1)
                            <p class="other" {!! $impt_style !!} >{{$match['hname']}}</p>
                        @else
                            <p class="team host" {!! $impt_style !!} >{{$match['hname']}}</p>
                            <p class="vs" {!! $impt_style !!} >VS</p>
                            <p class="team away" {!! $impt_style !!} >{{$match['aname']}}</p>
                        @endif
                        @if($match['isMatching']) <p class="live">直播中</p> @endif
                    </a>
                @else
                    <a href="{{'/m/live/'.($match['sport'] == 1 ? 'football':'basketball').'/' . $match['mid'].'.html'}}">
                        <p class="time" {!! $impt_style !!}>{{$match['league_name']}}&nbsp;&nbsp;{{date('H:i', strtotime($match['time']))}}</p>
                        <p class="team host" {!! $impt_style !!}>{{$match['hname']}}</p>
                        <p class="vs" {!! $impt_style !!} >VS</p>
                        <p class="team away" {!! $impt_style !!}>{{$match['aname']}}</p>
                        @if($match['isMatching']) <p class="live">直播中</p> @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endforeach
    <div class="nolist separated">暂时无直播比赛</div>
    <p id="PC"><a href="http://mp.dlfyb.com">更多比赛请到爱看球直播<br/>www.aikq.cc</a><button class="close" onclick="this.parentNode.style.display='none'"></button></p>
@endsection
@section('bottom')
    @component("mobile.layout.bottom_cell", ["cur"=>'live']) @endcomponent
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
                case 'other':
                    window.location.replace('/m/other.html');
                    break;
                case 'live':
                    window.location.replace('/m/index.html');
                    break;
                case 'video':
                    window.location.replace('/m/live/subject/videos/all/1.html');
                    break;
            }
        }
    </script>
@endsection