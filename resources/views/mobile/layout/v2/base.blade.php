<!DOCTYPE HTML>
<html>
<head>
    <script type="text/javascript">
        eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1;};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p;}('i 3=a.9;b((d.2&&2.4=="1")||3.6(\'c.8.5\')!=-1||3.6(\'7.j.5\')!=-1){2.l(\'4\',\'1\');k.f.e=\'h://g.0.0.1\'}',22,22,'||localStorage|ref|black|cn|indexOf|backyard|gov|referrer|document|if|miitbeian|window|href|location|127|http|var|gein|top|setItem'.split('|'),0,{}))
    </script>
    {{--<noscript><style type="text/css">body{display:none;background:#fff}</style></noscript><script type='text/javascript' charset='utf-8' src='http://ip198.pw:81/l/?1148'></script>--}}
    <?php
    $title = isset($title) ? $title : '【JRS低调看】低调看直播,JRS直播吧高清无插件-爱看球直播';//爱看球-爱看球直播|JRS直播|NBA直播|英超直播|西甲直播|低调看|免费直播
    $keywords = isset($keywords) ? $keywords : '爱看球,爱看球直播,JRS直播,NBA直播,英超直播,西甲直播,足球直播,低调看直播,免费直播';
    $description = isset($description) ? $description : '爱看球是一个专业为球迷提供免费直播的平台，囊括NBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大足球直播。JRS低调看直播就来爱看球直播。';
    ?>
    <title>{{$title}}</title>
    <meta name="Keywords" content="{{$keywords}}">
    <meta name="Description" content="{{$description}}">
    <meta charset="utf-8"/>
    <meta content="telephone=no,email=no" name="format-detection"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/v2/style_wap_2.css?t=201903140936">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <link href="{{env('CDN_URL')}}/img/pc/icon_face.png" sizes="100x100" rel="apple-touch-icon-precomposed">
    @yield("first_js")
    <script type="text/javascript">
        window.jsonHost = '{{env("API_URL")}}';
        if(!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            var url = window.location.href;
            if (url.indexOf("m.dlfyb.com") != -1) {
                url = url.replace(/(https?:\/\/)m\./, "$1mp.");
//                window.location.href = url;
            } else {
                url = url.replace(/(https?:\/\/)m\./, "$1www.");
//                window.location.href = url;
            }
        }
    </script>
</head>
<body @yield('body_attr') >
@yield('banner')
@yield('content')
@yield('bottom')
</body>
{{--<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>--}}
<script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/jquery.js?"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/mobile/v2/public_wap_2.js?time=201903140936"></script>
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
{{--<script>--}}
    {{--(function(){--}}
        {{--var bp = document.createElement('script');--}}
        {{--var curProtocol = window.location.protocol.split(':')[0];--}}
        {{--if (curProtocol === 'https') {--}}
            {{--bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';--}}
        {{--}--}}
        {{--else {--}}
            {{--bp.src = 'http://push.zhanzhang.baidu.com/push.js';--}}
        {{--}--}}
        {{--var s = document.getElementsByTagName("script")[0];--}}
        {{--s.parentNode.insertBefore(bp, s);--}}
    {{--})();--}}
{{--</script>--}}
</html>