@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/recording.css">
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            <div class="moreVideo default">
                <div class="labelbox">
                    @foreach($types as $tid=>$t_name) <button id="{{$tid}}" class="{{$tid == $type ? 'on' : ''}}">{{$t_name}}</button> @endforeach
                </div>
                @if(isset($videos) && count($videos) > 0)
                <div class="list">
                    @foreach($videos as $video) <a class="big" href=""><img src="{{$video['cover']}}"><p>{{$video['hname']}}</p></a> @endforeach
                    <div class="clear"></div>
                </div>
                @endif
                <div id="Page" curPage="{{$page['curPage']}}" lastPage="{{$page['lastPage']}}" style="display: none;"></div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/home.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/recording.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            createPageHtml('Page');
            bindPageA("Page");
        }
    </script>
@endsection