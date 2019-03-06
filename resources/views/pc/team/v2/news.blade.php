@extends('pc.team.v2.base')
@section('teamCss')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/news_list_2.css?time=20192191536">
    @endsection
@section('detail')
    <div id="Tab_con">
        <p><a href="/{{$name_en}}/team{{$tid}}_index_1.html">综合</a></p>
        <p class="on"><a href="#">资讯</a></p>
        <p><a href="/{{$zhuanti['name_en']}}/team{{$tid}}_video_1.html">视频</a></p>
        <p><a href="/{{$zhuanti['name_en']}}/team{{$tid}}_record_1.html">录像</a></p>
    </div>
    <div class="list_con">
        @foreach($articles as $article)
            <?php
            $tags = \App\Models\Tag\TagRelation::getTagWithSids(\App\Models\Tag\TagRelation::kTypeArticle,$article['id']);
            ?>
            <a class="news_list_box" href="{{$article['url']}}">
                <p class="img_box"><img src="{{$article['cover']}}"></p>
                <h2>{{$article['title']}}</h2>
                <p class="summary_text">{{$article['digest']}}</p>
                <div class="info_text">
                    {{date('Y-m-d', date_create($article['publish_at'])->getTimestamp())}}
                    @if(count($tags) > 0)
                    <p class="tag">
                        @foreach($tags as $key=>$tag)
                            <span>{{$tag['name']}}</span>
                        @endforeach
                    </p>
                        @endif
                </div>
            </a>
        @endforeach
        {{--@if($page > 1)--}}
            @component("pc.layout.v2.page_cell", ['lastPage'=>$page, "curPage"=>$pageNo,'href'=>'/'.$name_en.'/team'.$tid.'_news_']) @endcomponent
        {{--@endif--}}
    </div>
@endsection
@section('right')
    <?php
    $sTitle = $zhuanti['name'];
    ?>
    <div class="con_box">
        <div class="header_con">
            <h4>最新{{$sTitle}}视频</h4>
            <a href="{{is_null($zhuanti) ? '/video/':'/'.$zhuanti['name_en'].'/video/'}}">{{$sTitle}}视频集锦</a>
            <a href="{{is_null($zhuanti) ? '/record/':'/'.$zhuanti['name_en'].'/record/'}}">{{$sTitle}}比赛录像</a>
        </div>
        <div class="video">
            @if(isset($videos))
                @foreach($videos as $video)
                    <div class="video_item">
                        <a href="{{$video['link']}}">
                            <p class="img_box"><img src="{{$video['image']}}"></p>
                            <p class="text_box">{{$video['title']}}</p>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection