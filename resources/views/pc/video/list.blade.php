@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/video_list_2.css">
@endsection
@section('content')
    <div class="def_content">
        <div class="tab_con">
            @foreach($types as $key=>$name)
            <a @if($key == $type || ( isset($stars) && ( ($sport == 2 && $key == "basketballstar") || ($sport == 1 && $key == "footballstar") ) )
             || (isset($tags) && ( ($sport == 2 && $key == "basketball") || ($sport == 1 && $key == "football") ) ) ) href="#"  class="on" @else href="/video/{{$key == "new" ? "" : $key}}" @endif >{{$name}}</a>
            @endforeach
        </div>
        <div class="video_con">
            @if(isset($tags))
                <div class="tag_con">
                    @foreach($tags as $tag)
                    <a @if($type == $tag["name_en"]) class="on" @endif href="/{{$tag["name_en"]."/video/"}}">{{$tag["name"]}} ></a>
                    @endforeach
                </div>
            @endif
            @if(isset($stars))
            <div class="tag_con player_con">
                <button class="left" disabled></button>
                <button class="right"></button>
                <div class="player_list">
                    @foreach($stars as $star)
                    <p class="item"><a href="/video/{{($sport == 1 ? "footballstar" : "basketballstar") . "_".$star["id"]}}/" @if($star["id"] == $type) class="on" @endif >{{$star["name"]}}</a></p>
                    @endforeach
                </div>
            </div>
            @endif
            @foreach($videos as $video)
            <div class="item_con">
                <a target="_blank" href="{{\App\Models\Match\HotVideo::getVideoDetailUrl($video["id"])}}">
                    <img src="{{$video["cover"]}}">
                    <p>{{$video["title"]}}</p>
                </a>
            </div>
            @endforeach
            @component("pc.video.page_cell", ["page"=>$page, "pageUrl"=>$pageUrl]) @endcomponent
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/video_list_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection