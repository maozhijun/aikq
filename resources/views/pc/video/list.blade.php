@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/video_list_2.css">
@endsection
@section('content')
    <div class="def_content">
        <div class="tab_con">
            @foreach($types as $key=>$name)
            <a @if($key == $type) href="#"  class="on" @else href="/video/{{$key == "new" ? "" : $key}}" @endif >{{$name}}</a>
            @endforeach
        </div>
        <div class="video_con">
            <div class="tag_con" style="display: ;">
                <a href="video_league.html">英超视频 ></a>
                <a href="video_league.html">西甲视频 ></a>
                <a href="video_league.html">德甲视频 ></a>
                <a href="video_league.html">意甲视频 ></a>
                <a href="video_league.html">法甲视频 ></a>
                <a href="video_league.html">中超视频 ></a>
                <a href="video_league.html">欧冠视频 ></a>
                <a href="video_league.html">欧联视频 ></a>
            </div>
            <div class="tag_con player_con" style="display: none;">
                <button class="left" disabled></button>
                <button class="right"></button>
                <div class="player_list">
                    <p class="item"><a href="#" class="on">詹姆斯·哈登</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                    <p class="item"><a href="">勒布朗·詹姆斯</a></p>
                </div>
            </div>
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