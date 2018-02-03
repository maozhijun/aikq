@extends('mobile.layout.base')
@section('title')
    <title>我正在爱看球看【{{$match['hname']}}vs{{$match['aname']}}】</title>
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/videoPhone.css?time=20180203">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner"><a class="home" href="{{$match['sport'] == 2 ? '/m/basketball.html' : '/m/lives.html'}}"></a>比赛直播</div>
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
            <div class="line">
                @foreach($channels as $index=>$channel)
                    <?php
//                    if ($channel['type'] == 3 || $channel['type'] == 1 || $channel['type'] == 2 || $channel['type'] == 7)
//                        $preUrl = str_replace("https://","http://",env('APP_URL'));
//                    else if($channel['type'] == 99){
//                        if ($channel['player'] == 11){
//                            $preUrl = str_replace("https://","http://",env('APP_URL'));
//                        }
//                        else{
//                            if (stristr($channel['link'],'player.pptv.com')){
//                                $preUrl = str_replace("https://","http://",env('APP_URL'));
//                            }
//                            else{
//                                $preUrl = str_replace("http://","https://",env('APP_URL'));
//                            }
//                        }
//                    }
//                    else
//                        $preUrl = str_replace("http://","https://",env('APP_URL'));
                        $preUrl = '';
                    ?>
                    <button id="{{$channel['channelId']}}" @if($show_live) onclick="ChangeChannel('{{$preUrl.'/live/player.html?cid='.$channel['id']}}', this)" @endif >{{$channel['name']}}</button>
                @endforeach
                <?php $ch_cn = ['线路一', '线路二', '线路三']; ?>
                @for($index = count($channels); $index < 3; $index++)
                    <button disabled>{{$ch_cn[$index]}}</button>
                @endfor
            </div>
        @endif
            @if($show_live)
                <iframe id="Frame"></iframe>
            @elseif($match['status'] == 0 && !$show_live)
                <img src="{{env('CDN_URL')}}/img/pc/image_video_bg.jpg" width="100%" height="100%">
            @elseif($match['status'] == -1 && !$show_live)
                <img src="{{env('CDN_URL')}}/img/pc/image_video_bg.jpg" width="100%" height="100%">
            @endif
    </div>
    <div id="Content">
        <img src="{{env('CDN_URL')}}/img/pc/code.jpg">
        <p>扫二维码进入群</p>
    </div>
@endsection
@section('js')
    <script src="{{env('CND_URL')}}/js/public/mobile/videoPhone.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            var NowID = location.href.split('/')[location.href.split('/').length -1].split('.html')[0];
            if (localStorage.getItem('Video_' + NowID)) {
                var Local = JSON.parse(localStorage.getItem('Video_' + NowID));
                if ($('#Video .line button:eq(' + Local.btn + ')')) {
                    $('#Video .line button:eq(' + Local.btn + ')').trigger("click");
                }else{
                    $('#Video .line button:eq(0)').trigger("click");
                }
            }else{
                $('#Video .line button:eq(0)').trigger("click");
            }
        }

        function ChangeChannel (Link,obj) {
            if (obj.className.indexOf('on') != -1) {
                return;
            }
            var MatchID = location.href.split('/')[location.href.split('/').length -1].split('.html')[0];
            var BtnNum = 0;
            var Btn = $('div .line button');
            for (var i = 0; i < Btn.length; i++) {
                if (obj == Btn[i]) {
                    obj.className = 'on';
                    BtnNum = i;
                }else{
                    Btn[i].className = '';
                }
            }
            var Target = {
                'id': MatchID,
                'btn': BtnNum
            }
            localStorage.setItem('Video_' + MatchID,JSON.stringify(Target));
            if (!document.getElementById('Frame')) {
                var Iframe = document.createElement('iframe');
                Iframe.id = 'Frame';
                Iframe.setAttribute('allowfullscreen','true');
                Iframe.setAttribute('scrolling','no');
                Iframe.setAttribute('frameborder','0');
                Iframe.width = '100%';
                Iframe.height = '100%';
                Iframe.src = Link;
                document.getElementById('Video').appendChild(Iframe);
            }else{
                document.getElementById('Frame').src = Link;
            }
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

        /**
         * 倒计时
         */
        function countDown() {
            var time_span = $("#Video #time");
            if (time_span.length > 0) {
                if (overTime < 5 * 60) {
                    location.reload();
                    return;
                }
                var hour = parseInt(overTime / (60 * 60));
                var minute = parseInt( (overTime - hour * 60 * 60) / 60 );
                var second = overTime - hour * 60 * 60 - minute * 60;

                var time_html = "";
                if (hour > 0) {
                    time_html = hour + "：" + (minute < 10 ? (0 + "" + minute) : minute ) + "：" + (second < 10 ? ( 0 + "" + second) : second);
                } else if (minute > 0) {
                    time_html = (minute < 10 ? (0 + "" + minute) : minute ) + "：" + (second < 10 ? ( 0 + "" + second) : second);
                } else {
                    time_html = second < 10 ? ( 0 + "" + second) : second;
                }
                $("#Video #time").html(time_html);
                overTime--;
            }
        }
        if ($("#Video #time").length > 0) {
            var overTime = {{strtotime($match['time']) - time()}};
            countDown();
            setInterval(countDown, 1000);
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