@extends("pc.layout.base")
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/article.css?rd=20180000002">
@endsection
@section("content")
    <div id="Content">
        @if(isset($zhuanti))
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}">{{$zhuanti['name']}}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<span class="on">资讯详情</span></div>
        @else
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<a href="/news/">资讯</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<span class="on">资讯详情</span></div>
        @endif
        <div class="inner">
            <div id="Right">
                <dl>
                    <dt>直播赛程</dt>
                </dl>
            </div>
            <div id="Left">
                <div class="con">
                    <h1>{{$article->title}}</h1>
                    <p class="info">{!! !empty($article->resource) ? "来源：" . $article->resource . "&nbsp;&nbsp;&nbsp;" : "" !!}
                        作者：{{$article->author}}&nbsp;&nbsp;&nbsp;&nbsp;{{$article->type_obj->name}}<span>发表于：{{substr($article->publish_at, 0, 16)}}</span></p>
                    <div class="detail">{!! $article->getContent() !!}</div>
                </div>
                @if(isset($res) && count($res) > 0)
                <div class="other">
                    <div class="title">相关文章</div>
                    @foreach($res as $re)
                    <a target="_blank" href="{{$re->url}}">{{$re->title}}</a>
                    @endforeach
                    <p class="clear"></p>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section("js")
<script type="text/javascript">
    $.get("/news/lives.html", function (html) {
        $("#Right dl").html(html);
    });
</script>
@endsection