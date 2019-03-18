<?php
    $testM = env("TEST_M", "");
    $cdnUrl = env("CDN_URL");
    $keywords = "爱看球,爱看球录像,中超录像,英超录像,全场回放,高清录像";
    $description = "爱看球录像，为球迷收录各大联赛的精彩录像、高清录像。";
?>
@extends('mobile.layout.v2.base')
@section("body_attr") onscroll="//scrollBottom(loadVideos);" @endsection
@section('title')
    <title>爱看球-爱看球录像|NBA录像|英超录像|意甲录像|西甲录像|全场回放|高清录像</title>
@endsection
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
    <div class="video_list_con new" style="display: block;">
        @if(isset($tags) || isset($stars))
        <div class="video_tab_con">
            <p class="run_line">
                @if(isset($tags))
                    <a class="on">全部</a>
                    @foreach($tags as $tag)
                    <a href="league.html?type=video">{{$tag["name"]}}</a>
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
    <div class="video_list_con basketball" style="display: none">
        <div class="video_tab_con">
            <p class="run_line">
                <a class="on">全部</a>
                <a href="league.html?type=video">NBA</a>
                <a href="league.html?type=video">CBA</a>
            </p>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
    </div>
    <div class="video_list_con football" style="display: none">
        <div class="video_tab_con">
            <p class="run_line">
                <a class="on">全部</a>
                <a href="league.html?type=video">英超</a>
                <a href="league.html?type=video">西甲</a>
                <a href="league.html?type=video">德甲</a>
                <a href="league.html?type=video">法甲</a>
                <a href="league.html?type=video">意甲</a>
                <a href="league.html?type=video">欧冠</a>
                <a href="league.html?type=video">欧联</a>
            </p>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
    </div>
    <div class="video_list_con star" style="display: none;">
        <div class="video_tab_con">
            <p class="run_line">
                <a class="on" forItem="0">全部</a>
                <a href="javascript:void(0)" forItem="1">詹姆斯·哈登</a>
                <a href="javascript:void(0)" forItem="2">科比·布莱恩特</a>
                <a href="javascript:void(0)" forItem="3">勒布朗·詹姆斯</a>
                <a href="javascript:void(0)" forItem="4">詹姆斯·哈登</a>
                <a href="javascript:void(0)" forItem="5">科比·布莱恩特</a>
                <a href="javascript:void(0)" forItem="6">勒布朗·詹姆斯</a>
                <a href="javascript:void(0)" forItem="7">詹姆斯·哈登</a>
                <a href="javascript:void(0)" forItem="8">科比·布莱恩特</a>
                <a href="javascript:void(0)" forItem="9">勒布朗·詹姆斯</a>
                <a href="javascript:void(0)" forItem="10">詹姆斯·哈登</a>
                <a href="javascript:void(0)" forItem="11">科比·布莱恩特</a>
                <a href="javascript:void(0)" forItem="12">勒布朗·詹姆斯</a>
            </p>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
    </div>
    <div class="video_list_con other" style="display: none">
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
        <div class="video_item">
            <a href="video.html">
                <p class="img_box" style="background-image: url(http://img1.gtimg.com/ninja/2/2019/02/ninja155115170749940.jpg);"></p>
                <h3>勒布朗该为湖人的低迷负责?</h3>
            </a>
        </div>
    </div>
@endsection

@section('bottom') @include("mobile.layout.v2.bottom_cell", ["cur"=>"video"]) @endsection
@section('js')
    <script type="text/javascript" src="{{$cdnUrl}}/js/mobile/v2/public_wap_2.js"></script>
    <script type="text/javascript" src="{{$cdnUrl}}/js/mobile/v2/video_list_wap_2.js"></script>
    <script type="text/javascript">
        var nowPageType = 'new', nownewPage = 0, nowbasketballPage = 0, nowfootballPage = 0, nowstarPage = 0, nowotherPage = 0;
        window.onload = function () {
            setPage()
        }
    </script>
@endsection