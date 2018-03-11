@extends('mobile.layout.base')
@section('title')
    <title>我正在爱看球看【{{$match['hname']}}vs{{$match['aname']}}】</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoPhone.css?time=201803030001">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner"><a class="home" href="{{$match['sport'] == 2 ? '/m/basketball.html' : '/m/'}}"></a>比赛直播</div>
    </div>
@endsection
@section('content')
    <div class="default" id="Info">
        <div class="team host">
            <img src="{{$host_icon}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_team_default.png"' >
            <p>{{$match['hname']}}</p>
        </div>
        <div class="score">
            @if($show_live || $match['status'] == -1)
            <p style="display: {{$show_live ? 'none' : 'block'}};">
                <span class="host">{{$match['hscore']}}</span>
                <span class="away">{{$match['ascore']}}</span>
            </p>
            <button onclick="showScore(this);">{{$show_live ? '显示比分' : '隐藏比分'}}</button>
            @else <b>VS</b>
            @endif
        </div>
        <div class="team away">
            <img src="{{$away_icon}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_team_default.png"' >
            <p>{{$match['aname']}}</p>
        </div>
    </div>
    <div class="default" id="Video">
        @if(!isset($live))
            <p class="line">
                <button disabled>线路一</button>
                <button disabled>线路二</button>
                <button disabled>线路三</button>
            </p>
        @else
            <?php $channels = $live['channels'];?>
            <div class="line" style="visibility: hidden;">
                @foreach($channels as $index=>$channel)
                    <?php
                    if ($channel['type'] == 3 || $channel['type'] == 1 || $channel['type'] == 2 || $channel['type'] == 7)
                        $preUrl = str_replace("https://","http://",env('APP_URL'));
                    else if($channel['type'] == 99){
                        if ($channel['player'] == 11){
                            $preUrl = str_replace("https://","http://",env('APP_URL'));
                        }
                        else{
                            if (stristr($channel['link'],'player.pptv.com')){
                                $preUrl = str_replace("https://","http://",env('APP_URL'));
                            }
                            else{
                                $preUrl = str_replace("http://","https://",env('APP_URL'));
                            }
                        }
                    } else {
                        $preUrl = str_replace("http://","https://",env('APP_URL'));
                    }
                    ?>
                    {{--@if($show_live) onclick="ChangeChannel('{{$preUrl.'/live/player.html?cid='.$channel['id']}}', this)" @endif--}}
                    <button id="{{$channel['channelId']}}" value="{{$preUrl.'/live/player/player-'.$channel['id'].'-'. $channel['type'] .'.html'}}">{{$channel['name']}}</button>
                @endforeach
                <?php $ch_cn = ['线路一', '线路二', '线路三']; ?>
                @for($index = count($channels); $index < 3; $index++)
                <button disabled>{{$ch_cn[$index]}}</button>
                @endfor
            </div>
        @endif
            <iframe id="Frame"></iframe>
            {{--@if($show_live)--}}
                {{--<iframe id="Frame"></iframe>--}}
            {{--@elseif($match['status'] == 0 && !$show_live)--}}
                {{--<img src="{{env('CDN_URL')}}/img/pc/image_video_bg.jpg" width="100%" height="100%">--}}
            {{--@elseif($match['status'] == -1 && !$show_live)--}}
                {{--<img src="{{env('CDN_URL')}}/img/pc/image_video_bg.jpg" width="100%" height="100%">--}}
            {{--@endif--}}
    </div>
    <div id="Content">
        <img src="{{env('CDN_URL')}}/img/pc/demo.jpg">
        <p>扫二维码进入群</p>
    </div>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/videoPhone.js?time=201803030001"></script>
    <script>
        window.onload = function () {
            setPage();
        }
    </script>
    <script type="text/javascript">
        function showScore(btnObj) {
            var btn = $(btnObj);
            var score = $(btnObj).prev();
            if (btn.html() == "隐藏比分") {
                btn.html("显示比分");
                score.hide();
            } else {
                btn.html("隐藏比分");
                score.show();
            }
        }

        @if($match['sport'] == 1)
        //----------------------------------------------------------------------//
        //刷新比赛信息
        function refresh() {
            @if(isset($match['mid']))
            var preTime = 0;
            $.ajax({
                "url": "/m/lives/data/refresh.json?time=" + (new Date()).getTime(),
                "dataType": "json",
                "success": function (json) {
                    var dataItem = json["{{$match['mid']}}"];
                    if (!dataItem) {
                        return;
                    }
                    var scoreItem = $("div.score .host");
                    var scoreItem2 = $("div.score .away");

                    var currentScore = dataItem.score;
                    currentScore = currentScore.replace(' ','');
                    currentScore = currentScore.replace(' ','');
                    var scores = currentScore.split('-');
                    if (scoreItem) {
                        scoreItem.html(scores[0]);
                        scoreItem2.html(scores[1]);
                    }
                },
                "error": function () {

                }
            });
            @endif
        }
        //----------------------------------------------------------//
            @if($match['status'] > 0 && $match['status'] < 4)
            setInterval(refresh, 5000);//获取比赛统计数据
            @endif
        @endif
    </script>
@endsection