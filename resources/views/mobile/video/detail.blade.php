<?php $cdnUrl = env("CDN_URL", ""); $cur="video";$testM = env("TEST_M"); ?>
@extends('mobile.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/mobile/v2/video_list_wap_2.css">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/mobile/v2/video_wap_2.css">
@endsection
@section('content')
    @include("mobile.layout.v2.top_nav_cell")
    <h1>{{$video["title"]}}</h1>
    @if($video["player"] == 16)
        <a href="{{$video["link"]}}" class="play_con" style="background-image: url({{$video["image"]}});"></a>
    @else
        <?php
            if ($video["player"] == 11) {
                $iLink = $video["link"];
            } else {
                $iLink = env("WWW_URL")."/video/player.html?id=" . $video["id"];
            }
        ?>
        <iframe src="{{$iLink}}" class="play_con" style="background-image: url({{$video["image"]}});"></iframe>
    @endif
    @if(isset($comboData["videos"]))
    <p class="title_con">其他{{isset($def) ? $def["name"] : ""}}精彩视频</p>
    <div class="video_list_con">
        @foreach($comboData["videos"] as $mVideo)
        @continue($mVideo['id'] == $video['id'])
        <div class="video_item">
            <a href="{{$testM}}{{$mVideo["link"]}}">
                <p class="img_box" style="background-image: url({{$mVideo["image"]}});"></p>
                <h3>{{$mVideo["title"]}}</h3>
            </a>
        </div>
        @endforeach
    </div>
    @endif
@endsection
@section('js')
    <script type="text/javascript" src="{{$cdnUrl}}/js/mobile/v2/public_wap_2.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            // setPage()
        }
    </script>
@endsection