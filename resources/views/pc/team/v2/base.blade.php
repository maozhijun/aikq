@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?time=20192191536">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/team_2.css?time=20192191536">
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$zhuanti['name']}}录像
            </div>
        </div>
    @endif
    <?php
    $coach = "-";
    $sTitle = "";
    ?>
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div id="Team_info">
                <p class="img_con"><img src="{{$team['icon']}}" onerror='this.src="{{env('CDN_URL')}}/img/pc/icon_teamDefault.png"'></p>
                <h1>{{$team['name']}}</h1>
                <div class="other_info">
                    <p>现任主教练：{{$coach}}</p>
                    <p>外文队名：{{isset($team['name_en']) ? $team['name_en'] :'-'}}</p>
                    <p>所在城市：{{$team['city']}}</p>
                    <p>球队主场：{{$team['gym']}}</p>
                    <p>成立时间：{{strlen($team['establish']) > 0 ? $team['establish'] : "-"}}</p>
                    {{--<p>所属联赛：英超（英格兰超级联赛）</p>--}}
                </div>
            </div>
            {{--<div id="Tab_con">--}}
                {{--<p><a href="/{{$name_en}}/team{{$tid}}_index.html">综合</a></p>--}}
                {{--<p><a href="/{{$name_en}}/team{{$tid}}_news.html">资讯</a></p>--}}
                {{--<p><a href="/{{$name_en}}/team{{$tid}}_videos.html">视频</a></p>--}}
                {{--<p class="on"><a href="#">录像</a></p>--}}
            {{--</div>--}}
            @yield('detail')
        </div>
        <div id="Right_part">
            {{--<a class="banner_entra" href="">--}}
                {{--<img src="https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=c95a12f874f0f736d8fe4b07326ed424/3801213fb80e7bec36d92766232eb9389b506b31.jpg">--}}
                {{--<h3>美国男子职业篮球联赛</h3>--}}
                {{--<p>球队：<span>30支</span></p>--}}
            {{--</a>--}}
            {{--<div class="con_box">--}}
                {{--<div class="header_con">--}}
                    {{--<h4>最近直播</h4>--}}
                    {{--<a href="live_list.html">全部直播</a>--}}
                {{--</div>--}}
                {{--<div class="live">--}}
                    {{--<div class="live_item">--}}
                        {{--<p class="live_match_info">NBA<span>01-24 16:20</span></p>--}}
                        {{--<div class="live_match_team">--}}
                            {{--<p class="team"><span><a href="team.html">达拉斯独行侠</a></span></p>--}}
                            {{--<p class="vs"><span>直播中</span></p>--}}
                            {{--<p class="team"><span><a href="team.html">多伦多猛龙</a></span></p>--}}
                        {{--</div>--}}
                        {{--<div class="live_match_line">--}}
                            {{--<a href="live.html">高清直播</a>--}}
                            {{--<a href="live.html">主播剧本球童</a>--}}
                            {{--<a href="live.html">体育直播</a>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="con_box">
                <div class="header_con">
                    <h4>{{strlen($sTitle) == 0 ? '最新' : $sTitle}}资讯</h4>
                    {{--<a href="{{isset($zhuanti) == 0 ? '/news/':'/'.$zhuanti['name_en'].'/news/'}}">全部{{$sTitle}}资讯</a>--}}
                    <a href="/news/">全部资讯</a>
                </div>
                <div class="news">
                    @foreach($articles as $index=>$article)
                        @if($index < 2)
                            <a href="{{$article['link']}}" class="img_news">
                                <p class="img_box"><img src="{{$article['cover']}}"></p>
                                <h3>{{$article['title']}}</h3>
                            </a>
                        @else
                            <a href="{{$article['link']}}" class="text_new"><h4>{{$article['title']}}</h4></a>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/live_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection