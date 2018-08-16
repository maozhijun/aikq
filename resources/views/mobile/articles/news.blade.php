@extends('mobile.layout.base')
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
    <dl id="Bottom">
        <dd>
            <a>
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_live_n.png">
                <p>直播</p>
            </a>
        </dd>
        <dd>
            <a href="anchorPhone.html">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_anchor_n.png">
                <p>主播</p>
            </a>
        </dd>
        <dd class="on">
            <a>
                <img src="{{env('CDN_URL')}}/img/mobile/icon_news_s.png">
                <p>资讯</p>
            </a>
        </dd>
        <dd>
            <a href="https://shop.liaogou168.com">
                <img src="{{env('CDN_URL')}}/img/mobile/commom_icon_recommend_n.png">
                <p>推荐</p>
            </a>
        </dd>
    </dl>
@endsection
@section("js")
<script type="text/javascript">
    window.onload = function () {

    }
</script>
@endsection

