<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta content="telephone=no,email=no" name="format-detection" />
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/transfer/css/style.css?time=201808100003">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/transfer/css/rank.css?time=201808100002">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    {{--<link href="img/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">--}}
    <script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/jquery.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/public.js"></script>
    <title>夏季转会风云榜</title>
</head>
<body>
    <ul>
        @foreach($rankArray as $rank)
        <li>
            <p class="number">{{$rank['number']}}</p>
            <div class="info">
                <p class="name">{{$rank['name']}}</p>
                <p class="club">加盟：{{$rank['club']}}</p>
            </div>
            <p class="money">{{$rank['money']}}万</p>
        </li>
        @endforeach
    </ul>
    <!--广告-->
    <a href="http://mp.dlfyb.com/downloadPhone.html" class="banner"><img src="{{env('CDN_URL')}}/transfer/img/image_banner_n.jpg"></a>
</body>
<script type="text/javascript" src="{{env('CDN_URL')}}/transfer/js/common.js?time=20000006"></script>
<script type="text/javascript">
    window.onload = function () {
        customShare('英雄莫问出处，总得有个去处。转会窗口，容许我跳！个！槽！', '国际足坛夏季转会风云榜', 'http://mp.dlfyb.com/act/transfer.html', '', '');
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

