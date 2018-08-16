@extends('mobile.layout.base')
@section("body_attr") onscroll="scrollBottom(loadNews);" @endsection
@section('title')
    <title>体育资讯_爱看球</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/newsPhone.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
           <img src="{{env('CDN_URL')}}/img/mobile/image_slogan_nav.png">
        </div>
    </div>
@endsection
@section('content')
    <div id="Content">
        @foreach($page as $article)
            <a href="/m{{$article->getUrl()}}" class="li">
                @if(!empty($article->cover))
                <div class="imgbox" style="background: url({{$article->getCover()}}) no-repeat center; background-size: cover;"></div>
                @endif
                <h6>{{$article->title}}</h6>
                <p class="info">{{substr($article->publish_at, 0, 16)}}</p>
            </a>
        @endforeach
    </div>
@endsection
@section('bottom')
    @component("mobile.layout.bottom_cell", ['cur'=>'news']) @endcomponent
@endsection
@section("js")
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/mobile/news.js"></script>
<script type="text/javascript">
    window.curPage = '{{$page['curPage']}}';
    window.loadPage = false;
    window.onload = function () {

    }
</script>
@endsection

