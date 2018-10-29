@extends('mobile.layout.base')
@section('title')
    <title>我正在爱看球看【{{$match['hname']}}vs{{$match['aname']}}】</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoPhone2.css">
    <style>
        .weixin {
            padding-bottom: 10px;
            font-size: 40px;
            line-height: 60px;
            text-align: center;
        }
        #Navigation + #qsk_click{
            display: none;
        }
    </style>
@endsection
@section('banner')
    <div id="Navigation">
        @if(isset($h1))
            <h1>{{$h1}}</h1>
        @endif
        <div class="banner"><a class="home" href="/"></a>爱看球</div>
    </div>
@endsection
@section('content')
    <div id="qsk_click"><img src="{{env('CDN_URL')}}/img/mobile/qsk_click_n.png" ></div>
    <script type="text/javascript">//document.getElementById("qsk_click").style.display = 'none';</script>
    <div class="default" id="Info">
        @if($match['sport'] == 3)
            @if(isset($match['type']) && $match['type'] == 1)
                <p class="other">{{$match['hname']}}</p>
            @else
                <div class="team host">
                    <img src="{{$host_icon}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"' >
                    <p>{{$match['hname']}}</p>
                </div>
                <div class="score"><b>VS</b></div>
                <div class="team away">
                    <img src="{{$away_icon}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"' >
                    <p>{{$match['aname']}}</p>
                </div>
            @endif
        @else
            <div class="team host">
                <img src="{{$host_icon}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"' >
                <p>{{$match['hname']}}</p>
            </div>
            <div class="score">
                {{--@if($show_live || $match['status'] == -1)--}}
                {{--<p style="display: {{$show_live ? 'none' : 'block'}};">--}}
                {{--<span class="host">{{$match['hscore']}}</span>--}}
                {{--<span class="away">{{$match['ascore']}}</span>--}}
                {{--</p>--}}
                {{--<button onclick="showScore(this);">{{$show_live ? '显示比分' : '隐藏比分'}}</button>--}}
                {{--@else <b>VS</b>--}}
                {{--@endif--}}
                <b>VS</b>
            </div>
            <div class="team away">
                <img src="{{$away_icon}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"' >
                <p>{{$match['aname']}}</p>
            </div>
        @endif
    </div>
    <?php $channels = $live['channels'];?>
    <div class="default" id="Video">
        @if(!isset($live))
            <p class="line" style="display: none;">
                <button disabled>线路一</button>
                <button disabled>线路二</button>
                <button disabled>线路三</button>
            </p>
        @else
            <div class="line" @if($match['sport'] == 3 && count($channels) == 1) style="display: none" @endif>
                @foreach($channels as $index=>$channel)
                    <?php
                        $content = $channel['link'];
                        $player = $channel['player'];
                        if ($player == 11) {
                            $link = '/live/iframe/player-'.$channel['id'].'-'.$channel['type'].'.html';
                        } else {
                            $link = '/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
                        }
                    ?>
                    <button https="{{preg_match("/\.m3u8/", $content)}}" id="{{$channel['channelId']}}" value="{{$link}}">{{$channel['name']}}</button>
                @endforeach
                @if($match['sport'] < 3 && count($channels) < 3)
                    {{--<button onclick="window.open('https://shop.liaogou168.com/lqb/articles/{{$match['sport']}}/{{$match['mid']}}.html?default=1')">专家推荐</button>--}}
                @endif
                <?php //$ch_cn = ['线路一', '线路二', '线路三']; ?>
                {{--@for($index = count($channels); $index < 3; $index++)--}}
                {{--<button disabled>{{$ch_cn[$index]}}</button>--}}
                {{--@endfor--}}
            </div>
        @endif
        <iframe id="Frame"></iframe>
        <div class="publicAd"><img src="{{env('CDN_URL')}}/img/pc/banner_app_868.jpg"></div>
    </div>
    {{--<div id="Content">--}}
        {{--<img src="{{env('CDN_URL')}}/img/pc/image_qr_868.jpg">--}}
        {{--<p>扫二维码进入群</p>--}}
    {{--</div>--}}
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/videoPhone2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            setPage();
        }
    </script>
@endsection