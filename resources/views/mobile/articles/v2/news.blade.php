@extends('mobile.layout.v2.base')
{{--@section("body_attr") onscroll="scrollBottom(loadNews);" @endsection--}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/news_list_wap_2.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="column_con">
            <div class="run_line">
                <a class="column_item on">全部</a>
                <a class="column_item" href="league.html?type=news">NBA</a>
                <a class="column_item" href="league.html?type=news">英超</a>
                <a class="column_item" href="league.html?type=news">西甲</a>
                <a class="column_item" href="league.html?type=news">德甲</a>
                <a class="column_item" href="league.html?type=news">法甲</a>
                <a class="column_item" href="league.html?type=news">意甲</a>
                <a class="column_item" href="league.html?type=news">欧冠</a>
                <a class="column_item" href="league.html?type=news">欧联</a>
                <a class="column_item" href="league.html?type=news">CBA</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    {{--<div id="Content">--}}
        {{--@foreach($page as $article)--}}
            {{--<a href="{{$article->getUrl()}}" class="li">--}}
                {{--@if(!empty($article->cover))--}}
                    {{--<div class="imgbox" style="background: url({{$article->getCover()}}) no-repeat center; background-size: cover;"></div>--}}
                {{--@endif--}}
                {{--<h6>{{$article->title}}</h6>--}}
                {{--<p class="info">{{substr($article->publish_at, 0, 16)}}</p>--}}
            {{--</a>--}}
        {{--@endforeach--}}
    {{--</div>--}}
    <div class="news_list_con">
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
        <a href="news.html">
            <p class="img_box" style="background-image: url(http://img1.gtimg.com/sports/pics/hv1/109/76/2305/149902114.png)"></p>
            <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
            <p class="date_con">01-02</p>
            <p class="tag_con"><span>英超</span><span>利物浦</span><span>萨拉赫</span><span>萨拉赫</span><span>萨拉赫</span></p>
        </a>
    </div>
@endsection
@section('bottom')
    @include("mobile.layout.v2.bottom_cell", ['cur'=>'news'])
@endsection
@section("js")
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/news_list_wap_2.js?time=201903140958"></script>
    <script type="text/javascript">
//        window.curPage = 1;
        {{--window.lastPage = parseInt('{{$page->lastPage()}}');--}}
//        window.loadPage = false;
        var nowPage = 0;
        window.onload = function () {
            setPage()
        }
    </script>
@endsection

