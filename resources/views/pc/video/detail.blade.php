@extends('pc.layout.base')
@section('content')
    <div id="Content">
        <div class="inner">
            <div id="Info">
                <h1 class="name"></h1>
                <p class="line">
                    {{--<button id="{{$channel['id']}}"onclick="ChangeChannel('{{$link}}', this)">{{$channel['title']}}</button>--}}
                </p>
            </div>
            <div class="iframe" id="Video">
                <div class="ADWarm_RU" style="display: none;"><p onclick="document.getElementById('Video').removeChild(this.parentNode)">· 我知道了 ·</p></div>
            </div>
            <div class="share" id="Share">
                复制此地址分享：<input type="text" name="share" value="" onclick="Copy()"><span></span>
            </div>
        </div>
        <div id="Talent" class="tabContent inner" style=""></div>
    </div>
    <div class="clear"></div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/video.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
        }
        function changeShare(link, obj) {
            if (obj.className.indexOf('on') != -1) {
                return;
            }
            $("#Info button").removeClass('on');
            $(obj).addClass('on');
            $("#Share input").val(link);
        }
        $(function () {
            var id = GetQueryString('id');
            var type = GetQueryString('type');
            if (type != 'sv') type = 'hv';
            if (/\d+/.test(id)) {
                var index = Math.floor(id / 10000);
                var url;
                if (type == 'sv') {
                    url = "/live/subject/videos/channel/" + index + "/" + id + '.json';
                } else {
                    url = "/live/videos/channel/" + index + "/" + id + '.json';
                }
                $.ajax({
                    "url": url,
                    "dataType": "json",
                    "success": function (json) {
                        if (json && json.code == 0) {
                            $("#Info h1.name").html(json.lname + "：" + json.hname);
                            var host = window.location.host;
                            if (json.player == 11 || json.playurl.indexOf('player.pptv.com') != -1) {
                                host = 'http://' + host;
                            } else {
                                host = 'https://' + host;
                            }
                            var link = host + '/live/subject/player.html?cid=' + id + '&type=' + type;
                            var btnHtml = '<button id="' + id + '"onclick="ChangeChannel(\'' + link + '\', this)" style="display: none;"></button>';
                            $("#Info p.line").html(btnHtml);
                            LoadVideo();
                        }
                    },
                    "error": function () {
                    }
                });
            }
        });
    </script>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/video.css?t=123">
@endsection