@extends('pc.layout.base')
@section('content')
    <div id="Content">
        <div class="inner">
            <div id="Info">
                <p class="name">{{$match['lname']}}{{$type == 'video' ? '录像' : '集锦'}}：
                    {{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;{{isset($match['hscore']) ? $match['hscore'] . '-' . $match['ascore'] : 'VS'}}&nbsp;&nbsp;{{$match['aname']}}</p>@endif
                <p class="line">
                    <?php $channels = $match['channels']; ?>
                    @if(isset($channels))
                        @foreach($channels as $index=>$channel)
                            <?php
                                if ($channel['player'] == 11 || $channel['player'] == 19){
                                    $preUrl = str_replace("https://","http://",env('APP_URL'));
                                } else{
                                    if (isset($channel['link']) && stristr($channel['link'],'player.pptv.com')){
                                        $preUrl = str_replace("https://","http://",env('APP_URL'));
                                    } else{
                                        $preUrl = str_replace("http://","https://",env('APP_URL'));
                                    }
                                }
                                $link = $preUrl.'/live/subject/player.html?cid='.$channel['id'] . '&type=' . $type;
                            ?>
                            <button id="{{$channel['id']}}"onclick="ChangeChannel('{{$link}}', this)">{{$channel['title']}}</button>
                        @endforeach
                    @endif
                </p>
            </div>
            <div class="iframe" id="Video">
                <div class="ADWarm_RU" style="display: none;"><p onclick="document.getElementById('Video').removeChild(this.parentNode)">· 我知道了 ·</p></div>
            </div>
            <div class="share" id="Share">
                复制此地址分享：<input type="text" name="share" value="" onclick="Copy()"><span></span>
            </div>
        </div>
        <div class="adbanner inner"><img src="{{env('CDN_URL')}}/img/pc/banner_pc_868.jpg"><img class="show" src="{{env('CDN_URL')}}/img/pc/image_qr_868.jpg"></div>
        <div id="Talent" class="tabContent inner" style=""></div>
    </div>
    <div class="clear"></div>
    {{--<div class="adflag left">--}}
    {{--<button class="close" onclick="document.body.removeChild(this.parentNode)"></button>--}}
    {{--<a><img src="/img/pc/ad/double.jpg"></a>--}}
    {{--<br>--}}
    {{--<a href="http://91889188.87.cn" target="_blank"><img src="/img/pc/ad_zhong_double.jpg"></a>--}}
    {{--</div>--}}
    {{--<div class="adflag right">--}}
    {{--<button class="close" onclick="document.body.removeChild(this.parentNode)"></button>--}}
    {{--<a href="http://91889188.87.cn" target="_blank"><img src="/img/pc/ad_zhong_double.jpg"></a>--}}
    {{--<br>--}}
    {{--<a><img src="/img/pc/ad/double.jpg"></a>--}}
    {{--</div>--}}
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/video.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            LoadVideo();
        }
        function changeShare(link, obj) {
            if (obj.className.indexOf('on') != -1) {
                return;
            }
            $("#Info button").removeClass('on');
            $(obj).addClass('on');
            $("#Share input").val(link);
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/video.css?time=2018012611">
@endsection