<!DOCTYPE HTML>
<html mip>
<head>
    <?php
    $title = isset($title) ? $title : '【JRS低调看】低调看直播,JRS直播吧高清无插件-爱看球直播';//爱看球-爱看球直播|JRS直播|NBA直播|英超直播|西甲直播|低调看|免费直播
    $keywords = isset($keywords) ? $keywords : '爱看球,爱看球直播,JRS直播,NBA直播,英超直播,西甲直播,足球直播,低调看直播,免费直播';
    $description = isset($description) ? $description : '爱看球是一个专业为球迷提供免费直播的平台，囊括NBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大足球直播。JRS低调看直播就来爱看球直播。';
    ?>
    <title>{{$title}}</title>
    <meta name="Keywords" content="{{$keywords}}">
    <meta name="Description" content="{{$description}}">
    <meta charset="utf-8">
    <meta content="telephone=no,email=no" name="format-detection" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="canonical" href="{{isset($canonical) ? $canonical : \App\Http\Controllers\Mip\UrlCommonTool::getMobileUrl()}}">
    <link rel="stylesheet" type="text/css" href="https://c.mipcdn.com/static/v1/mip.css">
    <link rel="stylesheet" type="text/css" href="{{env('STATIC_URL')}}/css/mip/style_phone.css">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('STATIC_URL')}}/img/mip/ico.ico" href="{{env('STATIC_URL')}}/img/mip/ico.ico">
    <link href="{{env('STATIC_URL')}}/img/mip/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
</head>
<body @yield('body_attr') >
    @yield('banner')
    @yield('content')
    @yield('bottom')
    <mip-stats-baidu>
        <script type="application/json">
            {"token": "2966b2031ac2b01631362b1474d7f853"}
        </script>
    </mip-stats-baidu>
    <script src="https://c.mipcdn.com/static/v1/mip.js"></script>
    @yield('js')
    <script src="https://c.mipcdn.com/static/v1/mip-stats-baidu/mip-stats-baidu.js"></script>
</body>
</html>