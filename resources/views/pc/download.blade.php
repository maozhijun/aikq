<?php $cdnUrl = env("CDN_URL", ""); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>爱看球APP下载_爱看球官网软件下载-爱看球直播</title>
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/style_2.css?t={{time()}}">
    <link rel="stylesheet" type="text/css" href="{{$cdnUrl}}/css/pc/v2/download.css?t={{time()}}">
    <meta name="Keywords" content="爱看球,app下载">
    <meta name="Description" content="爱看球手机APP,足球、NBA、电竞赛事免费看。">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta http-equiv="mobile-agent" content="format=xhtml; url={{env('M_URL')}}/download/index.html">
    <meta http-equiv="mobile-agent" content="format=html5; url={{env('M_URL')}}/download/index.html">
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
</head>
<script type="text/javascript">
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    if (isAndroid || isiOS){
        var url = window.location.href;
        url = url.replace(/(https?:\/\/)(www\.)?/, "$1m.");
        window.location.href = url;
    }
</script>
<body>
<!-- <div class="adbanner inner"><a href=""><img src="http://img2.titan007.com/image/56456gjf5.gif"></a></div> -->
<div id="Navigation"><h1>爱看球APP下载_爱看球官网软件下载-爱看球直播</h1>
    <div class="header">
        <h1>爱看球APP下载</h1>
        <div class="def_content">
            <a href="/" class="home"></a>
            <div class="item_content">
                <a class="column" href="/">首页</a>
                <a class="column" href="/live/">直播</a>
                <a class="column" href="/news/">资讯</a>
                <a class="column" href="/video/">视频</a>
                <a class="column" href="/record/">录像</a>
                <a class="column" href="/data/">数据</a>
                <a class="column on" href="/download/index.html" target="">下载</a>
                @yield('nav_inner')
            </div>
        </div>
    </div>
    <?php
    if (!isset($subjects)) $subjects = \App\Http\Controllers\PC\Live\SubjectController::getSubjects();?>
    @if(isset($subjects) && count($subjects) > 0)
        <div class="league">
            @foreach($subjects as $id=>$su_obj) <a href="/{{$su_obj['name_en']}}/">{{$su_obj['name']}}</a> @endforeach
        </div>
    @endif
</div>
<div id="Content">
    <div class="aboxF">
        <img src="{{env('CDN_URL')}}/img/pc/image_bg_top.jpg" class="bg">
        <div class="abox">
            <div class="link ios"><p>ios下载</p></div>
            <div class="link android"><p>安卓下载</p></div>
        </div>
        <p class="warm">※安装APP后若无法更新版本，请先卸载再重新安装</p>
    </div>
    <div id="Teach">
        <div class="title"><p><span class="b">安装教学</span></p></div>
        <div class="con">因为这类软体无法通过Apple/Google审核于AppStore/Google play上架，因此需要透过特殊的方式下载并安装，请各位客官放心安装！</div>
        <div class="title"><p><span class="b">I</span><span class="s">os</span>如何安装</p></div>
        <div class="con ios">
            <p>1.APP下载完成后，请开启【设定】>点选【通用】或【一般】</p>
            <p>2.点选【描述档】或【描述档与装置管理】或【设备管理】</p>
            <p>3.点入【企业级应用】的选项</p>
            <p>4.按下【信任FRESH FLOIST AND GIFTSHO P】</p>
            <p>5.按下【信任】APP即完成设定</p>
            <p>6.点击开启【爱看球 App】，开始享用！</p>
        </div>
        <div class="title"><p><span class="b">A</span><span class="s">ndroid</span>如何安装</p></div>
        <div class="con android">
            <p>1.下载前请先至【设定】> [安全性] > 将[未知的来源] 打勾（务必请打勾）</p>
            <p>2.点击【下载按钮】或扫描二维码下载爱看球 apk并同意安装即可</p>
        </div>
    </div>
    <img src="{{env('CDN_URL')}}/img/pc/image_bg_down.jpg" class="bg">
</div>
<div id="Cover" style="display: none;">
    <div class="inner">
        <p class="title">请使用手机安装程式</p>
        <p><img src="{{env('CDN_URL')}}/img/pc/download_code.png"></p>
        <div class="con">
            <span>安装提醒：</span>
            <p>1.Android手机下载前请先至「设定 > 安全性」中将「未知的来源」打勾</p>
            <p>2.IOS手机下载后请至「设定 > 一般 > 描述档或装置管理」中, 点击信任「企业级APP」</p>
            <p>3.如使用微信扫描码，扫码完成后，按右上角选择在浏览器中打开</p>
        </div>
    </div>
</div>
</body>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        $('.link').click(function () {
            // var that = $(this);
            $('#Cover').css('display', '');
            $("body,html").animate({
                scrollTop: $('#Teach .' + ($(this).hasClass('ios') ? 'ios' : 'android')).offset().top - 160 //让body的scrollTop等于pos的top，就实现了滚动
            },0);
        })

        $('#Cover').click(function () {
            $('#Cover').css('display', 'none');
        })
    }
</script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</html>