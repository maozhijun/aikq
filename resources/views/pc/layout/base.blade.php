<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>爱看球</title>
    <meta name="Keywords" content="免费直播,体育直播,足球直播,NBA直播,JRS,低调看直播,直播吧,CCTV5在线直播,CCTV5+在线直播">
    <meta name="Description" content="爱看球——专注小联赛，专业为球迷和彩民服务的体育直播，赛事最全，线路最多。">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <script type="text/javascript">
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            var url = window.location.href;
            url = url.split('/');
            var str = '';
            for (var i = 0 ; i < url.length ; i++){
                str = str + url[i] + '/';
                if (i == 2){
                    str += 'm/';
                }
            }
            if (url.length == 4){
                str = str.substr(0,str.length - 2);
            }
            else{
                str = str.substr(0,str.length - 1);
            }
            window.location = str;
        }
    </script>
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/style.css?2017012602013">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
</head>
<body>
<div id="Navigation">
    <div class="inner">
        <img class="icon" src="{{env('CDN_URL')}}/img/pc/logo_akq.png">
        <a class="column{{isset($check) && $check == 'all' ? ' on' : ''}}" href="/">全部</a>
        <a class="column{{isset($check) && $check == 'bet' ? ' on' : ''}}" href="/betting.html">竞彩</a>
        <a class="column{{isset($check) && $check == 'foot' ? ' on' : ''}}" href="/football.html">足球</a>
        <a class="column{{isset($check) && $check == 'basket' ? ' on' : ''}}" href="/basketball.html">篮球</a>
        @yield('nav_inner')
    </div>
</div>
@yield('content')
<?php $links = \App\Http\Controllers\PC\Live\LiveController::links(); ?>
<div id="Bottom">
    <p>友情链接：@foreach($links as $link)<a target="_blank" href="{{$link['url']}}">{{$link['name']}}</a>@endforeach </p>
    <p>Copyright 2014-2015 ©aikq.cc, All rights reserved.</p>
    <p>免责声明：本站所有直播和视频链接均由网友提供，如有侵权问题，请及时联系，我们将尽快处理。</p>
</div>
@yield('bottom')
</body>
<script type="text/javascript">
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2b2ed5595f2e5b67b0121a6caa8d1c1a";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
@yield('js')
</html>