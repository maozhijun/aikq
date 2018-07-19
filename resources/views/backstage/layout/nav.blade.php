<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>爱看球主播后台</title>
    <link rel="stylesheet" type="text/css" href="/backstage/css/style.css">
    @yield("css")
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="/img/pc/ico.ico" href="/img/pc/ico.ico">
</head>
<body>
<div id="Navigation">
    <div class="inner">
        <a href="/bs/logout">退出登录</a>
        <a href="/bs/password/edit">修改密码</a>
    </div>
</div>
    @yield("content")
</body>
<script type="text/javascript" src="/backstage/js/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="/backstage/js/jquery_191.js"></script>
<![endif]-->
<!-- <script type="text/javascript" src="js/home.js"></script> -->
@yield("js")
</html>