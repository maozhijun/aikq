<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>爱看球-JRS|JRS直播|NBA直播|NBA录像|CBA直播|英超直播|西甲直播|低调看|直播吧|CCTV5在线</title>
    <meta name="Keywords" content="JRS,JRS直播,NBA直播,NBA录像,CBA直播,英超直播,西甲直播,足球直播,篮球直播,低调看,直播吧,CCTV5在线,CCTV5+">
    <meta name="Description" content="爱看球是一个专业为球迷提供免费的NBA,CBA,英超,西甲,德甲,意甲,法甲,中超,欧冠,世界杯等各大体育赛事直播、解说平台，无广告，无插件，高清，直播线路多">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/player.css?time=201808201153">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico">
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no">
</head>
<body scroll="no">
<div class="player_content" id="MyFrame"></div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer-2.2.0.js"></script>
<script type="text/javascript" src="{{env('CND_URL')}}/js/pc/ckplayer/ckplayer.js" charset="UTF-8"></script>
<script type="text/javascript">
    function PlayVideoShare(CID,sport) {
        $.ajax({
            url:'/match/live/url/match/' + CID+'?sport='+sport,
            type:'GET',
            dataType:'json',
            success:function(data){
                if (data.code == 0){
                    var match = data.match;
                    var show_live = match.show_live;
                    if(match.status == 0 && !show_live)
                    {
                        document.getElementById('MyFrame').innerHTML = p;
                        setInterval(countDown, 1000);
                    }
                    else if(show_live){
                        switch (data.type){
                            case 1:{
                                var ID = data.id;
                                LoadSports365(ID)
                            }
                                break;
                            case 3:{
                                var Frame = document.createElement('iframe');
                                Frame.src = data.playurl;
                                if (data.playurl)
                                    LoadIframe(data.playurl);
                            }
                                break;
                            case 2:{
                                if (data.js) {
                                    eval(data.js);
                                    if (play_url.indexOf('rtmp') != -1) {
                                        LoadRtmp (play_url)
                                    }
                                    else {
                                        LoadCK(play_url);
                                    }
                                }
                            }
                                break;
                            case 4:
                            {
                                if (data.playurl) {
                                    if(data.playurl.indexOf('zb.tc.qq.com') != -1){
                                        GoTcPlayer(data.playurl);
                                    }
                                    else if (data.playurl.indexOf('.m3u8') != -1 || data.playurl.indexOf('.flv') != -1) {
                                        if (data.playurl.indexOf('.flv') != -1){
                                            var playurl = data.playurl;
                                            LoadFlv(playurl);
                                        }
                                        else{
                                            var playurl = encodeURIComponent(data.playurl);
                                            LoadCK(playurl);
                                        }
                                    }else{
                                        LoadIframe(data.playurl);
                                    }
                                }
                            }
                                break;
                            case 5:
                            case 99:{
                                var Link = data.playurl;
                                if (Link.indexOf('qiumigo.com') != -1) {
                                    LoadIframe(Link);
                                }else if (Link.indexOf('player.pptv.com') != -1) {
                                    LoadPPTV(Link)
                                }else if (Link.indexOf('staticlive.douyucdn.cn') != -1 || Link.indexOf('upstatic.qiecdn.com') != -1 || Link.indexOf('liveshare.huya.com') != -1) {
                                    LoadTV(Link)
                                }else if (Link.indexOf('.m3u8') != -1) {
                                    LoadCK (Link)
                                }
                                else if (Link.indexOf('rtmp://') != -1) {
                                    LoadRtmp (Link)
                                } /*else if (Link.indexOf('.flv') != -1){
                                 LoadFlv(Link);
                                 } */else {
                                    LoadIframe(Link)
                                }
                            }
                            break;
                        }
                    }
                }
            },
        })
    }

    function ShareWarm (Text) {
        var P = document.createElement('p');
        P.id = 'ShareWarm';
        P.innerHTML = Text;
        document.body.appendChild(P)
    }
</script>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/player.js"></script>
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        var mid = '{{request('mid')}}';
        var sport = '{{request('sport')}}';
        if (mid && mid != '') {
            PlayVideoShare(mid, sport);
        }
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