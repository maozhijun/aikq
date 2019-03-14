@extends('mobile.layout.v2.base')
@section('title')
    <title>{{$title}}</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/news_wap_2.css?201901181541">
@endsection
@section('banner')
    @include('mobile.layout.v2.top_nav_cell', ['cur'=>'news'])
@endsection
@section('content')
    <div class="bg_con" style="background-image: url({{empty($article->cover) ? env('CDN_URL').'/img/mobile/image_bg.jpg' : $article->getCover()}});"></div>
    <h1>{{$article->title}}</h1>
    <div id="Introduction">
        <div class="modify"></div>
        <p class="introduction_text">{{$article->digest}}</p>
        <p class="author_text"><span>{!! !empty($article->resource) ? "来源：" . $article->resource . "&nbsp;&nbsp;&nbsp;" : "" !!}</span><span>作者：{{$article->author}}</span></p>
    </div>
    <div id="Content">
        {!! $article->getContent() !!}
    </div>
@endsection
@section("js")
<script type="text/javascript">
    window.onload = function () {

    }
    var ua = navigator.userAgent;
    if (ua.indexOf('AKQ') >= 0){
        $('#Navigation').remove();
        $('body')[0].style.padding = '0px';
        $('#BG')[0].style.top = '0px';
    }
</script>
@endsection

