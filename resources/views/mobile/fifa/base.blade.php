<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta content="telephone=no,email=no" name="format-detection" />
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/style.css">
    <link rel="Shortcut Icon" data-ng-href="img/ico.ico" href="img/ico.ico">
    <link href="img/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
    <script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/public.js?time=201803030002"></script>
    @yield('css')
    <title>世界杯看球助手</title>
</head>
<body>
<div id="Topbar">
    <a href="">直播</a>
    <a href="">录像</a>
    <a href="">推荐</a>
    <a class="on">世界杯</a>
</div>
@yield('content')
</body>
@yield('js')
<script type="text/javascript">
    window.onload = function () {
        setPage();
    }
</script>
</html>