@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('STATIC_URL')}}/css/mip/newsPhone.css">
@endsection
@section('js')
    <script src="https://c.mipcdn.com/static/v1/mip-infinitescroll/mip-infinitescroll.js"></script>
    <script src="https://c.mipcdn.com/static/v1/mip-mustache/mip-mustache.js"></script>
@endsection
@section('banner')
    <div id="Navigation">
        @if(isset($h1))
            <h1>{{$h1}}</h1>
        @else
            <h1>体育新闻资讯</h1>
        @endif
        <div class="banner">
            <mip-img height="26" width="75" src="{{env('STATIC_URL')}}/img/mip//image_slogan_nav.png"></mip-img>
        </div>
    </div>
@endsection
@section('content')
    <mip-infinitescroll id="Content" template="myTemplate" data-src="{{\App\Http\Controllers\Mip\UrlCommonTool::newsForPageUrl("all")}}">
        @foreach($page as $article)
            <a href="{{$article->getUrl()}}" class="li">
                @if(!empty($article->cover))
                    <mip-img height="66" layout="fixed-height" src="{{$article->getLocalCover()}}"></mip-img>
                @endif
                <h6>{{$article->title}}</h6>
                <p class="info">{{date('Y.m.d', strtotime($article->publish_at))}}&nbsp;&nbsp;{{date('H:i', strtotime($article->publish_at))}}</p>
            </a>
        @endforeach
        <script type="application/json">
            {
                "rn": "Infinity",
                "pn": 2,
                "pnName": "page",
                "bufferHeightPx": 40,
                "timeout": 5000,
                "loadingHtml": "更多数据正在路上",
                "loadFailHtml": "数据加载失败啦",
                "loadOverHtml": "没有数据了哦"
            }
        </script>
        <template type="mip-mustache" id="myTemplate">
            <a href="{{"{{"}}url{{"}"}}{{"}"}}" class="li">
                <mip-img height="66" layout="fixed-height" src="{{"{{"}}cover{{"}"}}{{"}"}}"></mip-img>
                <h6>{{"{{"}}title{{"}"}}{{"}"}}</h6>
                <p class="info">{{"{{"}}date{{"}"}}{{"}"}}&nbsp;&nbsp;{{"{{"}}time{{"}"}}{{"}"}}</p>
            </a>
        </template>

        <div class="mip-infinitescroll-results"></div>
        <div class="bg">
            <div class="mip-infinitescroll-loading"></div>
        </div>
    </mip-infinitescroll>
@endsection
@section('bottom')
    @include("mip.layout.bottom_cell", ['cur'=>'news'])
@endsection