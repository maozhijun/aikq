@extends('pc.layout.base')
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env("CDN_URL")}}/css/pc/record.css">
@endsection
@section("content")
<div id="Content">
    <div class="inner">
        <div id="Crumb">
            <a href="/">爱看球</a>
            @if(isset($sl))&nbsp;&nbsp;>&nbsp;&nbsp;<a href="/{{$sl['name_en']}}">{{$sl['name']}}</a>@endif
            &nbsp;&nbsp;>&nbsp;&nbsp;<span class="on">{{$match->getVideoTitle($svc['type']) . ' ' . $svc['title']}}</span>

        </div>
        <div id="Player">
            <h1>{{$match->getVideoTitle($svc['type'], true) . ' ' . $svc['title']}}</h1>
            <a href="{{$svc['content']}}" target="_blank"></a>
            <div class="list">
                <ul>
                    @foreach($allChannels as $ch)
                    <li @if($ch['id'] == $svc['id']) class="on" @endif >
                        <p class="imgbox" style="background: url({{empty($ch['cover']) ? env('CDN_URL').'/img/pc/akq_pc_default_n.jpg' : $ch['cover']}}); background-size: cover;"></p>
                        <a class="con" @if($ch['id'] != $svc['id']) href="video{{$ch['id']}}.html" @endif >{{$ch['title']}}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @if(isset($articles) && count($articles) > 0)
        <div class="right_part">
            <div id="News">
                <p class="title">相关新闻</p>
                @foreach($articles as $index=>$article)
                    @if($index < 3)
                        <a class="big" href="{{$article->url}}" target="_blank">
                            <p class="imgbox" style="background: url({{$article->cover}}); background-size: cover;"></p>
                            <p class="con">{{$article['title']}}</p>
                        </a>
                    @else
                        <a class="small" href="{{$article->url}}" target="_blank">{{$article['title']}}</a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        @if(isset($moreVideos) && count($moreVideos) > 0)
        <div class="left_part">
            <div id="Record">
                <p class="title">更多精彩录像</p>
                @foreach($moreVideos as $mVideo)
                <?php $vTitle = $mVideo->getVideoTitle(); ?>
                <div class="item" title="{{$vTitle}}">
                    <a href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($mVideo['s_lid'], $mVideo['id'], 'video')}}">
                        <p class="imgbox" style="background: url({{empty($mVideo['cover']) ? env('CDN_URL').'/img/pc/akq_pc_default_n.jpg' : $mVideo['cover']}}) center; background-size: cover;"></p>
                        <p class="con">{{$vTitle}}</p>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="clear"></div>
</div>
@endsection

<!-- <div class="adflag left">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div>
<div class="adflag right">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div> -->
</body>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<!-- <script type="text/javascript" src="js/team.js"></script> -->
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        setADClose();
    }
</script>
</html>