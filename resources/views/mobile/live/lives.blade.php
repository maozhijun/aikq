@extends('mobile.layout.base')
@section('title')
    <title>爱看球-JRS|JRS直播|NBA直播|NBA录像|CBA直播|英超直播|西甲直播|低调看|直播吧|CCTV5在线</title>
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
            <p class="type">
                {{--<button onclick="changeTab('football')" @if(!isset($type) || $type == 'football') class="on" @endif id="Football" name="type">足球</button>--}}
                {{--<button onclick="changeTab('basketball')" @if(isset($type) && $type == 'basketball') class="on" @endif id="Basketball" name="type">篮球</button>--}}
                {{--<button onclick="changeTab('other')" @if(isset($type) && $type == 'other') class="on" @endif id="Other" name="type">其他</button>--}}
                <button onclick="changeTab('live')" @if(!isset($type) || $type == 'live') class="on" @endif id="Football" name="type">直播</button>
                <button onclick="changeTab('video')" @if(isset($type) && $type == 'video') class="on" @endif id="Basketball" name="type">录像</button>
                <button onclick="window.open('https://shop.liaogou168.com/article/recommends')" @if(isset($type) && $type == 'other') class="on" @endif id="Other" name="type">推荐</button>
                {{--<button onclick="window.open('/downloadPhone.html')" id="download" name="type">下载</button>--}}
            </p>
        </div>
    </div>
@endsection
@section('content')
    <a href="/downloadPhone.html"><img style="width: 100%" src="/img/mobile/image_ad_wap.jpg"></a>
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
    <p id="PC"><a href="/downloadPhone.html">下载爱看球APP，流畅度快3倍<br/>www.aikq.cc</a><button class="close" onclick="this.parentNode.style.display='none'"></button></p>
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