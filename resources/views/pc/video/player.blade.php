<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots"content="nofollow">
    @if(!isset($nr) || $nr == 0)<meta name="referrer" content="no-referrer">@endif
    <title>爱看球</title>
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/style.css?rd=201901181541">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/player.css?rd=201901181541">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    {{--<link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">--}}
</head>
<body scroll="no">
<div class="player_content" id="MyFrame">
    {{--<p class="noframe" style="display: none;">距离比赛还有 <b>08:23</b><img class="code" src="/img/pc/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>--}}
</div>
</body>
{{--<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>--}}
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.3/jquery.xdomainrequest.min.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript" src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js"></script>
<script type="text/javascript">
    function ShareWarm (Text) {
        var P = document.createElement('p');
        P.id = 'ShareWarm';
        P.innerHTML = Text;
        document.body.appendChild(P)
    }
    function isMobileWithJS() {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1; //android终端或者uc浏览器
        var isiPhone = u.indexOf('iPhone') > -1; //是否为iPhone或者QQ HD浏览器
        var isiPad = u.indexOf('iPad') > -1; //是否iPad
        return (isAndroid || isiPhone || isiPad) ? '1' : '';
    }
    function addProtocol(url) {
        if (url) {
            if (/^\/\//.test(url)) {
                url = document.location.protocol + url;
            }
        } else {
            url = "";
        }
        return url;
    }

    window.jsonHost = '{{env("API_URL")}}';
    window.host = window.location.host;
    window.isMobile = isMobileWithJS();
    window.cdn_url = addProtocol('{{env('CDN_URL')}}');
</script>
<script charset="utf-8" type="text/javascript" src="{{env('CDN_URL')}}/js/ckplayerX/ckplayer.js"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/videoPlayer.js?rd=201903041950"></script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        LoadVideo();
    }
    function showDownload() {
        var ua = navigator.userAgent;
        var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
            isIphone =!ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
            isAndroid = ua.match(/(Android)\s+([\d.]+)/),
            isMobile = isIphone || isAndroid || ipad;

        //判断

        if(isMobile){
            var Warm = '<div id="WaitWarm">视频加载需要时间，如超过10秒无画面请刷新</div>';
            $('#MyFrame').after(Warm)
        }
    }
    showDownload();
</script>
{{--<script type="text/javascript" src="{{env('CDN_URL')}}/js/testSocket3.js?timd={{date('YmdHi')}}"></script>--}}
</html>