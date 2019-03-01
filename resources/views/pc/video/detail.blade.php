@extends('pc.layout.v2.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env("CDN_URL")}}/css/pc/v2/left_right_2.css">
    <link rel="stylesheet" type="text/css" href="{{env("CDN_URL")}}/css/pc/v2/video_2.css">
@endsection
@section("content")
    <div id="Crumbs">
        <div class="def_content">
            <a href="/">爱看球</a>
            @if(isset($def))
            - <a href="/{{$def["name_en"]}}/">{{$def["name"]}}</a>
            - <a href="/{{$def["name_en"]}}/video/">{{$def["name"]}}视频</a>
            @else
            - <a href="/video/">最新视频</a>
            @endif
            - {{$video["title"]}}
        </div>
    </div>

    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div id="Video_play_box">
                <h1>{{$video["title"]}}</h1>
                <a target="_blank" href="{{$video["link"]}}"><img src="{{$video["image"]}}"></a>
                {{-- <iframe src="" scrolling="no" allowfullscreen="true"></iframe> --><!-- 直接播放视频 --}}
            </div>
            @if(isset($comboData["videos"]))
            <div class="el_con">
                <div class="header">
                    <h3><p>{{isset($def) ? $def["name"] : "最新"}}视频</p></h3>
                    <p class="aline">
                        @if(isset($def))
                            <a target="_blank" href="/{{$def["name_en"]}}/video/">全部{{$def["name"]}}视频 ></a>
                        @else
                            <a target="_blank" href="/video/">全部视频 ></a>
                        @endif
                    </p>
                </div>
                <div class="video_list">
                    @foreach($comboData["videos"] as $cVideo)
                    <div class="list_item">
                        <a target="_blank" href="{{$cVideo["link"]}}">
                            <p class="img_box"><img src="{{$cVideo["image"]}}"></p>
                            <div class="title_text"><p><span>{{$cVideo["title"]}}</span></p></div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @if(isset($comboData["matches"]) && count($comboData["matches"]) > 0)
            <div class="el_con">
                <div class="header">
                    <h3><p>{{isset($def) ? $def["name"] : "最新"}}直播</p></h3>
                    <p class="aline">
                        <a target="_blank" href="/">全部直播></a>
                    </p>
                </div>
                <table class="match">
                    <col width="11%"><col><col width="11%"><col><col width="40%">
                    @foreach($comboData["matches"] as $cMatch)
                    <tr>
                        <td>{{substr($cMatch["time"], 5, 5)}}<br/><span>{{substr($cMatch["time"], 11, 5)}}</span></td>
                        <td class="host"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($cMatch['sport'], $cMatch['lid'], $cMatch['hid'])}}">{{$cMatch["hname"]}}</a></td>
                        <td class="vs">@if($cMatch["isMatching"])<span class="living">直播中</span>@else vs @endif</td>
                        <td class="away"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($cMatch['sport'], $cMatch['lid'], $cMatch['hid'])}}">{{$cMatch["aname"]}}</a></td>
                        <td class="line">
                            @foreach($cMatch["channels"] as $channel)
                                <?php $liveUrl = $channel["player"] == 16 ? $channel["link"] : $channel["live_url"]; ?>
                                <a target="_blank" href="{{$liveUrl}}" class="live">{{$channel["name"]}}</a>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
        </div>
        <div id="Right_part">
            {{--<a class="banner_entra" href="league_nba.html">--}}
                {{--<img src="https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=c95a12f874f0f736d8fe4b07326ed424/3801213fb80e7bec36d92766232eb9389b506b31.jpg">--}}
                {{--<h3>美国男子职业篮球联赛</h3>--}}
                {{--<p>球队：<span>30支</span></p>--}}
            {{--</a>--}}
            {{--<a class="banner_entra" href="team.html">--}}
                {{--<img src="http://img1.gtimg.com/sports/pics/hv1/231/116/2220/144385311.png">--}}
                {{--<h3>圣安东尼奥马刺</h3>--}}
                {{--<p>赛事：<span>NBA</span>排名：<span>东部第1名</span></p>--}}
            {{--</a>--}}
            @if(isset($comboData["articles"]) && count($comboData["articles"]) > 0)
            <div class="con_box">
                <div class="header_con">
                    <h4>{{isset($def) ? $def["name"] : "最新"}}资讯</h4>
                    @if(isset($def))
                        <a target="_blank" href="/{{$def["name_en"]}}/news/">全部{{$def["name"]}}资讯</a>
                    @else
                        <a target="_blank" href="/news/">全部资讯</a>
                    @endif
                </div>
                <div class="news">
                    @foreach($comboData["articles"] as $index=>$cArticle)
                        @if($index < 2)
                            <a target="_blank" href="{{$cArticle["link"]}}" class="img_news">
                                <p class="img_box"><img src="{{$cArticle["cover"]}}"></p>
                                <h3>{{$cArticle["title"]}}</h3>
                            </a>
                        @else
                            <a target="_blank" href="{{$cArticle["link"]}}" class="text_new"><h4>{{$cArticle["title"]}}</h4></a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
            @if(isset($comboData["records"]) && count($comboData["records"]) > 0)
            <div class="con_box">
                <div class="header_con">
                    <h4>{{isset($def) ? $def["name"] : "最新"}}录像</h4>
                    @if(isset($def))
                        <a target="_blank" href="/{{$def["name_en"]}}/record/">全部{{isset($def) ? $def["name"] : ""}}录像</a>
                    @else
                        <a target="_blank" href="/record/">全部{{isset($def) ? $def["name"] : ""}}录像</a>
                    @endif
                </div>
                <table class="record">
                    <col width="25%"><col><col width="25%">
                    @foreach($comboData["records"] as $vRecord)
                    <?php
                        $vTime = $vRecord["match"]["time"];
                        $sport = $vRecord["match"]['sport'];
                        $lid = $vRecord["match"]['lid'];
                        $hid = $vRecord["match"]['hid'];
                        $aid = $vRecord["match"]['aid'];
                        $hname = $vRecord["match"]["hname"];
                        $aname = $vRecord["match"]["aname"];
                    ?>
                    <tr>
                        <td class="time">{{substr($vTime, 5, 5)}}<br/>{{substr($vTime, 11, 5)}}</td>
                        <td>
                            <p><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $hid)}}">{{$hname}}</a><span>{{$vRecord["match"]["hscore"]}}</span></p>
                            <p><a target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($sport, $lid, $aid)}}">{{$aname}}</a><span>{{$vRecord["match"]["ascore"]}}</span></p>
                        </td>
                        <td><a target="_blank" href="{{$vRecord["link"]}}">观看录像</a></td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
        </div>
    </div>

@endsection

<!-- <div class="adflag left">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div>
<div class="adflag right">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div> -->
</body>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<!-- <script type="text/javascript" src="js/team.js"></script> -->
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        setADClose();
    }
</script>
</html>