<!DOCTYPE HTML>
<html>
<head>
    @yield('title')
    <meta name="Keywords" content="免费直播,体育直播,足球直播,NBA直播,JRS,低调看直播,直播吧,CCTV5在线直播,CCTV5+在线直播">
    <meta name="Description" content="爱看球——专注小联赛，专业为球迷和彩民服务的体育直播，赛事最全，线路最多。">
    <meta charset="utf-8"/>
    <meta content="telephone=no,email=no" name="format-detection"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/style_phone.css?t=20171215">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <link href="/img/pc/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
    <script type="text/javascript">
        if(!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            var url = window.location.href;
            url = url.split('/');
            var str = '';
            if (url[3] == 'm'){
                for (var i = 0 ; i < url.length ; i++){
                    if (i == 3){
                        continue;
                    }
                    str = str + url[i] + '/';
                }
                str = str.substr(0,str.length - 1);
                window.location = str;
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
@yield('js')
<script type="text/javascript">
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2b2ed5595f2e5b67b0121a6caa8d1c1a";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</html>