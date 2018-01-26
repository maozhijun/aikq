<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>爱看球</title>
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/style.css?time=20180126">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/player.css?time=20180126">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
</head>
<body scroll="no">
<div class="player_content" id="MyFrame">
    {{--<p class="loading"><img src="https://img.liaogou168.com/kqm/file/img/loading.gif">加载中</p>--}}
</div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/ckplayer/ckplayer.js?201801261159"></script>

<script type="text/javascript">
    function ShareWarm (Text) {
        var P = document.createElement('p');
        P.id = 'ShareWarm';
        P.innerHTML = Text;
        document.body.appendChild(P)
    }
    window.host = '{{$_SERVER['HTTP_HOST']}}';
</script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/player.js?time=0126"></script>
<script type="text/javascript">
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?2b2ed5595f2e5b67b0121a6caa8d1c1a";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
    window.onload = function () { //需要添加的监控放在这里
        LoadVideo();
    }
</script>
</html>