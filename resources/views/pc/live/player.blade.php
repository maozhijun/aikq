<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer">
    <title>爱看球</title>
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/css/pc/style.css?rd=2018">
    <link rel="stylesheet" type="text/css" href="{{$cdn}}/css/pc/player.css?rd=20180306">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{$cdn}}/img/pc/ico.ico" href="{{$cdn}}/img/pc/ico.ico">
</head>
<body scroll="no">
<div class="player_content" id="MyFrame">
    <p class="noframe" style="display: none;">距离比赛还有 <b>08:23</b><img class="code" src="/img/pc/code.jpg">加微信 <b>fs188fs</b><br/>与球迷赛事交流，乐享高清精彩赛事！</p>
</div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js"></script>
<script type="text/javascript" src="{{$cdn}}/js/public/pc/ckplayer/ckplayer.js"></script>

<script type="text/javascript">
    function isMobileWithJS() {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1; //android终端或者uc浏览器
        var isiPhone = u.indexOf('iPhone') > -1; //是否为iPhone或者QQ HD浏览器
        var isiPad = u.indexOf('iPad') > -1; //是否iPad
        return (isAndroid || isiPhone || isiPad) ? '1' : '';
    }
</script>

<script type="text/javascript">
    <?php $cdn = ''; $host = "user.liaogou168.com:9090"; ?>
    function ShareWarm (Text) {
        var P = document.createElement('p');
        P.id = 'ShareWarm';
        P.innerHTML = Text;
        document.body.appendChild(P)
    }
    window.host = '{{$host}}';
{{--    window.isMobile = '{{\App\Http\Controllers\Controller::isMobileUAgent($_SERVER['HTTP_USER_AGENT'])}}';--}}
            window.isMobile = isMobileWithJS();
    window.cdn_url = '{{$cdn}}';
    if (window.cdn_url && window.cdn_url != "") {
        window.cdn_url = (location.href.indexOf('https://') != -1 ? 'https:' : 'http:') + window.cdn_url;
    }
    //window.CKHead = (location.href.indexOf('https://') != -1 ? 'https:' : 'http:') + '{{$cdn}}/js/public/pc/ckplayer/';
</script>
<script type="text/javascript" src="{{$cdn}}/js/public/pc/player.js?rd=20180306"></script>
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