<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta content="telephone=no,email=no" name="format-detection" />
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/transfer/css/style.css?time=201808100002">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    {{--<link href="img/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">--}}
    <script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/jquery.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/public.js"></script>
    <title>夏季转会风云榜</title>
</head>
<body>
    <!--开始-->
    <img src="{{env('CDN_URL')}}/transfer/img/image_title_n.png" class="title opacity">
    <input type="text" name="name" placeholder="输入名字,申请转会" maxlength="20" class="opacity">
    <button id="Apply" class="opacity"></button>
    <a href="/act/transfer/rank.html" class="rank opacity">身价排行榜</a>

    <!--loading-->
    <div id="Loading" class="hidden">
        <p class="title">转会洽谈中</p>
        <div class="line"><p style="width: 0%;"></p></div>
    </div>

    <!--结果-->
    <div class="welcome opacity"></div>
    <div id="Result" class="transform">
        <div class="inner">
            <div class="info">
                <div>
                    <p class="name">-</p>
                    <p class="cost">-</p>
                    <p class="club">-</p>
                </div>
            </div>
            <div class="content">
                <p>-</p>
                <div class="money">领取签字费</div>
            </div>
        </div>
        <button id="Again" class="opacity" style="display: none;"></button>
        <button id="Wanna" class="opacity" style="display: none;"></button>
        <a href="/act/transfer/rank.html" class="rank opacity">身价排行榜</a>
    </div>

    <!--签字费-->
    <div id="Money" class="hidden">
        <div class="inner hid">
            <button></button>
            <img src="{{env('CDN_URL')}}/transfer/img/wx_code.jpg">
            <p><span>扫码添加 kanqiu818 微信</span>领取您的签字费</p>
        </div>
    </div>

    <!--广告-->
    <a href="http://mp.dlfyb.com/downloadPhone.html" class="banner"><img src="{{env('CDN_URL')}}/transfer/img/image_banner_n.jpg"></a>
</body>
<script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/common.js?time=20000002"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/home.js?time=2000000123"></script>
<script type="text/javascript">
    window.onload = function () {
        setPage();
    }
</script>
</html>

