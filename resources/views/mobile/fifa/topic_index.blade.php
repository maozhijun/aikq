@extends('mobile.fifa.base')
@section('content')
    @foreach($topics as $topic)
        <a class="li" href="{{'https://shop.liaogou168.com/news/detail/'.$topic['id'].'.html'}}">
            <div class="img" style="background: url(https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike80%2C5%2C5%2C80%2C26/sign=d7f09c74a08b87d6444fa34d6661435d/203fb80e7bec54e7523d345bb8389b504ec26aa5.jpg) no-repeat center; background-size: cover;"></div>
            <p class="title">{{$topic['title']}}</p>
            <p class="time">{{date("Y/m/d", $topic['publishAt'])}}&nbsp;&nbsp;{{date("H:i", $topic['publishAt'])}}</p>
        </a>
    @endforeach
    @component('mobile.fifa.base_bottom',['index'=>3])
    @endcomponent
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/team.js?time=201803030002"></script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/news.css">
@endsection
