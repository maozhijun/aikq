<!DOCTYPE HTML>
<html>
<head>
    <?php
    $title = isset($title) ? $title : '【JRS低调看】低调看直播,JRS直播吧高清无插件-爱看球直播';//爱看球-爱看球直播|JRS直播|NBA直播|英超直播|西甲直播|低调看|免费直播
    $keywords = isset($keywords) ? $keywords : '爱看球,爱看球直播,JRS直播,NBA直播,英超直播,西甲直播,足球直播,低调看直播,免费直播';
    $description = isset($description) ? $description : '爱看球是一个专业为球迷提供免费直播的平台，囊括NBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大足球直播。JRS低调看直播就来爱看球直播。';
    ?>
    <title>{{$title}}</title>
    <meta name="Keywords" content="{{$keywords}}">
    <meta name="Description" content="{{$description}}">
    <meta charset="utf-8"/>
    <meta content="telephone=no,email=no" name="format-detection"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/style_phone.css?t=201808241135">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <link href="{{env('CDN_URL')}}/img/pc/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
    @yield("first_js")
    <script type="text/javascript">
        window.jsonHost = '{{env("API_HOST")}}';
        if(!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            var url = window.location.href;
            if (url.indexOf("m.dlfyb.com") != -1) {
                url = url.replace(/(https?:\/\/)m\./, "$1mp.");
                window.location.href = url;
            } else {
                url = url.replace(/(https?:\/\/)m\./, "$1www.");
                window.location.href = url;
            }
        }
    </script>
</head>
<body @yield('body_attr') >
@yield('banner')
@yield('content')
@yield('bottom')
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/mobile/public.js?time=201808000001"></script>
@yield('js')
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
{{--<script>--}}
    {{--(function(){--}}
        {{--var bp = document.createElement('script');--}}
        {{--var curProtocol = window.location.protocol.split(':')[0];--}}
        {{--if (curProtocol === 'https') {--}}
            {{--bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';--}}
        {{--}--}}
        {{--else {--}}
            {{--bp.src = 'http://push.zhanzhang.baidu.com/push.js';--}}
        {{--}--}}
        {{--var s = document.getElementsByTagName("script")[0];--}}
        {{--s.parentNode.insertBefore(bp, s);--}}
    {{--})();--}}
{{--</script>--}}
</html>