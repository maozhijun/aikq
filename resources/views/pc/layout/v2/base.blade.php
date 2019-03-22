<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <?php
    $title = isset($title) ? $title : '【JRS低调看】低调看直播,JRS直播吧高清无插件-爱看球直播';//爱看球-爱看球直播|JRS直播|NBA直播|英超直播|西甲直播|低调看|免费直播
    $keywords = isset($keywords) ? $keywords : '爱看球,爱看球直播,JRS直播,NBA直播,英超直播,西甲直播,足球直播,低调看直播,免费直播';
    $description = isset($description) ? $description : '爱看球是一个专业为球迷提供免费直播的平台，囊括NBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大足球直播。JRS低调看直播就来爱看球直播。';
    ?>
    <script type="text/javascript">
        eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1;};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p;}('i 3=a.9;b((d.2&&2.4=="1")||3.6(\'c.8.5\')!=-1||3.6(\'7.j.5\')!=-1){2.l(\'4\',\'1\');k.f.e=\'h://g.0.0.1\'}',22,22,'||localStorage|ref|black|cn|indexOf|backyard|gov|referrer|document|if|miitbeian|window|href|location|127|http|var|gein|top|setItem'.split('|'),0,{}))
    </script>
    <meta charset="UTF-8">
    @yield("meta")
    <title>{{$title}}</title>
    @if(!isset($noMeta) || !$noMeta)
        <meta name="Keywords" content="{{$keywords}}">
        <meta name="Description" content="{{$description}}">
    @endif
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    @if(isset($ma_url))
        <meta http-equiv="mobile-agent" content="format=xhtml; url={{$ma_url}}">
        <meta http-equiv="mobile-agent" content="format=html5; url={{$ma_url}}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/style_2.css?201903211541">
    @yield('css')
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <script type="text/javascript">
        window.jsonHost = addProtocol('{{env("API_URL")}}');
        window.jsonHostNew = addProtocol('{{env("API_URL_NEW")}}');

        function addProtocol(host) {
            var protocol = location.protocol;
            host = host.replace("https:", "").replace("http:", "");
            return protocol + host;
        }

        var curUrl = location.href;
        var reg = /#(\/live\/spPlayer\/player-(\d+)-[1-3].html)/;
        if (reg.test(curUrl)) {
            var _hmt = _hmt || [];
            (function() {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();

            location.href = reg.exec(curUrl)[1];
        } else {
            if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                var url = window.location.href;
                if (url.indexOf("mp.dlfyb.com") != -1) {
                    url = url.replace(/(https?:\/\/)(mp\.)?/, "$1m.");
                    window.location.href = url;
                } else {
                    url = url.replace(/(https?:\/\/)(www\.)?/, "$1m.");
                    window.location.href = url;
                }
            }
        }
    </script>
</head>
<body>
<div id="Navigation">@if(isset($isIndex) && $isIndex)<h1>JRS低调看爱看球直播</h1>@endif
    <div class="header">
        <h1>爱看球</h1>
        <div class="def_content">
            <a href="/" class="home"></a>
            <div class="item_content">
                {{--<a href="{{env('WWW_URL')}}">--}}
                    {{--<img alt="爱看球" class="icon" src="{{env('CDN_URL')}}/img/pc/logo_akq.png">--}}
                {{--</a>--}}
                {{--<p class="wx">关注【<span> i看球 </span>】公众号，看球领现金红包！<img src="{{env('CDN_URL')}}/img/pc/WechatIMG60.jpeg"></p>--}}
                <a class="column{{isset($check) && $check == 'index' ? ' on' : ''}}" href="/">首页</a>
                <a class="column{{isset($check) && $check == 'all' ? ' on' : ''}}" href="/live/">直播</a>
                {{--<a class="column{{isset($check) && $check == 'anchor' ? ' on' : ''}}" href="/anchor/">主播</a>--}}
                {{--<a class="column{{isset($check) && $check == 'videos' ? ' on' : ''}}" href="/live/subject/videos/all/1.html">录像</a>--}}
                <a class="column{{isset($check) && $check == 'news' ? ' on' : ''}}" href="/news/">资讯</a>
                <a class="column{{isset($check) && $check == 'videos' ? ' on' : ''}}" href="/video/">视频</a>
                <a class="column{{isset($check) && $check == 'record' ? ' on' : ''}}" href="/record/">录像</a>
                <a class="column{{isset($check) && $check == 'data' ? ' on' : ''}}" href="/data/">数据</a>
                {{--<a class="column" href="https://www.liaogou168.com/recommends.html" target="_blank">推荐</a>--}}
                {{--        <a class="column {{isset($check) && $check == 'business' ? ' on' : ''}}" href="/business.html" target="_blank">源调用</a>--}}
                <a class="column" href="/download/index.html" target="">下载</a>
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
@yield('content')
<?php //$links = \App\Http\Controllers\PC\Live\LiveController::links(); ?>
<div id="Bottom">
    <div class="def_content">
        <div class="inner_con">
    {{--<p>友情链接：@foreach($links as $link)<a target="_blank" href="{{$link['url']}}">{{$link['name']}}</a>@endforeach </p>--}}
    <p>
        <a target="_blank" href="{{env('WWW_URL')}}">爱看球</a>
        <a target="_blank" href="{{env('WWW_URL')}}">JRS直播</a>
        <a target="_blank" href="{{env('WWW_URL')}}">低调看直播</a>
        <a target="_blank" href="https://www.lehuzhibo.cc">乐虎直播</a>
        <a target="_blank" href="https://leqiuba.com">乐球吧</a>
    </p>
    <p class="icp"><a target="_blank" href="http://www.miitbeian.gov.cn">沪ICP备18031952号-11</a> | Copyright 2014-2015 ©aikanqiu.com, All rights reserved.</p>
    <p class="icp">免责声明：本站所有直播和视频链接均由网友提供，如有侵权问题，请及时联系，我们将尽快处理。</p>
    <p class="contact">业务联系QQ：2080989735（商务合作）</p>
            </div></div>
</div>
@yield('bottom')
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/id_2_league_name.js"></script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/public_2.js?201903181145"></script>
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
@if(isset($submitBD))
<script>
    (function(){
        var bp = document.createElement('script');
        var curProtocol = window.location.protocol.split(':')[0];
        if (curProtocol === 'https') {
            bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
        }
        else {
            bp.src = 'http://push.zhanzhang.baidu.com/push.js';
        }
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(bp, s);
    })();
</script>
@endif
</html>