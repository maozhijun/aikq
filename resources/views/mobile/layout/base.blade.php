<!DOCTYPE HTML>
<html>
<head>
    @yield("first_js")
    <script type="text/javascript">
        if(!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
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
    @yield('title')
    <meta name="Keywords" content="JRS,JRS直播,NBA直播,NBA录像,CBA直播,英超直播,西甲直播,足球直播,篮球直播,低调看,直播吧,CCTV5在线,CCTV5+">
    <meta name="Description" content="爱看球是一个专业为球迷提供免费的NBA,CBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大体育赛事直播、解说平台，无广告，无插件，高清，直播线路多">
    <meta charset="utf-8"/>
    <meta content="telephone=no,email=no" name="format-detection"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/style_phone.css?rd=201802">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <link href="/img/pc/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
</head>
<body @yield('body_attr') >
@yield('banner')
@yield('content')
@yield('bottom')
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
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
<script>
    (function(){
        var bp = document.createElement('script');
        var curProtocol = window.location.protocol.split(':')[0];
        if (curProtocol === 'https') {
            bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
        }
        else {
            bp.src = 'http://push.zhanzhang.baidu.com/push.js';
        }
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(bp, s);
    })();
</script>
</html>