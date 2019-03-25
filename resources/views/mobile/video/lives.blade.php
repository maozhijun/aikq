<?php
    $testM = env("TEST_M", "");
    $cdnUrl = env("CDN_URL");
    $keywords = "爱看球,爱看球录像,中超录像,英超录像,全场回放,高清录像";
    $description = "爱看球录像，为球迷收录各大联赛的精彩录像、高清录像。";
?>
@extends('mobile.layout.v2.base')
@section("body_attr") onscroll="//scrollBottom(loadVideos);" @endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/mobile/v2/video_list_wap_2.css">
@endsection
@section('banner')
    <div id="Navigation">
        <div class="column_con">
            <div class="run_line">
                @foreach($types as $key=>$typeName)
                    <?php
                    $on = ($key == $type || ( isset($stars) && ( ($sport == 2 && $key == "basketballstar") || ($sport == 1 && $key == "footballstar") ) )
                        || (isset($tags) && ( ($sport == 2 && $key == "basketball") || ($sport == 1 && $key == "football") ) ) ) ? "on" : "";
                    ?>
                    <a class="column_item {{$on}}" @if($key == $type) href="#" @else href="{{$testM."/video/".($key == "new" ? "" : $key)}}" @endif forItem="new">{{$typeName}}</a>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="video_list_con {{preg_match('/star/', $type) || is_numeric($type) ? "star" : $type}}" style="display: block;">
        @if(isset($tags) || isset($stars))
        <div class="video_tab_con">
            <p class="run_line">
                @if(isset($tags))
                    <a class="on">全部</a>
                    @foreach($tags as $tag)
                    <a href="{{$testM}}/{{$tag["name_en"]}}">{{$tag["name"]}}</a>
                    @endforeach
                @endif
                @if(isset($stars))
                    <?php $startUrl = $testM."/video/".($sport == 1 ? "footballstar" : "basketballstar"); ?>
                    <a @if(!is_numeric($type)) class="on" href="#" @else href="{{$startUrl}}/" @endif >全部</a>
                    @foreach($stars as $star)
                    <a @if($type == $star["id"]) class="on" @endif href="{{$startUrl."_".$star["id"]}}">{{$star["name"]}}</a>
                    @endforeach
                @endif
            </p>
        </div>
        @endif
        @foreach($videos as $video)
        <div class="video_item">
            <a href="{{$testM}}{{\App\Models\Match\HotVideo::getVideoDetailUrl($video["id"])}}">
                <p class="img_box" style="background-image: url({{$video["cover"]}});"></p>
                <h3>{{$video["title"]}}</h3>
            </a>
        </div>
        @endforeach
    </div>
@endsection

@section('bottom') @include("mobile.layout.v2.bottom_cell", ["cur"=>"video"]) @endsection
@section('js')
    <script type="text/javascript" src="{{$cdnUrl}}/js/mobile/v2/public_wap_2.js?201903250913"></script>
    <script type="text/javascript" src="{{$cdnUrl}}/js/mobile/v2/video_list_wap_2.js"></script>
    <script type="text/javascript">
        var nowPageType = 'new', nownewPage = 0, nowbasketballPage = 0, nowfootballPage = 0, nowstarPage = 0, nowotherPage = 0;
        window.onload = function () {
            setPage()
        }
    </script>
@endsection