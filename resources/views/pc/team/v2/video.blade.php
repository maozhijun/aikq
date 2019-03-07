@extends('pc.team.v2.base')
@section('teamCss')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/video_list_2.css?time=20192191536">
    <style type="text/css">
        .video_con{ padding-left: 10px; }
        .video_con .item_con{ width: 33.333%; }
    </style>
@section('detail')
    <div id="Tab_con">
        <p><a href="/{{$name_en}}/team{{$tid}}_index_1.html">综合</a></p>
        <p><a href="/{{$name_en}}/team{{$tid}}_news_1.html">资讯</a></p>
        <p class="on"><a href="">视频</a></p>
        <p><a href="/{{$name_en}}/team{{$tid}}_record_1.html">录像</a></p>
    </div>
    <div class="video_con">
        @foreach($videos as $video)
            <div class="item_con">
                <a target="_blank" href="{{\App\Models\Match\HotVideo::getVideoDetailUrl($video["id"])}}">
                    <img src="{{$video["image"]}}">
                    <p>{{$video["title"]}}</p>
                </a>
            </div>
        @endforeach
{{--        @if($page > 1)--}}
            @component("pc.layout.v2.page_cell", ['lastPage'=>$page, "curPage"=>$pageNo,'href'=>'/'.$name_en.'/team'.$tid.'_record_']) @endcomponent
        {{--@endif--}}
    </div>
@endsection

@section('right')
    <?php
    $sTitle = "";
    ?>
    <div class="con_box">
        <div class="header_con">
            <h4>{{strlen($sTitle) == 0 ? '最新' : $sTitle}}资讯</h4>
            {{--<a href="{{isset($zhuanti) == 0 ? '/news/':'/'.$zhuanti['name_en'].'/news/'}}">全部{{$sTitle}}资讯</a>--}}
            <a href="/news/">全部资讯</a>
        </div>
        <div class="news">
            @if(isset($comboData) && isset($comboData['articles']))
                @foreach($articles as $index=>$article)
                    @if($index < 2)
                        <a href="{{$article['link']}}" class="img_news">
                            <p class="img_box"><img src="{{$article['cover']}}"></p>
                            <h3>{{$article['title']}}</h3>
                        </a>
                    @else
                        <a href="{{$article['link']}}" class="text_new"><h4>{{$article['title']}}</h4></a>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endsection