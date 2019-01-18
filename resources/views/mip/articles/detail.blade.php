@extends('mip.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mip/articlePhone.css?201901181541">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="banner">
            <a class="home" href="{{\App\Http\Controllers\Mip\UrlCommonTool::homeLivesUrl()}}"></a>
            <mip-img height="26" width="75" src="{{env('CDN_URL')}}/img/mip/image_slogan_nav.png"></mip-img>
        </div>
    </div>
@endsection
@section('content')
    <mip-img id="BG" layout="fixed-height" height="320" src="{{empty($article->cover) ? env('CDN_URL').'/img/mip/image_bg.jpg' : $article->getLocalCover()}}"></mip-img>
    <h1>{{$article->title}}</h1>
    <div id="Introduction">
        <div class="modify"></div>
        <p>{{$article->digest}}</p>
    </div>
    <div id="Content">
        {!! $article->content !!}
    </div>
@endsection

