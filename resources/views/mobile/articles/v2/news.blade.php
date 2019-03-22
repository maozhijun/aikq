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
                @foreach($subjects as $sub)
                    <a class="column_item" href="/{{$sub['name_en']}}/?type=news">{{$sub['name']}}</a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="news_list_con">
        @foreach($page as $article)
            @include('mobile.articles.v2.news_item_cell', ['article'=>$article])
        @endforeach
    </div>
@endsection
@section('bottom')
    @include("mobile.layout.v2.bottom_cell", ['cur'=>'news'])
@endsection
@section("js")
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/news_list_wap_2.js?time=201903140958"></script>
    <script type="text/javascript">
        var nowPage = 0;
        window.onload = function () {
            setPage()
        }
    </script>
@endsection

