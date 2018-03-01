<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer">
    <title>爱看球</title>
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/style.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/player.css">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
</head>
<body scroll="no">
<div class="player_content" id="MyFrame">
    <p class="noframe" style="display: none;">距离比赛还有 <b>08:23</b><img class="code" src="/img/pc/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>
</div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/ckplayer/ckplayer.js"></script>

<script type="text/javascript">
    function ShareWarm (Text) {
        var P = document.createElement('p');
        P.id = 'ShareWarm';
        P.innerHTML = Text;
        document.body.appendChild(P)
    }
    window.host = '{{$_SERVER['HTTP_HOST']}}';
    window.isMobile = '{{\App\Http\Controllers\Controller::isMobileUAgent($_SERVER['HTTP_USER_AGENT'])}}';
    window.cdn_url = '{{env('CDN_URL')}}';
    if (window.cdn_url && window.cdn_url != "") {
        window.cdn_url = (location.href.indexOf('https://') != -1 ? 'https:' : 'http:') + window.cdn_url;
    }
    //window.CKHead = (location.href.indexOf('https://') != -1 ? 'https:' : 'http:') + '{{env('CDN_URL')}}/js/public/pc/ckplayer/';
</script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/player.js?rd=2018030111"></script>
<script>
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    });
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
</script>
</html>