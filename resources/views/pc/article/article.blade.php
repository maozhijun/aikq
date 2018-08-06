@extends("pc.layout.anchor_base")
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/article.css">
@endsection
@section("content")
    <div id="Content">
        <div class="inner">
            <dl id="Right">
                <dt>直播赛程</dt>
            </dl>
            <div id="Left">
                <div class="con">
                    <h1>{{$article->title}}</h1>
                    <p class="info">{!! !empty($article->resource) ? "来源：料狗&nbsp;&nbsp;&nbsp;" : "" !!}
                        作者：{{$article->author}}&nbsp;&nbsp;&nbsp;&nbsp;{{$article->type_obj->name}}<span>发表于：{{substr($article->publish_at, 0, 16)}}</span></p>
                    <div class="detail">{!! $article->getContent() !!}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("js")
<script type="text/javascript">
    $.get("/news/lives.html", function (html) {
        $("#Right").html(html);
    });
</script>
@endsection