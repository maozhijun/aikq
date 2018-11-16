<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>直播终端_免费高清直播_爱看球</title>
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/player.css?time=201808201153">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <script type="text/javascript">
        window.jsonHost = '{{env("API_URL")}}';
    </script>
</head>
<body scroll="no">
<div class="line channel" style="display: none">
    @if(isset($channels))
        @foreach($channels as $index=>$channel)
            @continue($channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink)
            <?php
            $player = $channel['player'];
            if ($player == 11) {
                $link = '/live/iframe/player-'.$channel['id'].'-'.$channel['type'].'.html';
            } else {
                $link = '/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
            }
            //$link = '/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
            if (!empty($channel['link']) && stristr($channel['link'],'leqiuba.cc'))
                $link = '';
            ?>
            @if(strlen($link) > 0)
                <button id="{{$channel['channelId']}}"onclick="ChangeChannel('{{$link}}', this)">{{$channel['name']}}</button>
            @endif
        @endforeach
        <button><a href="/" target="_blank" style="text-decoration:none;color: #fff;">更多直播</a></button>
    @endif
</div>
<div class="line mchannel" style="display: none">
    @if(isset($mchannels))
        @foreach($mchannels as $index=>$channel)
            @continue($channel['player'] == \App\Models\Match\MatchLiveChannel::kPlayerExLink || $channel['platform'] == \App\Models\Match\MatchLiveChannel::kPlatformApp)
            <?php
            $link = '/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
            if (!empty($channel['link']) && stristr($channel['link'], 'leqiuba.cc'))
                $link = '';
            ?>
            @if(strlen($link) > 0)
                <button id="{{$channel['channelId']}}"onclick="ChangeChannel('{{$link}}', this)">{{$channel['name']}}</button>
            @endif
        @endforeach
        <button><a href="/" target="_blank" style="text-decoration:none;color: #fff;">更多直播</a></button>
    @endif
</div>
<div id="Framebox">
<iframe width="100%" height="100%" id="MyFrame">
</iframe>
</div>
<div class="publicAd" style="position: fixed;bottom: 0;left: 0;right: 0;"><button onclick="closeAD(this)" style="width: 50px; height: 50px; background: url(/img/mobile/icon_close_btn_white.png) no-repeat center rgba(0,0,0,0.3); background-size: 24px;; position: absolute; right: 0; top: 0;"></button>
    <a target="_top" href="javascript:log(0)"><img id="download_img" src="{{env('CDN_URL')}}/img/pc/image_ad_wap2.jpg" width="100%"></a>
</div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
@if(!isset($channels) || !isset($mchannels) || count($channels) == 0 || count($mchannels) == 0)
    <script type="text/javascript">
        var href = window.location.href;
        var iframeSrc = href.replace("spPlayer", "iframe");
        console.log(href, iframeSrc);
        document.getElementById('MyFrame').src = iframeSrc;
    </script>
@endif
<script type="text/javascript">
    function closeAD(button) {
        $(button).parent().remove();
    }
    //获取是S还是非S
    function GetHttp () {
        if (location.href.indexOf('https://') != -1) {
            return 'https://';
        }else{
            return 'http://';
        }
    }
    //通过播放地址判断使用http头
    function CheckHttp (Link) {
        if (Link.indexOf('.flv') != -1 || Link.indexOf('rtmp://') == 0 || Link.indexOf('.m3u8') != -1) { //播放方式为播放器播放
            return 'https://';
        }else{
            return 'http://';
        }
    }

    function isMobileWithJS() {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1; //android终端或者uc浏览器
        var isiPhone = u.indexOf('iPhone') > -1; //是否为iPhone或者QQ HD浏览器
        var isiPad = u.indexOf('iPad') > -1; //是否iPad
        return (isAndroid || isiPhone || isiPad) ? '1' : '';
    }

    if (isMobileWithJS()){
        $('#download_img').attr('src','{{env('CDN_URL')}}/img/mobile/image_ad_wap2.jpg');
    }

    //统计
    function log() {
        _hmt.push(['_trackEvent', 'gotoIndex', '0822']);
        top.location.href = 'https://www.aikanqiu.com';
        return;
    }

    window.onload = function () { //需要添加的监控放在这里
        if (isMobileWithJS()){
            $('div.mchannel')[0].style.display = '';
            if ($('.mchannel button').length > 0)
                $($('.mchannel button')[0]).trigger("click");
        }
        else {
            $('div.channel')[0].style.display = '';
            if ($('.channel button').length > 0)
                $($('.channel button')[0]).trigger("click");
        }
    }

    function ChangeChannel (Link,obj) {
        if (obj.className.indexOf('on') != -1) {
            return;
        }
        var MatchID = location.href.split('/')[location.href.split('/').length -1].split('.html')[0];
        var Btn = $('.line button');
        for (var i = 0; i < Btn.length; i++) {
            if (obj == Btn[i]) {
                obj.className = 'on';
                BtnNum = i;
            }else{
                Btn[i].className = '';
            }
        }

        var Target = {
            'id': MatchID,
            'btn': BtnNum
        }

        document.getElementById('MyFrame').src = Link;
    }
</script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?2966b2031ac2b01631362b1474d7f853";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
        _hmt.push(['_trackEvent', 'from', '0822']);
    })();
</script>
</html>