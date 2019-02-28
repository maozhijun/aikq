@extends("pc.layout.v2.base")
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/news_2.css?rd={{date('YmdHi')}}">
    {{--<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/jqcloud.css?rd={{date('YmdHi')}}">--}}
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?rd={{date('YmdHi')}}">
@endsection
@section("content")
    <div id="Crumbs">
        <div class="def_content">
            @if(isset($zhuanti))
                <a href="/">爱看球</a> - <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a> - <a href="/{{$zhuanti['name_en']}}/news/">{{$zhuanti['name']}}资讯</a> - {{$article->title}}
            @else
                <a href="/">爱看球</a> - <a href="/news/">资讯</a> - {{$article->title}}
            @endif
        </div>
</div>
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div id="News_Con">
                <div class="header">
                    <h1>{{$article->title}}</h1>
                    <p class="news_info">{!! !empty($article->resource) ? "来源：" . $article->resource . "&nbsp;&nbsp;&nbsp;" : "" !!}
                        作者：{{$article->author}}&nbsp;&nbsp;&nbsp;&nbsp;{{$article->type_obj->name}}<span>发表于：{{substr($article->publish_at, 0, 16)}}</span></p>
                </div>
                <div class="text_con">{!! $article->getContent() !!}</div>
            </div>
            @if(isset($res) && count($res) > 0)
                <div class="el_con">
                    <div class="header">
                        <h3><p>相关资讯</p></h3>
                        <p class="aline">
                            @if(isset($zhuanti))
                                <a href="/{{$zhuanti['name_en']}}/news/">全部{{$zhuanti['name']}}资讯 ></a>
                            @else
                                <a href="/news/">全部资讯 ></a>
                            @endif
                        </p>
                    </div>
                    <div class="news_list">
                        @foreach($res as $re)
                            <div class="news_con">
                                <a href="{{$re->url}}">
                                    <p class="img_box"><img src="{{$re->cover}}"></p>
                                    <h5>{{$re->title}}</h5>
                                    <p class="other_info">{{date('m-d', strtotime($re->publish_at))}}</p>
                                    <p class="tag_list">
                                        @foreach(explode(',', $re->labels) as $tag)
                                            <span>{{$tag}}</span>
                                        @endforeach
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div id="Right_part">
            <div class="con_box" style="display: none">
                <div class="header_con">
                    @if(isset($zhuanti))
                        <h4>{{$zhuanti['name']}}赛事直播</h4>
                        <a href="/{{$zhuanti['name_en']}}/">全部直播</a>
                    @else
                        <h4>赛事直播</h4>
                        <a href="/">全部直播</a>
                    @endif
                </div>
                <div class="live">
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span><a href="team.html">达拉斯独行侠</a></span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span><a href="team.html">多伦多猛龙</a></span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="con_box" style="display: none">
                <div class="header_con">
                    @if(isset($zhuanti))
                        <h4>最新{{$zhuanti['name']}}视频</h4>
                        <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}视频集锦</a>
                        <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}比赛录像</a>
                    @else
                        <h4>最新视频</h4>
                        <a href="/">视频集锦</a>
                        <a href="/">比赛录像</a>
                    @endif
                </div>
                <div class="video">
                    <div class="video_item">
                        <a href="video.html">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/j0839gwmhv0.png/0"></p>
                            <p class="text_box">直击-哈登赛后采访挤爆</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("js")
    {{--<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jqcloud-1.0.4.js"></script>--}}
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/live_2.js"></script>
    <script type="text/javascript">
        $.get("/news/lives.html", function (html) {
            $("#Right dl").html(html);
        });
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
        var ua = navigator.userAgent;
        if (ua.indexOf('http://www.baidu.com/search/spider.html') > 0) {
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });
            $.ajax({
                type: 'POST',
                url: '{{env('CMS_URL')}}/api/spider/article/'+'{{$article['id']}}',
                success: function (data) {
                    console.log(data);
                },
            });
        };
    </script>
@endsection