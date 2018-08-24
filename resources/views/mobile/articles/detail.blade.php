@extends('mobile.layout.base')
@section('title')
    <title>{{$title}}</title>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/articlePhone.css">
@endsection
@section('banner')
    <div id="Navigation">
        @if(isset($h1))
        <h1>{{$h1}}</h1>
        @endif
        <div class="banner">
            <a class="home" href="/"></a>
           <img src="{{env('CDN_URL')}}/img/mobile/image_slogan_nav.png">
        </div>
    </div>
@endsection
@section('content')
    <div id="BG" style="background: url({{empty($article->cover) ? env('CDN_URL').'/img/mobile/image_bg.jpg' : $article->getCover()}}) no-repeat center top; background-size: cover;"></div>
    <h1>{{$article->title}}</h1>
    <div id="Introduction">
        <div class="modify"></div>
        <p>{{$article->digest}}</p>
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

